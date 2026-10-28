"""Train and compare three classifiers for the GymSense workout recommender.

Run from the repository root:
    ml-service/.venv/Scripts/python.exe ml-service/train.py

Outputs:
    artifacts/metrics.json
    artifacts/model_logistic_regression.joblib
    artifacts/model_random_forest.joblib
    artifacts/model_svm.joblib
    artifacts/best_model.joblib
    artifacts/confusion_*.png
    artifacts/eda_overview.png
"""

from __future__ import annotations

import json
import time
from datetime import datetime, timezone
from pathlib import Path

import joblib
import matplotlib

matplotlib.use("Agg")
import matplotlib.pyplot as plt
import numpy as np
import pandas as pd
from matplotlib.colors import LinearSegmentedColormap
from sklearn.compose import ColumnTransformer
from sklearn.ensemble import RandomForestClassifier
from sklearn.linear_model import LogisticRegression
from sklearn.metrics import (
    accuracy_score,
    classification_report,
    confusion_matrix,
    f1_score,
    precision_score,
    recall_score,
)
from sklearn.model_selection import GridSearchCV, StratifiedKFold, train_test_split
from sklearn.pipeline import Pipeline
from sklearn.preprocessing import OneHotEncoder, StandardScaler
from sklearn.svm import SVC

from feature_schema import (
    ARTIFACTS_DIR,
    CATEGORICAL_FEATURES,
    CLASS_LABELS,
    CLASS_NAMES,
    DATA_RAW,
    FEATURE_COLUMNS,
    GOAL_TO_WORKOUT,
    LABEL_TO_INT,
    NUMERIC_FEATURES,
    TARGET,
)

RANDOM_STATE = 42
TEST_SIZE = 0.20
METRIC_AVERAGE = "macro"
INDIGO_CMAP = LinearSegmentedColormap.from_list(
    "gymsense_indigo", ["#0b0b18", "#312e81", "#6366f1", "#a5b4fc"]
)


def pretty_feature(name: str) -> str:
    cleaned = name.replace("num__", "").replace("cat__", "")
    mapping = {
        "Age": "Usia",
        "Weight (kg)": "Berat",
        "Height (m)": "Tinggi",
        "Max_BPM": "Max BPM",
        "Avg_BPM": "Avg BPM",
        "Resting_BPM": "Resting BPM",
        "Session_Duration (hours)": "Durasi sesi",
        "Calories_Burned": "Kalori terbakar",
        "Fat_Percentage": "Persentase lemak",
        "Water_Intake (liters)": "Asupan air",
        "Workout_Frequency (days/week)": "Frekuensi latihan",
        "BMI": "BMI",
    }
    if cleaned in mapping:
        return mapping[cleaned]
    if cleaned.startswith("Gender_"):
        return "Gender: " + cleaned.split("_", 1)[1]
    if cleaned.startswith("Workout_Type_"):
        return "Tipe latihan: " + cleaned.split("_", 1)[1]
    return cleaned.replace("_", " ")


def to_native(value):
    if isinstance(value, dict):
        return {str(key): to_native(item) for key, item in value.items()}
    if isinstance(value, (list, tuple)):
        return [to_native(item) for item in value]
    if isinstance(value, np.integer):
        return int(value)
    if isinstance(value, np.floating):
        return float(value)
    if isinstance(value, np.ndarray):
        return to_native(value.tolist())
    if isinstance(value, (np.bool_,)):
        return bool(value)
    return value


def build_preprocessor() -> ColumnTransformer:
    return ColumnTransformer(
        transformers=[
            ("num", StandardScaler(), NUMERIC_FEATURES),
            (
                "cat",
                OneHotEncoder(handle_unknown="ignore", sparse_output=False),
                CATEGORICAL_FEATURES,
            ),
        ],
        remainder="drop",
        verbose_feature_names_out=False,
    )


