---
name: dark-cosmic-ui-ux
description: Sistem desain UI/UX tema gelap untuk website SaaS/AI modern, gabungan dua referensi - hero "cosmic glow" (latar hitam, cahaya biru lengkung planet, mockup miring glassmorphism, headline dengan kata sorot) dan sistem section ala template "Dope.ai" (latar navy gelap, aksen indigo, kartu bergaris tipis, badge ikon berwarna, FAQ accordion, pricing, integrasi, mockup dashboard). Gunakan skill ini setiap kali pengguna meminta membuat atau memperbaiki tampilan website, landing page, hero, navbar, kartu fitur, form, pricing, FAQ, dashboard, atau komponen UI bergaya gelap, futuristik, premium, minimalis, atau "seperti gambar referensi", termasuk untuk website rekomendasi workout. Skill ini hanya soal visual dan UX; logika ML dan konsep produk ada di skill workout-ml-website-concept.
---

# Dark SaaS UI/UX (Cosmic Hero + Dope.ai Sections)

Karakter: gelap, tenang, premium, banyak ruang kosong. Satu warna aksen (indigo), tipografi bersih, kartu datar bergaris tipis, dan satu momen visual dramatis di hero.

## Pembagian peran dua referensi
- **Hero (bagian paling atas):** gaya cosmic glow - glow biru, mockup miring 3D, headline dengan 2 kata sorot.
- **Semua section di bawahnya:** gaya Dope.ai - kartu gelap, aksen indigo, layout terpusat, badge ikon berwarna.
- Aksen hero dan aksen section harus disatukan ke satu warna: `--accent` indigo-biru. Jangan pakai dua biru berbeda.

## Design tokens (perkiraan dari gambar, boleh disesuaikan)
```css
:root{
  --bg:#05050c;              /* latar halaman */
  --bg-section:#0a0a14;      /* panel/section */
  --card:#0e0e1b;            /* kartu */
  --card-hover:#13132a;
  --border:rgba(255,255,255,.08);
  --border-active:rgba(99,102,241,.7);   /* kartu terpilih: border indigo + glow */
  --text:#f5f6fb;
  --muted:#8e92ab;           /* paragraf: abu kebiruan */
  --accent:#6366f1;          /* indigo: tombol, eyebrow, link */
  --accent-soft:#8fb4ff;     /* kata sorot di headline hero */
  --glow:rgba(99,102,241,.35);
  --radius-card:16px; --radius-btn:8px; --radius-pill:999px;
  /* warna badge ikon */
  --ic-green:#3fbf8f; --ic-orange:#f5a03b; --ic-purple:#a56bf0;
  --ic-pink:#e754b8;  --ic-teal:#3bb8b0;  --ic-red:#ef5a5a;
}
```
Font: Inter / Geist / Manrope. Headline 600-700 dengan `letter-spacing:-.03em`, ukuran 40-56px desktop; paragraf 14-15px `--muted`, `line-height:1.6`. Teks section dibatasi lebar ~60ch dan dipusatkan.

## Pola section (urutan yang disarankan)
1. **Navbar:** logo kiri, link kecil di tengah (Features, Pages, Services, Blog, Pricing), tombol "Sign up" kecil `--accent` di kanan (radius 8px). Untuk hero cosmic: navbar bisa berupa pill transparan `backdrop-filter:blur(16px)`.
2. **Hero cosmic:** kolom kiri = headline 3 baris + paragraf + dua tombol (utama putih/indigo, sekunder abu gelap); kolom kanan = mockup miring + glow. Detail CSS di bawah.
3. **Eyebrow + headline terpusat:** label kecil berwarna `--accent` dengan garis tipis di kiri-kanan ("— Integration —"), headline 2 baris terpusat, paragraf muted, lalu satu tombol kecil indigo dengan panah.
4. **Grid 3 kartu fitur:** tiap kartu = badge ikon bulat berwarna, judul, deskripsi 2-3 baris, link "Get Started >" kecil berwarna aksen. Satu kartu bisa aktif (border indigo + glow) sebagai penekanan.
5. **Mockup dashboard:** panel gelap berisi sidebar kiri, search bar, dan grid daftar item dengan ikon bulat berwarna; bagian bawah memudar (mask gradient) ke latar. Untuk proyek ini isinya = form profil + hasil rekomendasi.
6. **Pricing / info dua kolom:** kiri kartu (harga besar, select, tombol penuh, daftar fitur dengan ceklis), kanan teks judul besar + daftar "Ideal For" dengan ceklis.
7. **FAQ accordion:** judul terpusat 2 baris, baris pertanyaan bernomor dalam kotak gelap bergaris tipis, panah di kanan, satu baris terbuka menampilkan jawaban. Latar section punya glow ungu-biru lembut di atas.
8. **Section konten dengan visual:** gambar/render 3D glass di dalam kartu radius besar, diikuti subjudul + paragraf.

