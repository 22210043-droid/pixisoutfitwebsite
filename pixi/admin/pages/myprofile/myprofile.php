    <?php

    $user = $conn->query("SELECT * FROM users WHERE id = '$id'")->fetch_assoc();
    ?>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h4 class="fw-bold mb-4">
                <iconify-icon icon="solar:user-id-line-duotone" width="24"></iconify-icon> My Profile
            </h4>

            <form action="?q=update_profile" method="POST" enctype="multipart/form-data" class="row g-4">
                <input type="hidden" name="id" value="<?= $user['id']; ?>">

                <!-- Foto Profil -->
                <div class="col-md-4 text-center">
                    <img src="./<?= htmlspecialchars($user['path_photo']); ?>" id="previewImage"
                        class="rounded-circle shadow-sm mb-3" width="160" height="160"
                        style="object-fit: cover; border: 3px solid #ddd;">
                    <div>
                        <label class="btn btn-outline-primary btn-sm">
                            <iconify-icon icon="solar:camera-line-duotone" width="18"></iconify-icon> Ganti Foto
                            <input type="file" name="path_photo" class="d-none" accept="image/*"
                                onchange="previewProfile(event)">
                        </label>
                    </div>
                </div>

                <!-- Data Profil -->
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control"
                            value="<?= htmlspecialchars($user['name']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" class="form-control" value="<?= htmlspecialchars($user['email']); ?>"
                            disabled>
                    </div>
                    <button type="submit" class="btn btn-success">
                        <iconify-icon icon="solar:check-circle-line-duotone" width="20"></iconify-icon> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function previewProfile(event) {
            const reader = new FileReader();
            reader.onload = function() {
                document.getElementById('previewImage').src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>