def evaluate_pipeline(
    key: str,
    name: str,
    pipeline: Pipeline,
    test_frame: pd.DataFrame,
    test_target: pd.Series,
    provenance: str,
    training_seconds: float,
    best_params: dict | None = None,
) -> dict:
    predictions = pipeline.predict(test_frame)
    labels = sorted(CLASS_LABELS)
    matrix = confusion_matrix(test_target, predictions, labels=labels)
    report = classification_report(
        test_target,
        predictions,
        labels=labels,
        target_names=CLASS_NAMES,
        output_dict=True,
        zero_division=0,
    )

    per_class = []
    for label in CLASS_NAMES:
        row = report[label]
        per_class.append(
            {
                "label": label,
                "precision": float(row["precision"]),
                "recall": float(row["recall"]),
                "f1": float(row["f1-score"]),
                "support": int(row["support"]),
            }
        )

    entry = {
        "key": key,
        "name": name,
        "file": f"model_{key}.joblib",
        "provenance": provenance,
        "accuracy": float(accuracy_score(test_target, predictions)),
        "precision": float(
            precision_score(test_target, predictions, average=METRIC_AVERAGE, zero_division=0)
        ),
        "recall": float(
            recall_score(test_target, predictions, average=METRIC_AVERAGE, zero_division=0)
        ),
        "f1": float(
            f1_score(test_target, predictions, average=METRIC_AVERAGE, zero_division=0)
        ),
        "confusion_matrix": [[int(value) for value in row] for row in matrix],
        "classification_report": to_native(report),
        "per_class": per_class,
        "best_params": to_native(best_params or {}),
        "training_seconds": round(float(training_seconds), 2),
        "is_best": False,
    }

    save_confusion_matrix(entry, labels)
    return entry


def save_confusion_matrix(entry: dict, labels: list[int]) -> None:
    matrix = np.array(entry["confusion_matrix"])
    fig, ax = plt.subplots(figsize=(5.2, 4.2), facecolor="#0a0a14")
    ax.set_facecolor("#0a0a14")
    ax.imshow(matrix, cmap=INDIGO_CMAP)

    threshold = matrix.max() / 2 if matrix.max() else 1
    for row in range(matrix.shape[0]):
        for col in range(matrix.shape[1]):
            value = matrix[row, col]
            color = "#05050c" if value > threshold else "#f5f6fb"
            ax.text(
                col,
                row,
                str(value),
                ha="center",
                va="center",
                color=color,
                fontsize=11,
                fontweight="600",
            )

    ax.set_xticks(range(len(labels)), CLASS_NAMES, color="#8e92ab", fontsize=9)
    ax.set_yticks(range(len(labels)), CLASS_NAMES, color="#8e92ab", fontsize=9)
    ax.set_xlabel("Prediksi", color="#8e92ab", labelpad=8)
    ax.set_ylabel("Aktual", color="#8e92ab", labelpad=8)
    ax.set_title(
        f"Confusion Matrix - {entry['name']}",
        color="#f5f6fb",
        fontsize=11,
        pad=12,
    )
    for spine in ax.spines.values():
        spine.set_color("#26263a")
    ax.tick_params(colors="#8e92ab", length=0)
    fig.tight_layout()
    fig.savefig(
        ARTIFACTS_DIR / f"confusion_{entry['key']}.png",
        dpi=170,
        facecolor=fig.get_facecolor(),
    )
    plt.close(fig)


