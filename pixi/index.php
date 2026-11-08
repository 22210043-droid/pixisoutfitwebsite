<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>AI Style Coach</title>
    <link rel="shortcut icon" href="assets/fav.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/index8.css">

    <!-- AOS CSS -->
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">

</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container d-flex align-items-center justify-content-between">
            <!-- Logo -->
            <a class="navbar-brand mb-0 d-flex align-items-center" href="">
                <img src="assets/logopixiss.png" alt="" height="60" class="me-2">
            </a>


            <!-- Toggle Button (Mobile) -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Menu Tengah -->
            <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                <ul class="navbar-nav gap-4">
                    <li class="nav-item"><a class="nav-link" href="#">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#features">Features</a></li>
                    <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
                </ul>
            </div>

            <!-- Tombol Kanan -->
            <div class="d-none d-lg-block">
                <a href="#features" class="btn btn-gradient ms-3">Get Started</a>
            </div>
        </div>
    </nav>


    <?php
    include 'conf/conf.php';

    // Ambil data hero section (karena hanya satu record)
    $query = "SELECT * FROM hero_section LIMIT 1";
    $result = mysqli_query($conn, $query);
    $hero = mysqli_fetch_assoc($result);
    ?>

    <!-- Hero Section -->
    <section id="hero" class="mb-5" data-aos="fade-up">
        <h1 id="heroTitle">
            <?= htmlspecialchars($hero['title']); ?>
        </h1>

        <p id="heroText">
            <?= nl2br(htmlspecialchars($hero['subtitle'])); ?>
        </p>

        <!-- Tombol sejajar -->
        <div class="hero-buttons">
            <?php if (!empty($hero['btn_start_text']) && !empty($hero['btn_start_link'])): ?>
                <a href="<?= htmlspecialchars($hero['btn_start_link']); ?>" class="btn-gradient">
                    <?= htmlspecialchars($hero['btn_start_text']); ?>
                </a>
            <?php endif; ?>

            <?php if (!empty($hero['btn_video_text']) && !empty($hero['btn_video_link'])): ?>
                <a href="<?= htmlspecialchars($hero['btn_video_link']); ?>" class="btn-outline">
                    <?= htmlspecialchars($hero['btn_video_text']); ?>
                </a>
            <?php endif; ?>
        </div>
    </section>


    <?php
    // Ambil semua data fitur dari tabel
    $features = [];
    $result = $conn->query("SELECT * FROM features_section ORDER BY id ASC");
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $features[] = $row;
        }
    }
    ?>
    <!-- ===== FEATURE + ABOUT SECTION ===== -->
    <section id="features" class="features text-center" data-aos="fade-up">
        <div class="container">
            <!-- Tabs -->
            <div class="feature-tabs" data-aos="fade-down" data-aos-delay="200">
                <?php foreach ($features as $index => $f): ?>
                    <button class="tab-btn <?= $index === 0 ? 'active' : ''; ?>"
                        data-tab="<?= htmlspecialchars($f['tab_key']); ?>">
                        <?= htmlspecialchars($f['tab_title']); ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <!-- Description -->
            <?php if (!empty($features)): ?>
                <p id="featureDesc" class="feature-desc" data-aos="fade-up" data-aos-delay="400">
                    <?= htmlspecialchars($features[0]['description']); ?>
                </p>
            <?php else: ?>
                <p class="feature-desc text-muted">Belum ada data fitur.</p>
            <?php endif; ?>

            <!-- Image Preview -->
            <div id="featureImages" class="feature-images" data-aos="zoom-in" data-aos-delay="600">
                <?php if (!empty($features)): ?>
                    <?php if (!empty($features[0]['image_before'])): ?>
                        <img src="admin/<?= htmlspecialchars($features[0]['image_before']); ?>" alt="Before">
                    <?php endif; ?>
                    <?php if (!empty($features[0]['image_reference'])): ?>
                        <img src="admin/<?= htmlspecialchars($features[0]['image_reference']); ?>" alt="Reference">
                    <?php endif; ?>
                    <?php if (!empty($features[0]['image_after'])): ?>
                        <img src="admin/<?= htmlspecialchars($features[0]['image_after']); ?>" alt="After">
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
        <!-- ===== ABOUT SECTION ===== -->
        <div id="about" class="container about-combined" data-aos="fade-up" data-aos-delay="200">
            <?php
            $about = $conn->query("SELECT * FROM about_section LIMIT 1")->fetch_assoc();
            if ($about):
            ?>
                <div class="row align-items-center gy-5">
                    <!-- Left Text -->
                    <div class="col-lg-6 text-start" data-aos="fade-right" data-aos-delay="300">
                        <p class="about-kicker"><?= htmlspecialchars($about['subtitle']); ?></p>
                        <h2 class="about-title"><?= htmlspecialchars($about['title']); ?></h2>
                        <p class="about-desc"><?= nl2br(htmlspecialchars($about['description'])); ?></p>

                        <div class="d-flex flex-wrap gap-3 mt-4">
                            <?php if (!empty($about['btn_primary_text'])): ?>
                                <a href="<?= htmlspecialchars($about['btn_primary_link']); ?>" class="btn-about">
                                    <?= htmlspecialchars($about['btn_primary_text']); ?> →
                                </a>
                            <?php endif; ?>
                            <?php if (!empty($about['btn_secondary_text'])): ?>
                                <a href="<?= htmlspecialchars($about['btn_secondary_link']); ?>" class="btn-about alt">
                                    <?= htmlspecialchars($about['btn_secondary_text']); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Right Images -->
                    <div class="col-lg-6" data-aos="fade-left" data-aos-delay="500">
                        <div class="about-image-box shadow-lg">
                            <?php if (!empty($about['image_main'])): ?>
                                <img src="admin/<?= htmlspecialchars($about['image_main']); ?>" alt="Main Image">
                            <?php endif; ?>
                            <?php if (!empty($about['image_secondary'])): ?>
                                <img src="admin/<?= htmlspecialchars($about['image_secondary']); ?>" alt="Secondary Image">
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <p class="text-center text-muted">Belum ada data About Section.</p>
            <?php endif; ?>
        </div>

    </section>

    <!-- ===== FOOTER ===== -->
    <footer class="footer" data-aos="fade-up" data-aos-delay="300">
        <p>✨ Created by <span>Pixis Lab</span> © 2025</p>
    </footer>


    <!-- AOS JS -->
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 1200, // durasi animasi dalam ms
            once: true, // animasi hanya sekali
            offset: 100, // jarak dari bawah viewport sebelum animasi muncul
        });
    </script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/index.js"></script>


    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const tabs = document.querySelectorAll(".tab-btn");
            const desc = document.getElementById("featureDesc");
            const images = document.getElementById("featureImages");

            const tabContent = <?= json_encode($features, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>;

            // Helper: prefix admin/ dan encode spasi/tanda kurung
            const resolveSrc = (path) => {
                if (!path) return "";
                if (path.startsWith("http")) return encodeURI(path);
                const prefixed = "admin/" + path.replace(/^\/+/, "");
                return encodeURI(prefixed);
            };

            tabs.forEach(tab => {
                tab.addEventListener("click", () => {
                    tabs.forEach(t => t.classList.remove("active"));
                    tab.classList.add("active");

                    const key = tab.getAttribute("data-tab");
                    const content = tabContent.find(item => item.tab_key === key);
                    if (!content) return;

                    desc.style.opacity = 0;
                    images.style.opacity = 0;

                    setTimeout(() => {
                        desc.textContent = content.description;

                        let imgsHTML = "";
                        if (content.image_before)
                            imgsHTML +=
                            `<img src="${resolveSrc(content.image_before)}" alt="Before">`;
                        if (content.image_reference)
                            imgsHTML +=
                            `<img src="${resolveSrc(content.image_reference)}" alt="Reference">`;
                        if (content.image_after)
                            imgsHTML +=
                            `<img src="${resolveSrc(content.image_after)}" alt="After">`;

                        images.innerHTML = imgsHTML;
                        desc.style.opacity = 1;
                        images.style.opacity = 1;
                    }, 400);
                });
            });
        });
    </script>

</body>

</html>