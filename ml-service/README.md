# GymSense ML Service

Layanan inferensi Python untuk website rekomendasi workout. Laravel hanya mengirim
profil pengguna lewat HTTP; seluruh preprocessing dan prediksi dilakukan di sini.

## Dataset

- **Nama:** Gym Members Exercise Tracking
- **Sumber:** dataset publik Kaggle (`valakhorasani`), 973 baris, 15 kolom.
- **Berkas:** `data/raw/gym_members_exercise_tracking.csv`
- **Target:** `Experience_Level` (1, 2, 3).
- **Sifat data:** dataset publik, **bukan** data pengguna nyata dan bukan hasil
  scraping. Dataset ini juga bukan data sintetis buatan proyek.
- **Kolom:** Age, Gender, Weight (kg), Height (m), Max_BPM, Avg_BPM, Resting_BPM,
  Session_Duration (hours), Calories_Burned, Workout_Type, Fat_Percentage,
  Water_Intake (liters), Workout_Frequency (days/week), Experience_Level, BMI.

## Aturan pelabelan (label scheme)

Skema label bersifat **ordinal** dan konsisten di seluruh aplikasi:

| Nilai dataset | Label aplikasi | Arti |
| --- | --- | --- |
| 1 | Beginner | Baru mulai / butuh fondasi teknik dan volume rendah |
| 2 | Intermediate | Sudah rutin, siap volume dan intensitas menengah |
| 3 | Advanced | Pengalaman tinggi, siap intensitas dan volume tinggi |

Aturan ini diambil langsung dari kolom target dataset. Tidak ada label gabungan
tujuan + level. Tujuan pengguna (`cutting` / `bulking` / `strength`) digunakan
untuk memilih detail program di Laravel dan untuk mengisi `Workout_Type` bila
pengguna tidak memilih tipe latihan:

| Tujuan | Workout_Type default |
| --- | --- |
| cutting | Cardio |
| bulking | Strength |
| strength | Strength |

## Pemetaan input website ke fitur model

Form website mengumpulkan lima input utama:

`usia`, `berat` (kg), `tinggi` (cm), `pengalaman_bulan`, `tujuan`.

Model yang ada memakai 14 fitur dataset. Karena itu:

- `Age = usia`
- `Weight (kg) = berat`
- `Height (m) = tinggi / 100`
- `BMI = berat / (tinggi_m)^2`
- `Gender` default `Male` (bisa diubah di bagian metrik opsional)
- `Workout_Type` mengikuti tabel tujuan di atas (bisa diubah)
- `Max_BPM`, `Avg_BPM`, `Resting_BPM`, `Session_Duration`, `Calories_Burned`,
  `Fat_Percentage`, `Water_Intake`, `Workout_Frequency` diestimasi dari usia,
  berat, BMI, tujuan, dan pengalaman bila pengguna tidak mengisinya.

Pengalaman gym **tidak** menjadi fitur model. Nilai tersebut dipakai untuk
mengestimasi durasi sesi dan frekuensi latihan:

| Pengalaman | Durasi default | Frekuensi default |
| --- | --- | --- |
| < 12 bulan | 0,75 jam | 2 hari/minggu |
| 12–35 bulan | 1,25 jam | 4 hari/minggu |
| >= 36 bulan | 1,5 jam | 5 hari/minggu |

Estimasi lain:

- `Max_BPM = clamp(220 - usia, 160, 199)`
- `Avg_BPM = clamp(0.8 * Max_BPM, 120, 169)`
- `Resting_BPM = clamp(62 + 0.12 * (usia - 38), 50, 74)`
- `Calories_Burned ≈ 8.8 * berat * durasi * faktor tujuan`
- `Fat_Percentage` memakai pendekatan Deurenberg:
  `1.20*BMI + 0.23*usia - 10.8*sex - 5.4`, dengan `sex = 1` untuk Male.
- `Water_Intake ≈ 0.035 * berat + 0.4 * durasi`

Nilai yang diestimasi dikembalikan di field `estimated`; nilai yang dijepit ke
rentang data latih dikembalikan di field `adjusted`. Semua field ini ditampilkan
di halaman hasil agar pengguna tahu mana yang benar-benar diukur dan mana yang
estimasi.

## Preprocessing

- Numerik: `StandardScaler`
- Kategorikal: `OneHotEncoder(handle_unknown="ignore")`
- Semua langkah dibungkus `Pipeline`/`ColumnTransformer` agar scaler dan encoder
  hanya di-fit pada data train (tidak ada data leakage).
