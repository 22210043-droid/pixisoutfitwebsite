<!-- Row 1: Dashboard Summary -->
<div class="row">
    <!-- Uploads -->
    <div class="col-lg-3 col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="fw-semibold mb-2">Total Uploads</h5>
                    <?php
                    $uploads = $conn->query("SELECT COUNT(*) AS total FROM uploads")->fetch_assoc()['total'];
                    ?>
                    <h3 class="fw-bold text-primary mb-0"><?= $uploads ?></h3>
                    <p class="mb-0 text-muted fs-2">Data gambar pengguna</p>
                </div>
                <div class="icon-box bg-primary text-white rounded-circle p-3">
                    <iconify-icon icon="solar:cloud-upload-line-duotone" width="28" height="28"></iconify-icon>
                </div>
            </div>
        </div>
    </div>

    <!-- Recommendations -->
    <div class="col-lg-3 col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="fw-semibold mb-2">Recommendations</h5>
                    <?php
                    $recom = $conn->query("SELECT COUNT(*) AS total FROM recommendations")->fetch_assoc()['total'];
                    ?>
                    <h3 class="fw-bold text-success mb-0"><?= $recom ?></h3>
                    <p class="mb-0 text-muted fs-2">Hasil analisis gaya</p>
                </div>
                <div class="icon-box bg-success text-white rounded-circle p-3">
                    <iconify-icon icon="solar:palette-line-duotone" width="28" height="28"></iconify-icon>
                </div>
            </div>
        </div>
    </div>

    <!-- Outfit Rules -->
    <div class="col-lg-3 col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="fw-semibold mb-2">Outfit Rules</h5>
                    <?php
                    $rules = $conn->query("SELECT COUNT(*) AS total FROM outfit_rules")->fetch_assoc()['total'];
                    ?>
                    <h3 class="fw-bold text-warning mb-0"><?= $rules ?></h3>
                    <p class="mb-0 text-muted fs-2">Panduan warna & bentuk</p>
                </div>
                <div class="icon-box bg-warning text-white rounded-circle p-3">
                    <iconify-icon icon="solar:t-shirt-line-duotone" width="28" height="28"></iconify-icon>
                </div>
            </div>
        </div>
    </div>

    <!-- Users -->
    <div class="col-lg-3 col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="fw-semibold mb-2">Total Users</h5>
                    <?php
                    $users = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'];
                    ?>
                    <h3 class="fw-bold text-danger mb-0"><?= $users ?></h3>
                    <p class="mb-0 text-muted fs-2">Terdaftar di sistem</p>
                </div>
                <div class="icon-box bg-danger text-white rounded-circle p-3">
                    <iconify-icon icon="solar:user-id-line-duotone" width="28" height="28"></iconify-icon>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Row 2: Charts -->
<div class="row mt-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h5 class="card-title fw-semibold mb-4">Upload & Recommendation Trends</h5>
                <div id="chart-trend" style="height: 320px;"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h5 class="card-title fw-semibold mb-4">Data Composition</h5>
                <div id="chart-pie" style="height: 320px;"></div>
            </div>
        </div>
    </div>
</div>