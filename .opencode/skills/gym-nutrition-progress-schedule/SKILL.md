---
name: gym-nutrition-progress-schedule
description: "Panduan membangun fitur nutrisi otomatis, jadwal latihan, penyimpanan program dengan database dan akun, check-in mingguan, serta grafik progres bertema neon-gelap di website gym-web (Laravel, GymSense). Gunakan saat diminta menghitung kalori dan makro dari berat badan, membuat jadwal mingguan atau 4 minggu setelah form rekomendasi, menyimpan progres per minggu, membuat halaman Progres atau Jadwal, atau membuat grafik, heatmap, dan progress ring."
---

# Fitur Nutrisi, Jadwal, dan Progres (Laravel)

Skill ini berpasangan dengan dark-cosmic-ui-ux (gaya visual) dan gym-user-facing-revision (tidak menyebut ML ke pengguna). Semua logika di sini adalah aturan biasa di PHP, bukan ML.

**Alur produk:** isi profil -> hasil (level + nutrisi + jadwal) -> simpan sebagai program aktif -> check-in mingguan -> grafik progres.
Pengguna tanpa akun tetap bisa menghitung rekomendasi (tanpa menyimpan). Menyimpan program dan progres membutuhkan login.

## Cara kerja per tahap
Kerjakan satu tahap per permintaan. Tiap tahap: rencana singkat -> implementasi -> test -> laporan file yang berubah.
1. Auth + migrasi + simpan program
2. NutritionCalculator + test + tampil di hasil
3. ScheduleGenerator + halaman Jadwal + "tandai selesai"
4. Check-in + halaman Progres + grafik
5. Poles: empty state, halaman privasi, aksesibilitas

## 1. Nutrisi (`app/Services/NutritionCalculator.php`)
Fungsi murni (input -> array), mudah dites. Semua hasil berlabel "Perkiraan".
- **BMR (Mifflin-St Jeor):** `10*kg + 6.25*cm - 5*usia + s`, dengan `s = +5` (pria), `-161` (wanita), `-78` bila gender belum diisi.
- **Faktor aktivitas** dari sesi latihan per minggu: 2 atau kurang = 1.375, 3-4 = 1.55, 5 atau lebih = 1.725. `TDEE = BMR x faktor`.
- **Target kalori:** cutting = TDEE - 400 (rentang 300-500, maksimal 20% TDEE); bulking = TDEE + 300 (rentang 250-400); strength = TDEE + 0 sampai 150.
- **Makro:** protein cutting 2.0 g/kg (1.8-2.2), bulking dan strength 1.8 g/kg (1.6-2.0); lemak 0.9 g/kg; karbohidrat = sisa kalori / 4. Protein dan karbo 4 kkal/g, lemak 9 kkal/g.
- **Serat** 25-35 g/hari; **air** 35 ml/kg (rentang 30-40).
- Bulatkan kalori ke 10 terdekat dan gram ke bilangan bulat. Tampilkan rentang, bukan angka yang terlihat pasti.

### Pengaman (wajib, ada di test)
- **BMI di bawah 18,5 dan tujuan cutting:** jangan beri defisit. Tampilkan catatan ramah dan arahkan ke target maintenance atau surplus ringan.
- **Usia di bawah 18:** tanpa target defisit atau surplus; tampilkan panduan umum (protein cukup, air, tidur) dan sarankan bicara dengan orang tua, dokter, atau pelatih.
- **Batas bawah kalori:** jangan menampilkan target di bawah BMR, atau di bawah 1500 kkal (pria) dan 1200 kkal (wanita). Jika hasil hitungan lebih rendah, naikkan ke batas itu dan beri catatan.
- Jangan menjanjikan hasil ("pasti turun X kg"). Pertahankan disclaimer "Bukan saran medis".
- Penyesuaian otomatis (opsional, versi lanjut): jika 2 check-in berturut-turut tidak sesuai target, usulkan perubahan 150 kkal dan biarkan pengguna menyetujui.

## 2. Jadwal (`app/Services/ScheduleGenerator.php`)
- Input: level, tujuan, sesi per minggu, split dari hasil rekomendasi, dan hari yang dipilih pengguna.
- Output: jadwal 7 hari dan jadwal 4 minggu; minggu ke-4 adalah deload (volume sekitar 60%).
- Template latihan di `config/workout_templates.php` (data, bukan di controller): per split dan hari berisi fokus, daftar latihan, set x repetisi, RPE. Jangan mengarang klaim kesehatan.
- Aturan: hindari otot yang sama di hari berurutan, beri hari istirahat setelah 3 hari latihan beruntun, taruh kardio zona 2 di hari istirahat atau setelah angkat beban. Jika hari tersedia lebih sedikit dari sesi, kurangi sesi dan beri tahu pengguna.
- Tombol "tandai selesai" mengisi `completed_at` sesi dan menjadi sumber data progres.
- Ekspor kalender `.ics` adalah bonus, bukan wajib.

