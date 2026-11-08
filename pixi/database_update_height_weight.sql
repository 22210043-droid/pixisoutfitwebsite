-- Jalankan satu kali pada database `putriti` melalui phpMyAdmin.
-- Menambahkan data antropometri yang diinput pengguna.

ALTER TABLE uploads
    ADD COLUMN IF NOT EXISTS height_cm DECIMAL(5,1) NULL AFTER file_path,
    ADD COLUMN IF NOT EXISTS weight_kg DECIMAL(5,1) NULL AFTER height_cm;

-- Kategori body shape baru yang digunakan sistem:
-- rectangle, triangle, inverted triangle, hourglass
--
-- Data/rule lama tidak dihapus otomatis agar tidak merusak isi database yang sudah ada.
-- Sistem masih mempunyai fallback kompatibilitas untuk rule `pear` dan `upper-body`.