- Split: stratified 80/20, `random_state=42`.
- Metrik: **macro average** untuk precision, recall, dan F1. F1 macro dipilih
  sebagai dasar pemilihan model karena kelas Advanced lebih sedikit.

## Model yang dibandingkan

1. **Random Forest** — artifact pengguna (`model_gym_rf.pkl` +
   `preprocessor_gym.pkl`) yang sudah ada, dievaluasi ulang pada test split.
2. **Logistic Regression** — dilatih ulang, `GridSearchCV` + stratified 5-fold,
   tuning `C` dan `class_weight`.
3. **Support Vector Machine** — `SVC(probability=True)`, tuning kernel linear/RBF,
   `C`, `gamma`, dan `class_weight`.

Model terbaik dipilih berdasarkan **F1 macro** dan disimpan sebagai
`artifacts/best_model.joblib`. Berdasarkan training terakhir:

| Model | Accuracy | Precision | Recall | F1 macro |
| --- | --- | --- | --- | --- |
| Random Forest (best) | 0.9128 | 0.9293 | 0.9267 | 0.9274 |
| Logistic Regression | 0.8718 | 0.8942 | 0.8946 | 0.8938 |
| SVM | 0.8769 | 0.8982 | 0.8975 | 0.8978 |

> Catatan transparansi: Random Forest berasal dari artifact pengguna. Proses
> split asli saat model dibuat tidak terdokumentasi, sehingga metrik test-nya
> bisa sedikit optimistis. Logistic Regression dan SVM dilatih ulang dengan
> split yang sama dan preprocessing yang di-fit pada train.

## Artefak

```
artifacts/
├── best_model.joblib
├── model_logistic_regression.joblib
├── model_random_forest.joblib
├── model_svm.joblib
├── model_gym_rf.pkl            # artifact asli pengguna
├── preprocessor_gym.pkl        # artifact asli pengguna
├── metrics.json
├── confusion_logistic_regression.png
├── confusion_random_forest.png
├── confusion_svm.png
└── eda_overview.png
```

## Menjalankan

Dari root `gym-web`:

```powershell
# sekali saja
python -m venv ml-service/.venv
ml-service/.venv/Scripts/python.exe -m pip install -r ml-service/requirements.txt

# latih ulang (butuh ~1-2 menit)
ml-service/.venv/Scripts/python.exe ml-service/train.py

# jalankan API
ml-service/.venv/Scripts/python.exe -m uvicorn app:app --host 127.0.0.1 --port 8001 --reload --app-dir ml-service
```

Fallback tanpa dua server (lebih lambat, hanya untuk demo):

```powershell
ml-service/.venv/Scripts/python.exe ml-service/predict_cli.py '{"usia":28,"berat":72,"tinggi":175,"pengalaman_bulan":18,"tujuan":"cutting"}'
```

## API

### `GET /health`

```json
{
  "status": "ok",
  "model": "Random Forest",
  "model_key": "random_forest",
  "best_f1": 0.9274
}
```

### `GET /metrics`

Mengembalikan seluruh isi `artifacts/metrics.json`, termasuk confusion matrix,
classification report, EDA, dan catatan analisis.

### `POST /predict`

Request minimal:

```json
{
  "usia": 28,
  "berat": 72,
  "tinggi": 175,
  "pengalaman_bulan": 18,
  "tujuan": "cutting"
}
```

Field opsional: `gender`, `workout_type`, `max_bpm`, `avg_bpm`, `resting_bpm`,
`session_duration`, `calories_burned`, `fat_percentage`, `water_intake`,
`workout_frequency`.

Response:

```json
{
  "kategori": "Intermediate",
  "label": 2,
  "probabilitas": 0.51,
  "probabilities": {
    "Beginner": 0.05,
    "Intermediate": 0.51,
    "Advanced": 0.44
  },
  "model": "Random Forest",
  "model_key": "random_forest",
  "bmi": 23.51,
  "features": {},
  "fitur_model": [],
  "estimated": ["Max BPM", "..."],
  "adjusted": [],
  "catatan": null
}
```

## Keterbatasan

- Model memprediksi level/intensitas, bukan diagnosis medis atau jaminan hasil.
- Dataset publik 973 baris, rentang usia 18–59 tahun, tidak mewakili semua
  populasi atau kondisi kesehatan.
- Beberapa fitur model adalah estimasi saat pengguna tidak mengisinya. Untuk
  hasil yang lebih personal, isi bagian "Metrik latihan" di form.
- Random Forest adalah artifact yang sudah ada; provenance split-nya tidak
  terdokumentasi penuh.
