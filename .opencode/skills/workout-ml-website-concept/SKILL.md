---
name: workout-ml-website-concept
description: Konsep, arsitektur, dan alur kerja website Laravel untuk klasifikasi / rekomendasi level dan kategori workout berbasis machine learning (input usia, berat, tinggi, pengalaman gym, tujuan cutting/bulking/strength; output kategori program atau intensitas), dengan perbandingan Logistic Regression/KNN, Random Forest, dan SVM (Accuracy, Precision, Recall, F1, Confusion Matrix). Gunakan skill ini setiap kali pengguna bekerja di proyek gym-web: membuat route, controller, Form Request, Blade view, integrasi layanan ML Python, dataset, training model, halaman perbandingan model, atau test Laravel untuk fitur rekomendasi workout. Skill ini soal konsep, data, model, dan arsitektur backend; tampilan visual ada di skill dark-cosmic-ui-ux.
---

# Website Rekomendasi Workout (Laravel + ML Python)

## Tujuan produk
Pengguna mengisi profil fisik, website memprediksi **kategori program latihan / intensitas** yang sesuai, dan menampilkan perbandingan 3 model ML secara transparan.

**Input:** usia, berat (kg), tinggi (cm), pengalaman gym (bulan atau level), tujuan (`cutting` | `bulking` | `strength`). Fitur turunan: BMI = berat / (tinggi_m)^2.
**Output (target):** pilih satu skema label dan konsisten, contoh `Beginner | Intermediate | Advanced`, atau gabungan tujuan + level (`Cutting-Beginner`, dst.). Tulis aturan pelabelan di `ml-service/README.md`.

## Arsitektur (penting)
Laravel adalah PHP, sedangkan scikit-learn adalah Python. Jangan menulis ulang algoritma ML di PHP. Pisahkan tugasnya:

```
gym-web/
├── app/ routes/ resources/ ...        # Laravel (UI, validasi, orkestrasi)
└── ml-service/                        # Python (training + inferensi)
    ├── data/                          # raw & processed
    ├── notebooks/                     # EDA, training, evaluasi
    ├── artifacts/                     # model_*.joblib, metrics.json, confusion_*.png
    ├── train.py                       # latih 3 model, simpan artifacts
    ├── app.py                         # FastAPI: POST /predict, GET /metrics, GET /health
    └── requirements.txt
```

Alur: Browser -> Laravel (`POST /rekomendasi`) -> validasi -> HTTP ke FastAPI `POST /predict` -> hasil -> Blade menampilkan kartu rekomendasi.

Fallback jika tidak bisa menjalankan dua server: `Process::run(['python', 'ml-service/predict_cli.py', $json])` (Laravel Process facade), tetapi lebih lambat; pakai hanya untuk demo.

## Sisi Laravel
- **Routes** (`routes/web.php`): `GET /` (landing), `GET /rekomendasi` (form), `POST /rekomendasi` (prediksi), `GET /model` (perbandingan model), `GET /tentang` (metodologi).
- **Controller:** `RekomendasiController` (form + prediksi), `ModelController` (metrik). Controller tipis, logika panggilan ML ditaruh di service class `app/Services/MlService.php`.
- **Validasi:** `app/Http/Requests/RekomendasiRequest.php`, aturan: `usia` integer 14-80, `tinggi` numeric 120-220, `berat` numeric 30-200, `pengalaman_bulan` integer 0-600, `tujuan` in `cutting,bulking,strength`. Pesan error dalam Bahasa Indonesia.
- **Config:** `config/services.php` -> `'ml' => ['url' => env('ML_SERVICE_URL', 'http://127.0.0.1:8001'), 'timeout' => 10]`. Tambah `ML_SERVICE_URL` ke `.env` dan `.env.example`.
- **Panggilan HTTP:**
```php
$res = Http::timeout(config('services.ml.timeout'))
    ->acceptJson()
    ->post(config('services.ml.url').'/predict', $data);
if ($res->failed()) { /* tampilkan pesan ramah, log error, jangan 500 */ }
```
- **Cache metrik:** `Cache::remember('ml.metrics', 3600, fn () => Http::get(.../metrics)->json());`
- **Penyimpanan riwayat (opsional):** tabel `predictions` (migration) hanya jika pengguna diberi tahu dan setuju; jangan simpan data profil diam-diam.
- **Views Blade:** `resources/views/layouts/app.blade.php`, `home`, `rekomendasi/form`, `rekomendasi/hasil`, `model/index`, `tentang`. Pecah komponen (`<x-card>`, `<x-button>`, `<x-segmented>`) di `resources/views/components/`.
- **Test:** Pest/PHPUnit di `tests/Feature`. Pakai `Http::fake()` untuk meniru layanan ML, uji validasi, hasil sukses, dan kasus layanan mati.

## Sisi ML (ml-service)
1. **EDA:** distribusi fitur, balance kelas, korelasi, outlier.
2. **Preprocessing:** OneHot untuk `tujuan`, Ordinal untuk level pengalaman, `StandardScaler` untuk numerik. Gunakan `Pipeline` dan `ColumnTransformer` agar tidak ada data leakage.
3. **Split:** stratified train/test, `random_state` tetap.
4. **Model (minimal 3):** Logistic Regression atau KNN, Random Forest, SVM (`SVC`, coba kernel linear dan RBF, `probability=True`).
5. **Tuning:** `GridSearchCV` dengan stratified k-fold.
6. **Evaluasi:** Accuracy, Precision, Recall, F1 (`macro` atau `weighted`, sebutkan pilihannya), Confusion Matrix, `classification_report`. Simpan semuanya ke `artifacts/metrics.json` + gambar confusion matrix.
7. **Model terbaik:** pilih berdasarkan F1 (bukan accuracy saja bila kelas tidak seimbang), simpan dengan `joblib`.
8. **API FastAPI:** model dimuat sekali saat startup; `/predict` menerima JSON, mengembalikan `{kategori, probabilitas, model}`; validasi dengan Pydantic.
9. **Dataset:** cari dataset publik (Kaggle/UCI). Bila membuat sintetis, aturan pelabelan harus eksplisit dan dinyatakan jujur sebagai sintetis di halaman "Tentang".

## Halaman perbandingan model
Tabel 3 model x metrik (Accuracy, Precision, Recall, F1), nilai terbaik per kolom diberi aksen, grafik batang, confusion matrix per model, dan catatan analisis (kelas mana yang sering tertukar, keterbatasan data).

## Perintah harian
```
php artisan serve              # Laravel
npm run dev                    # Vite
cd ml-service && uvicorn app:app --port 8001 --reload
php artisan test
```

## Aturan kerja untuk AI
- Ikuti konvensi Laravel; jangan mengedit `vendor/`, jangan membaca atau menampilkan isi `.env`.
- Jangan menambah package baru tanpa menyebut alasannya.
- Jangan menulis ulang model ML di PHP; semua inferensi lewat `ml-service`.
- Setelah mengubah fitur, jalankan `php artisan test` dan laporkan hasilnya.
- Untuk tampilan, ikuti skill dark-cosmic-ui-ux.
- Tampilkan disclaimer di halaman hasil: ini rekomendasi umum untuk edukasi, bukan saran medis; konsultasikan dengan dokter atau pelatih bila punya kondisi kesehatan.
- Jika skema label atau sumber dataset belum jelas, tanyakan satu pertanyaan terpenting dulu, lalu lanjut dengan asumsi yang dinyatakan.