## 3. Database dan akun
- Autentikasi: cek `composer.json`; gunakan starter kit Laravel (misalnya Breeze) hanya setelah pengguna menyetujui.
- Tabel:
  - `programs`: user_id, level, goal, usia, berat_kg, tinggi_cm, pengalaman_bulan, gender (nullable), kalori, protein_g, lemak_g, karbo_g, air_ml, sesi_per_minggu, mulai_pada, aktif.
  - `plan_sessions`: program_id, minggu_ke, hari, tanggal, fokus, completed_at (nullable).
  - `weekly_checkins`: program_id, minggu_ke, berat_kg, pinggang_cm (nullable), energi 1-5 (nullable), catatan (nullable); unik (program_id, minggu_ke).
- Gunakan migration, relasi Eloquent, Form Request (berat 30-200, pinggang 40-200), dan Policy: pengguna hanya bisa melihat dan mengubah data miliknya.
- **Privasi:** teks "Data profil tidak disimpan ke database" harus diganti agar akurat. Tambahkan persetujuan saat menyimpan program, halaman Akun dengan "ekspor data" dan "hapus akun beserta datanya" (cascade). Simpan seminimal mungkin, tanpa foto di versi pertama.
- Route: `/program`, `/jadwal`, `/progres`, `POST /progres/checkin`. Tambah menu "Progres" di navbar untuk pengguna yang login.
- Test: otorisasi (pengguna A tidak bisa melihat data pengguna B), kasus pengaman nutrisi (misalnya BMI 18,3 + cutting tidak menghasilkan defisit), dan aturan ScheduleGenerator.

## 4. Grafik progres
Enam grafik ditambah donut. Jangan menambah jenis lain (candlestick, flow map, contour, boxplot, piramida populasi, network, venn, fraction) karena tidak cocok untuk data gym.

| Grafik | Data | Teknik |
|---|---|---|
| Tracking (garis mulus; aktual solid, target putus-putus, pita zona target) | Berat per minggu | Chart.js line |
| Combination (bar + garis) | Sesi selesai (bar) + pinggang atau berat (garis) | Chart.js bar + line, dua sumbu |
| Circular progress | Sesi minggu ini, progres ke target | SVG `stroke-dasharray`, angka di tengah |
| Progress bar gradasi | Makro harian, progres program 4 minggu | div + `role="progressbar"` dan `aria-valuenow` |
| Heatmap konsistensi | 12 minggu x 7 hari, status per hari | CSS grid; status: selesai, terlewat, terjadwal, istirahat |
| Timeline program | 4 minggu, pin check-in, minggu 4 = Deload | Flex horizontal, vertikal di mobile |
| Donut makro | Protein, karbo, lemak | SVG |

Ditunda ke versi lanjut: radar, area chart, waterfall, grouped bar.

### Gaya (neon yang diredam, serasi dengan dark-cosmic-ui-ux)
- Warna utama gradasi `--accent` (#6366f1) ke `--accent-soft` (#8fb4ff). Pink (#e754b8) hanya untuk target, proyeksi, atau peringatan. Teal (#3bb8b0) hanya untuk status selesai. Maksimal 2 warna per grafik; tanpa kombinasi merah-hijau.
- Isian gradasi transparan 20-35%, glow tipis (`drop-shadow(0 0 8px rgba(99,102,241,.35))`), garis grid `rgba(255,255,255,.05)`, tooltip berupa kartu kaca gelap.
- Animasi gambar-garis 600 ms ease-out; matikan jika `prefers-reduced-motion`.
- Chart.js dipasang lewat npm (`npm i chart.js`), daftarkan hanya modul yang dipakai, tulis di `resources/js/charts/`. Jangan memakai CDN.
- Responsif: tinggi grafik 220-280 px, bertumpuk di mobile; heatmap boleh scroll horizontal.

### Aksesibilitas dan kondisi kosong
- Perbedaan tidak hanya lewat warna (solid vs putus-putus, label langsung pada titik terakhir). Setiap grafik punya ringkasan teks atau tabel tersembunyi untuk pembaca layar, dan sel heatmap punya `aria-label`.
- Jika check-in kurang dari 2, tampilkan kerangka grafik dan ajakan "Isi check-in minggu pertama", bukan grafik kosong.

## Aturan umum
- Teks UI Bahasa Indonesia, tanpa istilah ML (ikuti gym-user-facing-revision).
- Jangan mengubah styling global atau komponen yang sudah ada kecuali diminta; pakai ulang komponen Blade yang ada.
- Jangan menambah package tanpa menyebut alasannya.
- Setelah tiap tahap, jalankan `php artisan test` dan catat progres di `docs/progress.md` (selesai dan langkah berikutnya).
