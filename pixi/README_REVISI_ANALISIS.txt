REVISI ANALISIS OTOMATIS - PUTRIAI
===================================

Yang diubah:
1. upload.php
   - Tambah input tinggi badan (cm) dan berat badan (kg).
   - Memuat TensorFlow.js, MoveNet pose detection, dan BodyPix dari CDN.

2. assets/js/analyze5.js
   - Analisis foto aktif otomatis.
   - MoveNet mendeteksi titik bahu, pinggul, wajah.
   - Skin tone diestimasi dari area wajah dengan rule warna.
   - BodyPix membantu mengestimasi lebar pinggang.
   - Body shape hanya menghasilkan 4 kategori:
     rectangle, triangle, inverted triangle, hourglass.

3. save_result.php
   - Menyimpan tinggi dan berat badan ke MySQL.
   - Validasi body shape hanya 4 kategori di atas.
   - Foto masih disimpan lokal pada folder uploads/ (AWS BELUM diterapkan).

4. result.php + halaman admin
   - Disesuaikan dengan 4 kategori body shape.
   - Hasil tinggi/berat/BMI ditampilkan di halaman hasil.

WAJIB SEBELUM TEST:
1. Buka phpMyAdmin.
2. Pilih database `putriti`.
3. Jalankan file: database_update_height_weight.sql
4. Pastikan internet aktif ketika membuka halaman upload karena model TensorFlow.js dimuat dari CDN.

SARAN FOTO UNTUK PENGUJIAN:
- Satu orang saja.
- Menghadap kamera.
- Bahu dan pinggul terlihat.
- Lengan tidak menutupi pinggang.
- Pencahayaan wajah cukup dan tidak memakai filter warna kuat.

CATATAN:
Klasifikasi ini bersifat estimasi visual berbasis aturan untuk kebutuhan prototipe penelitian.
AWS belum disentuh sesuai permintaan.
