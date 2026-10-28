---
name: gym-user-facing-revision
description: >-
  Panduan merevisi tampilan website GymSense (gym-web, Laravel) agar 100% berbahasa gym dan workout untuk pengguna akhir, tanpa menyebut atau menampilkan machine learning (nama algoritma seperti Random Forest, Logistic Regression, SVM; metrik seperti F1, accuracy, precision, recall; kata dataset, train, test split, FastAPI, file .pkl, probabilitas, fitur). Gunakan skill ini setiap kali pengguna meminta menghapus, mengganti, atau menyembunyikan konten teknis ML dari halaman Beranda, Rekomendasi, Perbandingan Model, Tentang, navbar, footer, judul halaman, atau pesan error; atau meminta tampilan yang lebih fokus ke latihan, program, level, dan progres. Skill ini menggantikan bagian di skill workout-ml-website-concept yang meminta halaman perbandingan model atau penjelasan metodologi ML untuk pengguna.
---

# Revisi Tampilan: Murni untuk Gym dan Workout

## Prinsip
Pengguna datang untuk tahu level dan program latihan mereka, bukan untuk membaca soal algoritma. Semua teks yang terlihat pengguna harus memakai bahasa gym: level, program, frekuensi, progres, tips. Teknologi di belakangnya tidak disebut.

**Yang berubah hanya sisi tampilan.** Backend, `MlService`, layanan `ml-service/`, dan nama class atau variabel internal tetap seperti semula. Jangan mengubah field respons API; cukup ubah label yang tampil.

## Hal yang tidak boleh dihapus tanpa konfirmasi
Proyek ini kemungkinan butuh perbandingan 3 model untuk laporan, presentasi, atau demo akademik. Jadi:
- **Jangan menghapus** controller, view, route, atau data metrik perbandingan model.
- **Pindahkan dan sembunyikan**: ubah URL ke `/admin/evaluasi-model`, lindungi dengan middleware `auth` atau flag `config('app.show_ml_report')` (default `false`, balas 404 saat mati; nilai dari `.env`: `SHOW_ML_REPORT=false`).
- Tanyakan ke pengguna sekali di awal: "Halaman perbandingan model dipindah ke URL tersembunyi, bukan dihapus. Setuju?" Hapus permanen hanya jika pengguna menjawab jelas.
- Rincian ML boleh tetap ada di `README.md`, `ml-service/README.md`, notebook, dan halaman admin tersebut.

## Istilah terlarang di tampilan pengguna
Berlaku untuk Blade, JS/Alpine, `<title>`, meta description, alt text, placeholder, tooltip, pesan validasi, dan komentar yang dirender:
`Random Forest`, `Logistic Regression`, `SVM`, `Support Vector Machine`, `KNN`, `model`, `algoritma`, `machine learning`, `ML`, `F1`, `macro`, `accuracy`, `precision`, `recall`, `confusion matrix`, `test split`, `dataset`, `training`, `train.py`, `.pkl`, `FastAPI`, `probabilitas`, `fitur`, `kelas target`, `skema label`, `ordinal`, `diprediksi`, `yakin`.

## Tabel penggantian (berdasarkan tampilan saat ini)
| Sekarang | Ganti dengan |
|---|---|
| Nav: Perbandingan Model | Hapus; ganti `Program` atau `Panduan Latihan` |
| Beranda: "Angka yang bisa kamu periksa sendiri" + 3 kartu model | Bagian "Cara kerja" 3 langkah: Isi profil, Dapat level dan program, Mulai latihan |
| Hero halaman model: "Tiga algoritma, satu test set" | Halaman dihapus dari navigasi (lihat bagian pemindahan) |
| Badge "Metrik live dari FastAPI" | Hapus |
| Kartu "Model terpilih", F1, Accuracy | Hapus dari publik |
| "Random Forest · 69,0% yakin" | "Kecocokan program: 69%" |
| "Cara membaca hasil: Model memprediksi level dari 14 fitur..." | "Level ini disarankan dari usia, berat, tinggi, tujuan, dan pengalaman latihanmu. Anggap sebagai titik awal, lalu sesuaikan dengan kondisi tubuhmu." |
| Kartu "Model / Skema label / Metrik pemilihan" | Kartu "Level kamu / Fokus program / Frekuensi per minggu" |
| Level `Beginner / Intermediate / Advanced (ordinal)` | `Pemula / Menengah / Mahir`, lewat file `lang/id/level.php`, jangan di-hardcode di Blade |
| Badge `Estimasi` dan `Turunan` | `Perkiraan` dan `Dihitung`, dengan tooltip singkat: "Dihitung dari data profilmu" |
| Tentang: metodologi, dataset, label | "Tentang GymSense": misi, cara kerja sederhana, privasi, disclaimer |

## Konten pengganti yang berguna (pilih sesuai kebutuhan, jangan mengarang klaim)
- Panduan per level: ringkasan program Pemula, Menengah, Mahir (frekuensi, split, intensitas).
- Contoh jadwal mingguan sebagai timeline atau kartu, bukan tabel metrik.
- Tips teknik dan pemulihan yang umum dan aman, tanpa klaim medis.
- FAQ gym singkat memakai komponen accordion dari skill dark-cosmic-ui-ux.
- Halaman hasil: kartu level besar, 3 poin program, "Langkah berikutnya", dan disclaimer (pertahankan teks "Bukan saran medis").

Semua komponen visual mengikuti skill dark-cosmic-ui-ux.

## Tentang URL dan SEO
- Perbarui `<title>` dan meta description tiap halaman ke bahasa gym.
- Hapus link ke halaman model dari navbar, footer, tombol "Buka detail metrik", dan sitemap.
- Tambahkan redirect 301 dari `/model` ke `/rekomendasi` bila halaman lama sudah dibagikan.

## Prosedur kerja
1. **Audit dulu, jangan langsung mengedit.** Cari istilah terlarang di tampilan:
   ```powershell
   Get-ChildItem resources,routes -Recurse -Include *.php,*.js | Select-String -Pattern "Random Forest|Logistic|SVM|KNN|F1|macro|accuracy|precision|recall|algoritma|machine learning|FastAPI|train\.py|\.pkl|dataset|probabilit|fitur|model"
   ```
   Abaikan kecocokan yang hanya berupa nama class atau variabel internal (misalnya Eloquent `Model`).
2. **Laporkan temuan** ke pengguna: daftar file, baris, dan rencana penggantian dalam satu tabel singkat.
3. **Edit** hanya string tampilan, navigasi, route, dan struktur section. Rapikan ulang spacing setelah bagian dihapus.
4. **Verifikasi:** jalankan `php artisan test`, buka halaman `/`, `/rekomendasi`, dan `/tentang`, lalu jalankan audit sekali lagi. Hasil yang boleh tersisa hanya di halaman admin dan README.
5. **Laporan akhir:** file yang diubah, apa yang dipindah ke admin, dan apa yang sengaja dibiarkan.

## Aturan tambahan
- Bahasa UI: Bahasa Indonesia yang santai tapi sopan, kalimat pendek, kata kerja jelas.
- Jangan menambah klaim hasil ("pasti turun 5 kg") atau data palsu seperti testimoni dan jumlah pengguna.
- Jika ragu apakah sebuah teks termasuk "teknis", anggap teknis dan tanyakan.
