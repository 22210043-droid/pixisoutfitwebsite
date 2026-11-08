PATCH V2 - FIX BODYPix

1. Copy/replace:
   - upload.php
   - assets/js/analysis-loader.js
   - assets/js/analyze5.js
2. Buka http://localhost:8080/putriai/upload.php (sesuaikan folder/port Anda)
3. Tekan Ctrl+F5.
4. Console yang diharapkan:
   - TensorFlow.js berhasil dimuat
   - MoveNet / Pose Detection berhasil dimuat
   - BodyPix berhasil dimuat
5. Pilih foto satu orang yang bahu, pinggang, dan pinggulnya terlihat.
6. Isi tinggi dan berat, lalu Analisis Sekarang.

Perubahan penting:
- URL BodyPix diperbaiki mengikuti browser bundle resmi package, tanpa /dist/body-pix.min.js.
- ensureModels tidak lagi membuat ulang MoveNet hanya karena BodyPix opsional belum tersedia.
