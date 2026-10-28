"""Inference helpers shared by the FastAPI app and the CLI fallback."""

from __future__ import annotations

import json
from pathlib import Path
from typing import Any

import joblib
import pandas as pd

from feature_schema import (
    ARTIFACTS_DIR,
    CLASS_LABELS,
    DISPLAY_NAMES,
    FEATURE_COLUMNS,
    GOAL_TO_WORKOUT,
    TRAIN_RANGES,
    USER_FIELD_TO_DISPLAY,
)


def _clamp(value: float, minimum: float, maximum: float) -> float:
    return max(minimum, min(maximum, value))


class GymPredictor:
    """Loads the best model once and exposes a friendly predict method."""

    def __init__(self, artifacts_dir: Path | None = None) -> None:
        self.artifacts_dir = Path(artifacts_dir or ARTIFACTS_DIR)
        self.model_path = self.artifacts_dir / "best_model.joblib"
        self.metrics_path = self.artifacts_dir / "metrics.json"

        if not self.model_path.exists():
            raise FileNotFoundError(
                f"Model belum tersedia di {self.model_path}. Jalankan train.py terlebih dahulu."
            )

        self.pipeline = joblib.load(self.model_path)
        self.metrics = {}
        if self.metrics_path.exists():
            with self.metrics_path.open(encoding="utf-8") as handle:
                self.metrics = json.load(handle)

    @property
    def model_name(self) -> str:
        return self.metrics.get("best_model", {}).get("name", "Random Forest")

    @property
    def model_key(self) -> str:
        return self.metrics.get("best_model", {}).get("key", "random_forest")

    def _build_features(
        self, payload: dict[str, Any]
    ) -> tuple[dict[str, Any], list[str], list[str], str | None, list[dict[str, Any]], float]:
        estimated: list[str] = []
        adjusted: list[str] = []
        notes: str | None = None

        age = float(payload["usia"])
        weight = float(payload["berat"])
        height_cm = float(payload["tinggi"])
        experience = int(payload["pengalaman_bulan"])
        goal = str(payload["tujuan"])

        height_m = height_cm / 100.0
        bmi = weight / (height_m**2)
        bmi_for_model = _clamp(bmi, *TRAIN_RANGES["BMI"])
        if round(bmi_for_model, 2) != round(bmi, 2):
            adjusted.append(DISPLAY_NAMES["BMI"])
            notes = (
                "BMI di luar rentang data latih dijepit agar prediksi tetap stabil; "
                "nilai BMI asli tetap ditampilkan."
            )

        gender = payload.get("gender") or "Male"
        if not payload.get("gender"):
            estimated.append(USER_FIELD_TO_DISPLAY["gender"])

        workout_type = payload.get("workout_type") or GOAL_TO_WORKOUT[goal]
        if not payload.get("workout_type"):
            estimated.append(USER_FIELD_TO_DISPLAY["workout_type"])

        def pick(field: str, default: float, feature: str, decimals: int | None = None) -> float:
            value = payload.get(field)
            if value is None or value == "":
                estimated.append(USER_FIELD_TO_DISPLAY[field])
                value = default
            value = float(value)
            clipped = _clamp(value, *TRAIN_RANGES[feature])
            if round(clipped, 4) != round(value, 4):
                adjusted.append(USER_FIELD_TO_DISPLAY[field])
            if decimals is not None:
                return round(clipped, decimals)
            return clipped

        # Pengalaman gym dipakai untuk mengisi metrik latihan yang tidak
        # diukur pengguna. Threshold-nya mengikuti konvensi umum:
        # < 12 bulan beginner, 12-35 intermediate, >= 36 advanced.
        default_session = 0.75 if experience < 12 else 1.25 if experience < 36 else 1.5
        max_bpm_default = _clamp(220 - age, *TRAIN_RANGES["Max_BPM"])
        avg_bpm_default = _clamp(round(max_bpm_default * 0.80), *TRAIN_RANGES["Avg_BPM"])
        resting_bpm_default = _clamp(round(62 + (age - 38) * 0.12), *TRAIN_RANGES["Resting_BPM"])
        session_default = _clamp(default_session, *TRAIN_RANGES["Session_Duration (hours)"])
        calorie_factor = 1.05 if goal == "cutting" else 1.0
        calories_default = _clamp(
            round(8.8 * weight * session_default * calorie_factor),
            *TRAIN_RANGES["Calories_Burned"],
        )
        sex = 1 if gender == "Male" else 0
        fat_default = _clamp(
            round(1.20 * bmi + 0.23 * age - 10.8 * sex - 5.4, 1),
            *TRAIN_RANGES["Fat_Percentage"],
        )
        water_default = _clamp(
            round(0.035 * weight + 0.4 * session_default, 1),
            *TRAIN_RANGES["Water_Intake (liters)"],
        )
        frequency_default = 2 if experience < 12 else 4 if experience < 36 else 5

        max_bpm = pick("max_bpm", max_bpm_default, "Max_BPM")
        avg_bpm = pick("avg_bpm", avg_bpm_default, "Avg_BPM")
        resting_bpm = pick("resting_bpm", resting_bpm_default, "Resting_BPM")
        session_duration = pick(
            "session_duration", session_default, "Session_Duration (hours)", decimals=2
        )
        calories = pick("calories_burned", calories_default, "Calories_Burned")
        fat_percentage = pick("fat_percentage", fat_default, "Fat_Percentage", decimals=1)
        water_intake = pick("water_intake", water_default, "Water_Intake (liters)", decimals=1)
        frequency = pick(
            "workout_frequency", frequency_default, "Workout_Frequency (days/week)"
        )

        features = {
            "Age": int(round(_clamp(age, *TRAIN_RANGES["Age"]))),
            "Gender": gender,
            "Weight (kg)": round(_clamp(weight, *TRAIN_RANGES["Weight (kg)"]), 1),
            "Height (m)": round(_clamp(height_m, *TRAIN_RANGES["Height (m)"]), 2),
            "Max_BPM": int(round(max_bpm)),
            "Avg_BPM": int(round(avg_bpm)),
            "Resting_BPM": int(round(resting_bpm)),
            "Session_Duration (hours)": round(session_duration, 2),
            "Calories_Burned": round(calories, 1),
            "Workout_Type": workout_type,
            "Fat_Percentage": round(fat_percentage, 1),
            "Water_Intake (liters)": round(water_intake, 1),
            "Workout_Frequency (days/week)": int(round(frequency)),
            "BMI": round(bmi_for_model, 2),
        }

        if int(round(age)) != int(round(_clamp(age, *TRAIN_RANGES["Age"]))):
            adjusted.append(DISPLAY_NAMES["Age"])
        if round(weight, 1) != round(_clamp(weight, *TRAIN_RANGES["Weight (kg)"]), 1):
            adjusted.append(DISPLAY_NAMES["Weight (kg)"])
        if round(height_m, 2) != round(_clamp(height_m, *TRAIN_RANGES["Height (m)"]), 2):
            adjusted.append(DISPLAY_NAMES["Height (m)"])

        display_rows = []
        estimated_set = set(estimated)
        adjusted_set = set(adjusted)
        for feature in FEATURE_COLUMNS:
            label = DISPLAY_NAMES[feature]
            value = features[feature]
            source = "input"
            if label in estimated_set:
                source = "estimasi"
            elif label in adjusted_set:
                source = "disesuaikan"
            elif feature == "BMI":
                source = "turunan"
            display_rows.append(
                {
                    "label": label,
                    "value": value,
                    "source": source,
                }
            )

        return features, estimated, adjusted, notes, display_rows, round(bmi, 2)

    def predict(self, payload: dict[str, Any]) -> dict[str, Any]:
        features, estimated, adjusted, note, display_rows, actual_bmi = self._build_features(payload)
        frame = pd.DataFrame([features], columns=FEATURE_COLUMNS)

        probabilities_raw = self.pipeline.predict_proba(frame)[0]
        classes = [int(value) for value in self.pipeline.classes_]
        probabilities = {
            CLASS_LABELS[class_value]: round(float(probability), 4)
            for class_value, probability in zip(classes, probabilities_raw)
        }

        predicted_class = classes[int(probabilities_raw.argmax())]
        category = CLASS_LABELS[predicted_class]

        return {
            "kategori": category,
            "label": predicted_class,
            "probabilitas": round(float(probabilities_raw.max()), 4),
            "probabilities": probabilities,
            "model": self.model_name,
            "model_key": self.model_key,
            "bmi": actual_bmi,
            "features": features,
            "fitur_model": display_rows,
            "estimated": estimated,
            "adjusted": adjusted,
            "catatan": note,
        }
