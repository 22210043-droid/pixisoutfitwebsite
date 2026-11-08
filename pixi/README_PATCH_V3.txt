PATCH V3 - Body Segmentation

Replace file berikut ke project putriai:
- upload.php
- assets/js/analysis-loader.js
- assets/js/analyze5.js

Perubahan:
- Mengganti package @tensorflow-models/body-pix lama dengan @tensorflow-models/body-segmentation.
- MoveNet tetap menjadi detector pose utama.
- Body Segmentation dipakai untuk estimasi lebar pinggang agar kategori hourglass dapat dibedakan dari rectangle.
- Jika body segmentation gagal, analisis tetap berjalan dengan fallback MoveNet.

Setelah replace: buka upload.php lalu Ctrl+F5.
Console ideal:
[AI loader] TensorFlow.js berhasil dimuat.
[AI loader] MoveNet / Pose Detection berhasil dimuat.
[AI loader] Body Segmentation berhasil dimuat.
