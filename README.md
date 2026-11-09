# Sistem Rekomendasi Outfit Berbasis Analisis Warna Kulit dan Bentuk Tubuh

Aplikasi web yang menganalisis **warna kulit** dan **bentuk tubuh** dari satu foto, lalu menampilkan **palet warna outfit** dan **panduan potongan (Do's & Don'ts)** yang sesuai.

> Proyek skripsi. Lihat bagian [Keterbatasan](#keterbatasan) sebelum menggunakan hasilnya sebagai acuan.

<!-- TODO: tambahkan screenshot -->
<!-- ![Demo](docs/screenshot.png) -->

---

## Fitur

- Upload foto (JPG, PNG, WebP, maks. 8 MB) beserta tinggi dan berat badan
- Deteksi titik tubuh (pose keypoints) langsung di browser
- Segmentasi tubuh untuk mengukur lebar bahu, pinggang, dan pinggul
- Klasifikasi bentuk tubuh: `triangle`, `inverted triangle`, `hourglass`, `rectangle`
- Klasifikasi warna kulit: `warm`, `cool`, `neutral-warm`, `neutral-cool`, `neutral`
- Fallback otomatis ke estimasi berbasis pose jika segmentasi gagal (ditandai peringatan di halaman hasil)
- Halaman hasil dengan URL unik (slug acak 32 karakter hex)
- Rekomendasi palet dan panduan potongan dikelola lewat database

---

## Cara Kerja

```
index.html
   â  (user upload foto + tinggi + berat)
   â¼
analyze-app.js  ââ orkestrator UI & alur analisis (di browser)
   ââ PoseDetector          â keypoints tubuh
   ââ SkinToneAnalyzer      â warna kulit
   ââ Segmentation          â mask tubuh (opsional)
   ââ BodyShapeAnalyzer     â lebar upper/waist/lower â bentuk tubuh
   â
   â¼  POST (multipart/form-data)
save_result.php ââ validasi ulang di server, simpan foto + record ke DB
   â
   â¼  redirect dengan slug
result.php?slug=... ââ JOIN hasil dengan tabel rekomendasi, tampilkan palet & panduan
```

Seluruh analisis dilakukan di **sisi klien**. Server hanya memvalidasi dan menyimpan hasil.

---

## Struktur Proyek

```
.
âââ index.html                  # Halaman upload & analisis
âââ analyze-app.js              # Orkestrator UI + alur analisis
âââ body-shape-analyzer.js      # Pengukuran proporsi & klasifikasi bentuk tubuh
âââ pose-detector.js            # Wrapper deteksi keypoints
âââ segmentation.js             # Wrapper segmentasi tubuh
âââ skin-tone-analyzer.js       # Analisis warna kulit
âââ save_result.php             # Endpoint penyimpanan hasil (JSON)
âââ result.php                  # Halaman hasil & rekomendasi
âââ db_config.php               # Koneksi database (JANGAN di-commit)
âââ schema.sql                  # Skema database
âââ uploads/                    # Foto yang diupload (dibuat otomatis)
```

<!-- TODO: sesuaikan dengan struktur repo yang sebenarnya -->

---

## Teknologi

| Bagian | Teknologi |
|---|---|
| Frontend | HTML, CSS, JavaScript (non-module) |
| Deteksi pose | MoveNet <!-- TODO: konfirmasi library/versi --> |
| Segmentasi | BodyPix <!-- TODO: konfirmasi library/versi --> |
| Backend | PHP 8.0+ (memakai `match` expression) |
| Database | MySQL / MariaDB (memakai `GROUP_CONCAT` dan kolom `JSON`) |

---

## Instalasi

### Prasyarat

- PHP 8.0 atau lebih baru dengan ekstensi `pdo_mysql` dan `fileinfo`
- MySQL 5.7+ / MariaDB 10.2+
- Web server (Apache/Nginx) atau XAMPP/Laragon untuk lokal

### Langkah

1. **Clone repo**
   ```bash
   git clone https://github.com/<username>/<nama-repo>.git
   cd <nama-repo>
   ```

2. **Buat database dan import skema**
   ```bash
   mysql -u root -p -e "CREATE DATABASE nama_database CHARACTER SET utf8mb4;"
   mysql -u root -p nama_database < schema.sql
   ```

3. **Konfigurasi koneksi database**

   Salin template lalu isi kredensial Anda:
   ```bash
   cp db_config.example.php db_config.php
   ```
   `db_config.php` harus menyediakan fungsi `get_db_connection()` yang mengembalikan objek `PDO`
   (dengan `PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION` dan `FETCH_ASSOC` sebagai default).

4. **Pastikan folder `uploads/` bisa ditulis oleh web server**
   ```bash
   mkdir -p uploads && chmod 755 uploads
   ```

5. **Jalankan** lewat web server, lalu buka `http://localhost/<nama-repo>/index.html`.

   > Aplikasi tidak bisa dibuka lewat `file://` karena membutuhkan PHP dan model ML dimuat via jaringan.

---

## Database

Tiga tabel utama (detail lengkap ada di `schema.sql`):

| Tabel | Fungsi |
|---|---|
| `analysis_results` | Hasil analisis per upload: slug, path foto, tinggi, berat, BMI, skin tone, body shape, sumber pengukuran, flag low-confidence, `metrics_json`, `dataset_type` |
| `outfit_recommendations` | Teks Do's & Don'ts per kombinasi `skin_tone` Ã `body_shape` |
| `recommendation_colors` | Daftar warna (nama, hex, urutan) untuk tiap rekomendasi |

Halaman hasil memakai `LEFT JOIN`, jadi hasil analisis tetap tampil meski rekomendasi untuk kombinasi tertentu belum diisi admin.

**Nilai kategori (harus cocok persis dengan ENUM di database):**

- `skin_tone`: `warm`, `cool`, `neutral-warm`, `neutral-cool`, `neutral`
- `body_shape`: `triangle`, `inverted triangle`, `hourglass`, `rectangle`

Total 5 Ã 4 = **20 kombinasi** yang perlu diisi di `outfit_recommendations` agar semua hasil punya rekomendasi.

---

## Logika Klasifikasi Bentuk Tubuh

Lebar diukur pada tiga zona torso (upper, waist, lower), lalu:

| Bentuk | Kondisi |
|---|---|
| Triangle | `upper / lower â¤ 0.94` |
| Inverted triangle | `upper / lower â¥ 1.12` |
| Hourglass | selisih frame â¤ 0.09 **dan** `waist / rata-rata frame â¤ 0.86` |
| Rectangle | selain kondisi di atas |

Dua jalur pengukuran:

1. **Segmentasi + pose** (utama): lebar diukur langsung dari mask tubuh pada beberapa baris sampel per zona.
2. **Pose-only fallback**: lebar pinggang diestimasi dengan interpolasi antara lebar bahu dan pinggul. Hasil ditandai `low_confidence_fallback` dan peringatan ditampilkan di halaman hasil.

---

## Keamanan

- Validasi **ulang di server** untuk MIME type asli file (`finfo`), ukuran file, rentang tinggi/berat, dan nilai kategori. Validasi di JS tidak dianggap cukup.
- Query memakai *prepared statement*.
- Output di `result.php` di-escape dengan `htmlspecialchars`.
- Slug dibuat dengan `random_bytes(16)` dan divalidasi regex sebelum query.
- Foto dihapus kembali jika insert ke database gagal.

---

## Keterbatasan

- **Threshold klasifikasi bersifat heuristik**, belum dikalibrasi terhadap dataset berlabel. Akurasi belum terukur secara statistik.
- Jalur fallback **tidak mengukur lekukan pinggang secara langsung**, sehingga akurasinya secara fundamental lebih rendah daripada jalur segmentasi.
- Hasil sangat bergantung pada kualitas foto: pencahayaan, latar polos, pose tegak menghadap depan, bahu sampai pinggul terlihat, hanya satu orang.
- Klasifikasi warna kulit dipengaruhi pencahayaan dan white balance kamera.
- Rekomendasi bersifat saran umum, bukan penilaian profesional.

## Privasi

Foto pengguna disimpan di folder `uploads/` dan dapat diakses lewat URL langsung. Meski nama file acak, **siapa pun yang punya URL bisa melihat fotonya**. Sebelum dipakai di luar lingkungan uji:

- Tambahkan persetujuan eksplisit pengguna (consent) sebelum upload
- Pertimbangkan menyimpan foto di luar web root dan menyajikannya lewat skrip dengan kontrol akses
- Sediakan mekanisme penghapusan data

---

## Roadmap

- [ ] Kalibrasi threshold dengan dataset berlabel
- [ ] Evaluasi akurasi (confusion matrix) untuk bentuk tubuh dan warna kulit
- [ ] Halaman admin untuk mengelola rekomendasi dan palet
- [ ] Penghapusan data / retensi foto otomatis

<!-- TODO: sesuaikan roadmap -->

---

## Lisensi

<!-- TODO: pilih lisensi, mis. MIT -->
Lisensi belum ditentukan.

## Penulis

<!-- TODO: isi nama, kampus, kontak -->
- **Putri Chalista Tumiwa** â Skripsi, Program Studi Teknik Informatika, Universitas Negeri Manado
