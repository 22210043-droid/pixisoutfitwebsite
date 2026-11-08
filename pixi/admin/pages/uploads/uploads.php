<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">
                <iconify-icon icon="solar:cloud-upload-line-duotone" width="24"></iconify-icon> Uploads
            </h4>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">
                <iconify-icon icon="solar:add-square-broken" width="18"></iconify-icon> Tambah Upload
            </button>
        </div>

        <div class="table-responsive">
            <table id="uploadsTable" class="table table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="text-center">No</th>
                        <th>Preview</th>
                        <th>Dataset Type</th>
                        <th>Tinggi</th>
                        <th>Berat</th>
                        <th>Skin Tone Label</th>
                        <th>Body Shape Label</th>
                        <th>Skin Tone (Hasil)</th>
                        <th>Body Shape (Hasil)</th>
                        <th>Slug</th>
                        <th>Tanggal Upload</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT * FROM uploads ORDER BY created_at DESC");
                    if ($result && $result->num_rows > 0) {
                        $no = 1;
                        while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td class="text-center fw-semibold"><?= $no++; ?></td>

                        <?php
                                $filePath = $row['file_path'];

                                if (file_exists("../" . $filePath)) {
                                    $imgPath = "../" . $filePath;
                                } else {
                                    $imgPath = "./" . $filePath;
                                }
                                ?>

                        <td>
                            <img src="<?= htmlspecialchars($imgPath); ?>" width="80" class="rounded shadow-sm border">
                        </td>

                        <td>
                            <?php
                                    $datasetType = strtolower($row['dataset_type'] ?? 'training');
                                    $datasetBadgeClass = $datasetType === 'testing'
                                        ? 'bg-warning-subtle text-warning'
                                        : 'bg-info-subtle text-info';
                                    ?>
                            <span class="badge <?= $datasetBadgeClass; ?> fw-semibold">
                                <?= ucfirst(htmlspecialchars($datasetType)); ?>
                            </span>
                        </td>

                        <td><?= !empty($row['height_cm']) ? htmlspecialchars($row['height_cm']) . ' cm' : '-'; ?></td>
                        <td><?= !empty($row['weight_kg']) ? htmlspecialchars($row['weight_kg']) . ' kg' : '-'; ?></td>

                        <td>
                            <span class="badge bg-secondary-subtle text-secondary fw-semibold">
                                <?= !empty($row['skin_tone_label']) ? htmlspecialchars($row['skin_tone_label']) : '-'; ?>
                            </span>
                        </td>

                        <td>
                            <span class="badge bg-dark-subtle text-dark fw-semibold">
                                <?= !empty($row['body_shape_label']) ? htmlspecialchars($row['body_shape_label']) : '-'; ?>
                            </span>
                        </td>

                        <td>
                            <span class="badge bg-primary-subtle text-primary fw-semibold">
                                <?= !empty($row['skin_tone']) ? htmlspecialchars($row['skin_tone']) : '-'; ?>
                            </span>
                        </td>

                        <td>
                            <span class="badge bg-success-subtle text-success fw-semibold">
                                <?= !empty($row['body_shape']) ? htmlspecialchars($row['body_shape']) : '-'; ?>
                            </span>
                        </td>

                        <td><code><?= htmlspecialchars($row['slug']); ?></code></td>
                        <td><?= date('d M Y H:i', strtotime($row['created_at'])); ?></td>

                        <td class="text-center">
                            <a href="../result.php?slug=<?= urlencode($row['slug']); ?>"
                                class="btn btn-sm btn-info text-white me-1">
                                <iconify-icon icon="solar:eye-broken"></iconify-icon>
                            </a>

                            <button class="btn btn-sm btn-warning me-1" data-bs-toggle="modal"
                                data-bs-target="#editModal<?= $row['id']; ?>">
                                <iconify-icon icon="solar:pen-broken"></iconify-icon>
                            </button>

                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                data-bs-target="#deleteModal<?= $row['id']; ?>">
                                <iconify-icon icon="solar:trash-bin-trash-broken"></iconify-icon>
                            </button>
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editModal<?= $row['id']; ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <form action="?q=edit_uploads&id=<?= $row['id']; ?>" method="post"
                                    enctype="multipart/form-data">
                                    <div class="modal-header">
                                        <h5 class="modal-title fw-bold">Edit Upload</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Dataset Type</label>
                                            <select name="dataset_type" class="form-control" required>
                                                <option value="training"
                                                    <?= (($row['dataset_type'] ?? '') === 'training') ? 'selected' : ''; ?>>
                                                    Training</option>
                                                <option value="testing"
                                                    <?= (($row['dataset_type'] ?? '') === 'testing') ? 'selected' : ''; ?>>
                                                    Testing</option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Skin Tone Label</label>
                                            <select name="skin_tone_label" class="form-control" required>
                                                <option value="warm"
                                                    <?= (($row['skin_tone_label'] ?? '') === 'warm') ? 'selected' : ''; ?>>
                                                    Warm</option>
                                                <option value="cool"
                                                    <?= (($row['skin_tone_label'] ?? '') === 'cool') ? 'selected' : ''; ?>>
                                                    Cool</option>
                                                <option value="neutral"
                                                    <?= (($row['skin_tone_label'] ?? '') === 'neutral') ? 'selected' : ''; ?>>
                                                    Neutral</option>
                                                <option value="neutral-warm"
                                                    <?= (($row['skin_tone_label'] ?? '') === 'neutral-warm') ? 'selected' : ''; ?>>
                                                    Neutral-Warm</option>
                                                <option value="neutral-cool"
                                                    <?= (($row['skin_tone_label'] ?? '') === 'neutral-cool') ? 'selected' : ''; ?>>
                                                    Neutral-Cool</option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Body Shape Label</label>
                                            <select name="body_shape_label" class="form-control" required>
                                                <option value="rectangle"
                                                    <?= (($row['body_shape_label'] ?? '') === 'rectangle') ? 'selected' : ''; ?>>
                                                    Rectangle</option>
                                                <option value="triangle"
                                                    <?= in_array(($row['body_shape_label'] ?? ''), ['triangle', 'pear'], true) ? 'selected' : ''; ?>>
                                                    Triangle</option>
                                                <option value="inverted triangle"
                                                    <?= in_array(($row['body_shape_label'] ?? ''), ['inverted triangle', 'inverted-triangle', 'upper-body'], true) ? 'selected' : ''; ?>>
                                                    Inverted Triangle</option>
                                                <option value="hourglass"
                                                    <?= (($row['body_shape_label'] ?? '') === 'hourglass') ? 'selected' : ''; ?>>
                                                    Hourglass</option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Skin Tone (Hasil Sistem)</label>
                                            <input type="text" name="skin_tone" class="form-control"
                                                value="<?= htmlspecialchars($row['skin_tone'] ?? ''); ?>"
                                                placeholder="contoh: warm, cool, neutral">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Body Shape (Hasil Sistem)</label>
                                            <input type="text" name="body_shape" class="form-control"
                                                value="<?= htmlspecialchars($row['body_shape'] ?? ''); ?>"
                                                placeholder="contoh: rectangle, triangle, inverted triangle, hourglass">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Ganti Foto (Opsional)</label>
                                            <input type="file" name="file_path" class="form-control">
                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-warning text-white">Simpan
                                            Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Delete Modal -->
                    <div class="modal fade" id="deleteModal<?= $row['id']; ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title fw-bold">Konfirmasi Hapus</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body text-center">
                                    <p>Apakah kamu yakin ingin menghapus data ini?</p>
                                    <img src="../<?= htmlspecialchars($row['file_path']); ?>" width="100"
                                        class="rounded mb-3">
                                </div>

                                <div class="modal-footer">
                                    <a href="?q=delete_uploads&id=<?= $row['id']; ?>" class="btn btn-danger">Hapus</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php }
                    } else { ?>
                    <tr>
                        <td colspan="12" class="text-center text-muted py-4">Belum ada data upload.</td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="?q=add_uploads" method="post" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Tambah Upload</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Upload Gambar</label>
                        <input type="file" name="file_path" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Dataset Type</label>
                        <select name="dataset_type" class="form-control" required>
                            <option value="training">Training</option>
                            <option value="testing">Testing</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Skin Tone Label</label>
                        <select name="skin_tone_label" class="form-control" required>
                            <option value="">-- Pilih Skin Tone Label --</option>
                            <option value="warm">Warm</option>
                            <option value="cool">Cool</option>
                            <option value="neutral">Neutral</option>
                            <option value="neutral-warm">Neutral-Warm</option>
                            <option value="neutral-cool">Neutral-Cool</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Body Shape Label</label>
                        <select name="body_shape_label" class="form-control" required>
                            <option value="">-- Pilih Body Shape Label --</option>
                            <option value="rectangle">Rectangle</option>
                            <option value="triangle">Triangle</option>
                            <option value="inverted triangle">Inverted Triangle</option>
                            <option value="hourglass">Hourglass</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Skin Tone (Hasil Sistem)</label>
                        <input type="text" name="skin_tone" class="form-control"
                            placeholder="contoh: warm, cool, neutral">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Body Shape (Hasil Sistem)</label>
                        <input type="text" name="body_shape" class="form-control"
                            placeholder="contoh: rectangle, triangle, inverted triangle, hourglass">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>