## Hero cosmic (CSS inti)
```css
.hero{position:relative;overflow:hidden;background:var(--bg)}
.hero::before{content:"";position:absolute;right:-10%;top:5%;width:70vw;height:70vw;border-radius:50%;
  background:radial-gradient(circle at 30% 30%,rgba(143,180,255,.5),rgba(99,102,241,.18) 35%,transparent 62%);
  filter:blur(30px);pointer-events:none}
.device{transform:perspective(1400px) rotateY(-18deg) rotateX(6deg) rotateZ(-3deg);
  background:linear-gradient(160deg,rgba(255,255,255,.08),rgba(255,255,255,.02));
  border:1px solid var(--border);border-radius:var(--radius-card);
  box-shadow:0 40px 120px var(--glow), inset 0 1px 0 rgba(255,255,255,.1)}
.hl{color:var(--accent-soft)}  /* 2 kata sorot di headline */
```
Bintang: titik 1-2px acak, opasitas 0.2-0.7, jumlah sedikit.

## Komponen
- **Kartu:** `background:var(--card);border:1px solid var(--border);border-radius:16px;padding:24px`. Hover: `--card-hover` + border sedikit lebih terang. Aktif: `border-color:var(--border-active);box-shadow:0 0 40px var(--glow)`.
- **Badge ikon:** lingkaran 40px, warna dari token `--ic-*`, ikon putih di tengah (pakai ikon SVG, bukan emoji).
- **Tombol:** utama = `--accent` teks putih (di section) atau putih teks hitam (di hero); sekunder = abu gelap. Tinggi 36-44px, transisi 150ms, hover naik 1px + glow.
- **Input form:** latar `--card`, border tipis, fokus = ring `--accent`. Pilihan tujuan (cutting/bulking/strength) = segmented pill.
- **Hasil prediksi:** kartu besar, kategori sebagai headline, bar keyakinan berwarna aksen, 3 poin ringkas program.
- **Tabel perbandingan model:** gelap bergaris tipis, nilai terbaik per kolom diberi `--accent`. Confusion matrix = heatmap gradasi indigo (hindari merah-hijau).
- **Ceklis:** ikon centang kecil berlingkar tipis, teks muted.

## Gerak
Fade-up saat scroll (opacity + translateY 12px, 500ms), parallax halus pada mockup, glow berdenyut sangat pelan. Hormati `prefers-reduced-motion`.

## UX dan aksesibilitas
- Kontras teks minimal 4.5:1; `--muted` jangan lebih gelap dari `#8e92ab` di atas latar gelap.
- Target sentuh minimal 44px; fokus keyboard selalu terlihat; accordion memakai `button` + `aria-expanded`.
- Satu CTA utama per layar. Maksimal satu warna aksen.
- Responsif: grid 3 kolom jadi 1 kolom di mobile, mockup turun di bawah teks dan tidak dimiringkan ekstrem, navbar jadi menu ringkas.
- Rancang state kosong, loading (skeleton gelap), dan error.

## Yang dihindari
Gradien ungu-pink generik, bayangan hitam pekat di atas latar hitam, border menyala di semua kartu, emoji sebagai ikon UI, paragraf panjang di hero, lebih dari satu warna aksen utama.

## Penerapan di Laravel (Blade + Vite + Tailwind)
- Taruh design tokens di `resources/css/app.css` sebagai CSS variables (`:root{...}`), lalu petakan ke Tailwind lewat `@theme` (Tailwind v4) atau `tailwind.config.js` (v3), tergantung versi yang terpasang di `package.json`; cek dulu sebelum menulis.
- Layout utama di `resources/views/layouts/app.blade.php` (navbar pill + `@yield('content')`/`{{ $slot }}`), tiap section di partial atau komponen Blade (`resources/views/components/`).
- Komponen yang dibuat: `x-button`, `x-card`, `x-badge-icon`, `x-segmented`, `x-faq-item`, `x-stat`. Pakai ulang, jangan menyalin kelas yang sama berulang-ulang.
- Interaksi ringan (accordion FAQ, segmented pill) dengan Alpine.js atau JS vanilla, tanpa library berat.
- Tampilkan error validasi Laravel (`$errors`) dengan gaya input gelap + pesan merah lembut di bawah field; pertahankan input lama dengan `old()`.
- Grafik perbandingan model: Chart.js via npm (`npm i chart.js`) atau gambar PNG dari `ml-service/artifacts`, gaya mengikuti token (aksen indigo, tanpa merah-hijau).
- Jalankan `npm run dev` saat mengembangkan dan `npm run build` sebelum deploy.