def save_eda_overview(df: pd.DataFrame) -> dict:
    numeric = df[NUMERIC_FEATURES + [TARGET]]
    correlations = (
        numeric.corr()[TARGET]
        .drop(labels=[TARGET])
        .sort_values(key=lambda series: series.abs(), ascending=False)
    )

    fig, axes = plt.subplots(1, 2, figsize=(11, 4.2), facecolor="#0a0a14")
    counts = df[TARGET].map(CLASS_LABELS).value_counts().reindex(CLASS_NAMES)
    axes[0].set_facecolor("#0a0a14")
    bars = axes[0].bar(counts.index, counts.values, color="#6366f1", width=0.55)
    axes[0].bar_label(bars, color="#f5f6fb", fontsize=9)
    axes[0].set_title("Distribusi kelas target", color="#f5f6fb", fontsize=10)
    axes[0].tick_params(colors="#8e92ab", length=0)
    axes[0].set_ylim(0, counts.max() * 1.2)

    axes[1].set_facecolor("#0a0a14")
    axes[1].barh(
        [pretty_feature(name) for name in correlations.index],
        correlations.values,
        color="#8fb4ff",
    )
    axes[1].axvline(0, color="#26263a", linewidth=1)
    axes[1].invert_yaxis()
    axes[1].set_title("Korelasi fitur numerik dengan target", color="#f5f6fb", fontsize=10)
    axes[1].tick_params(colors="#8e92ab", length=0, labelsize=8)
    for spine in list(axes[0].spines.values()) + list(axes[1].spines.values()):
        spine.set_color("#26263a")

    fig.tight_layout()
    fig.savefig(
        ARTIFACTS_DIR / "eda_overview.png",
        dpi=170,
        facecolor=fig.get_facecolor(),
    )
    plt.close(fig)

    summary = {}
    for feature in NUMERIC_FEATURES:
        series = df[feature]
        summary[feature] = {
            "mean": round(float(series.mean()), 3),
            "std": round(float(series.std()), 3),
            "min": round(float(series.min()), 3),
            "max": round(float(series.max()), 3),
        }

    outliers = {}
    for feature in NUMERIC_FEATURES:
        series = df[feature]
        q1, q3 = series.quantile(0.25), series.quantile(0.75)
        iqr = q3 - q1
        outliers[feature] = int(
            ((series < q1 - 1.5 * iqr) | (series > q3 + 1.5 * iqr)).sum()
        )

    return {
        "rows": int(len(df)),
        "features": len(FEATURE_COLUMNS),
        "missing_values": int(df.isna().sum().sum()),
        "duplicate_rows": int(df.duplicated().sum()),
        "class_distribution": {
            CLASS_LABELS[int(key)]: int(value)
            for key, value in df[TARGET].value_counts().sort_index().items()
        },
        "numeric_summary": summary,
        "outliers_iqr": outliers,
        "correlations_with_target": {
            feature: round(float(value), 4)
            for feature, value in correlations.items()
        },
    }


