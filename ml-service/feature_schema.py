"""Shared feature and label definitions for the GymSense ML service."""

from pathlib import Path

BASE_DIR = Path(__file__).resolve().parent
DATA_RAW = BASE_DIR / "data" / "raw" / "gym_members_exercise_tracking.csv"
ARTIFACTS_DIR = BASE_DIR / "artifacts"

TARGET = "Experience_Level"

NUMERIC_FEATURES = [
    "Age",
    "Weight (kg)",
    "Height (m)",
    "Max_BPM",
    "Avg_BPM",
    "Resting_BPM",
    "Session_Duration (hours)",
    "Calories_Burned",
    "Fat_Percentage",
    "Water_Intake (liters)",
    "Workout_Frequency (days/week)",
    "BMI",
]

CATEGORICAL_FEATURES = ["Gender", "Workout_Type"]

FEATURE_COLUMNS = [
    "Age",
    "Gender",
    "Weight (kg)",
    "Height (m)",
    "Max_BPM",
    "Avg_BPM",
    "Resting_BPM",
    "Session_Duration (hours)",
    "Calories_Burned",
    "Workout_Type",
    "Fat_Percentage",
    "Water_Intake (liters)",
    "Workout_Frequency (days/week)",
    "BMI",
]

# Target 1/2/3 in the public dataset = beginner/intermediate/advanced.
CLASS_LABELS = {1: "Beginner", 2: "Intermediate", 3: "Advanced"}
CLASS_NAMES = ["Beginner", "Intermediate", "Advanced"]
LABEL_TO_INT = {label: value for value, label in CLASS_LABELS.items()}

GOAL_LABELS = {
    "cutting": "Cutting",
    "bulking": "Bulking",
    "strength": "Strength",
}

# The dataset has no "goal" column. We translate the user's goal into the
# closest workout type so the existing model can consume it.
GOAL_TO_WORKOUT = {
    "cutting": "Cardio",
    "bulking": "Strength",
    "strength": "Strength",
}

WORKOUT_TYPES = ["Strength", "Cardio", "Yoga", "HIIT"]
GENDERS = ["Male", "Female"]

# Ranges observed in the training data. Values outside these ranges are
# clipped before inference so the model never receives out-of-distribution
# inputs.
TRAIN_RANGES = {
    "Age": (18, 59),
    "Weight (kg)": (40.0, 129.9),
    "Height (m)": (1.50, 2.00),
    "Max_BPM": (160, 199),
    "Avg_BPM": (120, 169),
    "Resting_BPM": (50, 74),
    "Session_Duration (hours)": (0.5, 2.0),
    "Calories_Burned": (303.0, 1783.0),
    "Fat_Percentage": (10.0, 35.0),
    "Water_Intake (liters)": (1.5, 3.7),
    "Workout_Frequency (days/week)": (2, 5),
    "BMI": (12.32, 49.84),
}

DISPLAY_NAMES = {
    "Age": "Usia",
    "Gender": "Gender",
    "Weight (kg)": "Berat",
    "Height (m)": "Tinggi",
    "Max_BPM": "Max BPM",
    "Avg_BPM": "Avg BPM",
    "Resting_BPM": "Resting BPM",
    "Session_Duration (hours)": "Durasi sesi",
    "Calories_Burned": "Kalori terbakar",
    "Workout_Type": "Tipe latihan",
    "Fat_Percentage": "Persentase lemak",
    "Water_Intake (liters)": "Asupan air",
    "Workout_Frequency (days/week)": "Frekuensi latihan",
    "BMI": "BMI",
}

USER_FIELD_TO_DISPLAY = {
    "max_bpm": "Max BPM",
    "avg_bpm": "Avg BPM",
    "resting_bpm": "Resting BPM",
    "session_duration": "Durasi sesi",
    "calories_burned": "Kalori terbakar",
    "fat_percentage": "Persentase lemak",
    "water_intake": "Asupan air",
    "workout_frequency": "Frekuensi latihan",
    "workout_type": "Tipe latihan",
    "gender": "Gender",
}
