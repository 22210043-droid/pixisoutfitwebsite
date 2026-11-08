<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Style Coach - Upload Foto</title>
    <link rel="shortcut icon" href="assets/favi.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/upload6.css">
</head>

<body>
    <nav class="navbar">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <a class="navbar-brand" href="./">Style Coach</a>
            <a href="./" class="btn-back">← Back</a>
        </div>
    </nav>

    <main class="container fadeIn">
        <h1>Style Coach</h1>
        <p class="page-subtitle">Unggah foto tubuh, lalu isi tinggi dan berat badan untuk analisis otomatis.</p>

        <div class="upload-box">
            <label class="upload-label" for="photoUpload">📸 Pilih Foto</label>
            <input type="file" id="photoUpload" name="photo" accept="image/jpeg,image/png,image/webp">

            <div id="preview">
                <div class="scan-text preview-placeholder">Preview foto akan muncul di sini</div>
            </div>

            <div class="physical-inputs">
                <div class="physical-field">
                    <label for="heightInput">Tinggi Badan</label>
                    <div class="input-with-unit">
                        <input type="number" id="heightInput" min="120" max="230" step="0.1" placeholder="Contoh: 165"
                            required>
                        <span>cm</span>
                    </div>
                </div>

                <div class="physical-field">
                    <label for="weightInput">Berat Badan</label>
                    <div class="input-with-unit">
                        <input type="number" id="weightInput" min="30" max="250" step="0.1" placeholder="Contoh: 55"
                            required>
                        <span>kg</span>
                    </div>
                </div>
            </div>

            <div class="physical-inputs">
                <div class="physical-field">
                    <label for="chestInput">Lingkar Dada / Upper Torso</label>
                    <div class="input-with-unit">
                        <input type="number" id="chestInput" min="50" max="200" step="0.1" placeholder="Contoh: 90">
                        <span>cm</span>
                    </div>
                </div>

                <div class="physical-field">
                    <label for="waistMeasureInput">Lingkar Pinggang</label>
                    <div class="input-with-unit">
                        <input type="number" id="waistMeasureInput" min="40" max="200" step="0.1"
                            placeholder="Contoh: 70">
                        <span>cm</span>
                    </div>
                </div>

                <div class="physical-field">
                    <label for="hipInput">Lingkar Pinggul</label>
                    <div class="input-with-unit">
                        <input type="number" id="hipInput" min="50" max="200" step="0.1" placeholder="Contoh: 92">
                        <span>cm</span>
                    </div>
                </div>
            </div>

            <div class="photo-guide">
                Gunakan foto satu orang, tampak tubuh bagian atas sampai pinggul, posisi menghadap kamera, dan
                pencahayaan cukup.
            </div>

            <div id="analysisStatus" class="analysis-status" aria-live="polite"></div>
            <button id="analyzeBtn" type="button">🔍 Analisis Sekarang</button>
        </div>
    </main>

    <script>
    window.addEventListener("scroll", () => {
        const nav = document.querySelector(".navbar");
        nav.classList.toggle("scrolled", window.scrollY > 20);
    });
    </script>

    <!-- Loader library analisis. Mencoba jsDelivr lalu UNPKG jika CDN pertama gagal. -->
    <script src="assets/js/analysis-loader.js"></script>
</body>

</html>