def main() -> None:
    ARTIFACTS_DIR.mkdir(parents=True, exist_ok=True)
    if not DATA_RAW.exists():
        raise SystemExit(
            f"Dataset tidak ditemukan di {DATA_RAW}. "
            "Letakkan gym_members_exercise_tracking.csv di ml-service/data/raw/."
        )

    df = pd.read_csv(DATA_RAW)
    missing_columns = [
        column for column in FEATURE_COLUMNS + [TARGET] if column not in df.columns
    ]
    if missing_columns:
        raise SystemExit(f"Kolom dataset tidak lengkap: {missing_columns}")

    df = df.dropna(subset=FEATURE_COLUMNS + [TARGET]).copy()
    X = df[FEATURE_COLUMNS]
    y = df[TARGET].astype(int)

    X_train, X_test, y_train, y_test = train_test_split(
        X,
        y,
        test_size=TEST_SIZE,
        stratify=y,
        random_state=RANDOM_STATE,
    )

    print(f"Dataset: {len(df)} baris, {len(FEATURE_COLUMNS)} fitur")
    print(f"Train: {len(X_train)} | Test: {len(X_test)}")
    print("Distribusi kelas:", y.value_counts().sort_index().to_dict())

    eda = save_eda_overview(df)
    models: list[dict] = []
    fitted_pipelines: dict[str, Pipeline] = {}

    # 1. Random Forest artifact from the user's existing ML project.
    rf_pipeline = Pipeline(
        [
            ("preprocessor", joblib.load(ARTIFACTS_DIR / "preprocessor_gym.pkl")),
            ("classifier", joblib.load(ARTIFACTS_DIR / "model_gym_rf.pkl")),
        ]
    )
    models.append(
        evaluate_pipeline(
            key="random_forest",
            name="Random Forest",
            pipeline=rf_pipeline,
            test_frame=X_test,
            test_target=y_test,
            provenance=(
                "Model Random Forest yang sudah ada di proyek pengguna "
                "(model_gym_rf.pkl + preprocessor_gym.pkl), dievaluasi ulang "
                "pada test split 20%."
            ),
            training_seconds=0.0,
            best_params=dict(rf_pipeline.named_steps["classifier"].get_params()),
        )
    )
    fitted_pipelines["random_forest"] = rf_pipeline
    print(f"[RF] macro F1 = {models[-1]['f1']:.4f}")

    # 2. Logistic Regression with GridSearchCV.
    cv = StratifiedKFold(n_splits=5, shuffle=True, random_state=RANDOM_STATE)
    lr_pipeline = Pipeline(
        [
            ("preprocessor", build_preprocessor()),
            (
                "classifier",
                LogisticRegression(max_iter=4000, random_state=RANDOM_STATE),
            ),
        ]
    )
    lr_grid = {
        "classifier__C": [0.1, 1.0, 10.0],
        "classifier__class_weight": [None, "balanced"],
    }
    started = time.perf_counter()
    lr_search = GridSearchCV(
        lr_pipeline,
        lr_grid,
        scoring="f1_macro",
        cv=cv,
        n_jobs=-1,
        refit=True,
        verbose=0,
    )
    lr_search.fit(X_train, y_train)
    lr_seconds = time.perf_counter() - started
    models.append(
        evaluate_pipeline(
            key="logistic_regression",
            name="Logistic Regression",
            pipeline=lr_search.best_estimator_,
            test_frame=X_test,
            test_target=y_test,
            provenance="Dilatih ulang oleh train.py memakai GridSearchCV + stratified 5-fold.",
            training_seconds=lr_seconds,
            best_params=lr_search.best_params_,
        )
    )
    fitted_pipelines["logistic_regression"] = lr_search.best_estimator_
    print(f"[LR] macro F1 = {models[-1]['f1']:.4f} | params = {lr_search.best_params_}")

    # 3. Support Vector Machine (linear + RBF), with probability estimates.
    svm_pipeline = Pipeline(
        [
            ("preprocessor", build_preprocessor()),
            (
                "classifier",
                SVC(probability=True, random_state=RANDOM_STATE),
            ),
        ]
    )
    svm_grid = {
        "classifier__C": [1, 10],
        "classifier__kernel": ["rbf", "linear"],
        "classifier__gamma": ["scale", 0.1],
        "classifier__class_weight": [None, "balanced"],
    }
    started = time.perf_counter()
    svm_search = GridSearchCV(
        svm_pipeline,
        svm_grid,
        scoring="f1_macro",
        cv=cv,
        n_jobs=-1,
        refit=True,
        verbose=0,
    )
    svm_search.fit(X_train, y_train)
    svm_seconds = time.perf_counter() - started
    models.append(
        evaluate_pipeline(
            key="svm",
            name="Support Vector Machine",
            pipeline=svm_search.best_estimator_,
            test_frame=X_test,
            test_target=y_test,
            provenance="Dilatih ulang oleh train.py memakai GridSearchCV + stratified 5-fold.",
            training_seconds=svm_seconds,
            best_params=svm_search.best_params_,
        )
    )
    fitted_pipelines["svm"] = svm_search.best_estimator_
    print(f"[SVM] macro F1 = {models[-1]['f1']:.4f} | params = {svm_search.best_params_}")

    best = max(models, key=lambda entry: entry["f1"])
    best["is_best"] = True

    for entry in models:
        joblib.dump(fitted_pipelines[entry["key"]], ARTIFACTS_DIR / entry["file"])
    joblib.dump(fitted_pipelines[best["key"]], ARTIFACTS_DIR / "best_model.joblib")

    best_matrix = np.array(best["confusion_matrix"])
    confused = []
    for row in range(best_matrix.shape[0]):
        for col in range(best_matrix.shape[1]):
            if row != col and best_matrix[row, col] > 0:
                confused.append(
                    {
                        "from": CLASS_NAMES[row],
                        "to": CLASS_NAMES[col],
                        "count": int(best_matrix[row, col]),
                    }
                )
    confused.sort(key=lambda item: item["count"], reverse=True)

    top_features = []
    try:
        preprocessor = fitted_pipelines["random_forest"].named_steps["preprocessor"]
        classifier = fitted_pipelines["random_forest"].named_steps["classifier"]
        names = preprocessor.get_feature_names_out()
        importances = classifier.feature_importances_
        order = np.argsort(importances)[::-1][:8]
        top_features = [
            {
                "feature": pretty_feature(str(names[index])),
                "importance": round(float(importances[index]), 4),
            }
            for index in order
        ]
    except Exception as error:  # pragma: no cover - defensive only
        print(f"Lewati feature importance: {error}")

    metrics = {
        "version": "1.0.0",
        "generated_at": datetime.now(timezone.utc).isoformat(),
        "dataset": {
            "name": "Gym Members Exercise Tracking",
            "source": "Kaggle public dataset (valakhorasani)",
            "rows": int(len(df)),
            "features": len(FEATURE_COLUMNS),
            "classes": CLASS_NAMES,
            "label_column": TARGET,
            "synthetic": False,
            "class_distribution": eda["class_distribution"],
            "missing_values": eda["missing_values"],
            "duplicate_rows": eda["duplicate_rows"],
        },
        "label_scheme": {
            "type": "ordinal",
            "mapping": {str(key): value for key, value in CLASS_LABELS.items()},
            "description": (
                "Experience_Level 1/2/3 pada dataset dipetakan ke "
                "Beginner/Intermediate/Advanced."
            ),
        },
        "preprocessing": {
            "numeric": "StandardScaler",
            "categorical": "OneHotEncoder(handle_unknown='ignore')",
            "split": {
                "test_size": TEST_SIZE,
                "random_state": RANDOM_STATE,
                "stratified": True,
            },
            "goal_mapping": GOAL_TO_WORKOUT,
        },
        "metric_average": METRIC_AVERAGE,
        "models": models,
        "best_model": {
            "key": best["key"],
            "name": best["name"],
            "file": "best_model.joblib",
            "metric": "f1_macro",
            "value": best["f1"],
        },
        "analysis": {
            "frequently_confused": confused[:5],
            "top_features": top_features,
            "notes": [
                "F1 macro dipakai sebagai dasar pemilihan model karena kelas Advanced lebih sedikit (191 dari 973 baris).",
                "Random Forest berasal dari artifact pengguna sehingga metriknya dapat sedikit optimistis: proses split asli tidak terdokumentasi.",
                "Model memprediksi level/intensitas, bukan diagnosis medis atau jaminan hasil latihan.",
                "Fitur seperti BPM, kalori, dan durasi sesi diisi otomatis bila pengguna tidak mengisinya; nilai tersebut adalah estimasi, bukan pengukuran.",
                "Dataset publik ini terbatas pada 973 baris dan tidak mewakili semua populasi, usia ekstrem, atau kondisi kesehatan khusus.",
            ],
        },
        "eda": eda,
    }

    with (ARTIFACTS_DIR / "metrics.json").open("w", encoding="utf-8") as handle:
        json.dump(to_native(metrics), handle, ensure_ascii=False, indent=2)

    print("\n=== Ringkasan model ===")
    for entry in models:
        marker = " (BEST)" if entry["is_best"] else ""
        print(
            f"{entry['name']:<24} acc={entry['accuracy']:.3f} "
            f"precision={entry['precision']:.3f} recall={entry['recall']:.3f} "
            f"f1={entry['f1']:.3f}{marker}"
        )
    print(f"\nModel terbaik: {best['name']} (macro F1 = {best['f1']:.4f})")
    print(f"Artefak disimpan di: {ARTIFACTS_DIR}")


if __name__ == "__main__":
    main()
