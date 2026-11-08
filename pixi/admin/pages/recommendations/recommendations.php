<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">
                <iconify-icon icon="solar:sparkles-line-duotone" width="24"></iconify-icon> Recommendations
            </h4>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addRecommendationModal">
                <iconify-icon icon="solar:add-square-broken" width="18"></iconify-icon> Tambah Recommendation
            </button>
        </div>

        <div class="table-responsive">
            <table id="recommendationsTable" class="table table-striped align-middle">
                <thead class="table-light">
                    <tr class="text-center">
                        <th>No</th>
                        <th>Preview</th>
                        <th>Upload</th>
                        <th>Skin Tone</th>
                        <th>Body Shape</th>
                        <th>Title</th>
                        <th>Items</th>
                        <th>Tanggal Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("
                        SELECT 
                            r.*,
                            COALESCE(u.file_path, '') AS file_path,
                            COALESCE(u.slug, '') AS slug
                        FROM recommendations r
                        LEFT JOIN uploads u ON r.upload_id = u.id
                        ORDER BY r.created_at DESC
                    ");

                    if ($result && $result->num_rows > 0) {
                        $no = 1;
                        while ($row = $result->fetch_assoc()) {
                            $items = json_decode($row['items_json'], true);
                            $itemPreview = '-';

                            if (is_array($items)) {
                                $previewParts = [];

                                foreach ($items as $key => $value) {
                                    if (is_array($value)) {
                                        $previewParts[] = "<strong>" . htmlspecialchars($key) . ":</strong> " . htmlspecialchars(implode(', ', $value));
                                    } else {
                                        $previewParts[] = "<strong>" . htmlspecialchars($key) . ":</strong> " . htmlspecialchars($value);
                                    }
                                }

                                $itemPreview = implode('<br>', $previewParts);
                            }

                            $previewPath = (!empty($row['file_path']) && file_exists("../" . $row['file_path']))
                                ? "../" . htmlspecialchars($row['file_path'])
                                : "../assets/img/noimage.png";
                    ?>
                            <tr>
                                <td class="text-center fw-semibold"><?= $no++; ?></td>

                                <td class="text-center">
                                    <img src="<?= $previewPath; ?>" width="60"
                                        class="rounded border shadow-sm <?= empty($row['file_path']) ? 'opacity-50' : '' ?>"
                                        title="<?= empty($row['file_path']) ? 'No preview available' : 'Preview image' ?>">
                                </td>

                                <td class="text-center">
                                    <?php if (!empty($row['slug'])): ?>
                                        <span class="badge bg-primary-subtle text-primary fw-semibold">
                                            #<?= (int)$row['upload_id']; ?> – <?= htmlspecialchars($row['slug']); ?>
                                        </span>
                                    <?php elseif (!empty($row['upload_id'])): ?>
                                        <span class="badge bg-warning-subtle text-dark fw-semibold">
                                            #<?= (int)$row['upload_id']; ?> – (Belum terhubung)
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted fst-italic">Umum / Tanpa Upload</span>
                                    <?php endif; ?>
                                </td>

                                <td class="text-center">
                                    <span class="badge bg-info-subtle text-info fw-semibold">
                                        <?= !empty($row['skin_tone']) ? htmlspecialchars($row['skin_tone']) : '-'; ?>
                                    </span>
                                </td>

                                <td class="text-center">
                                    <span class="badge bg-success-subtle text-success fw-semibold">
                                        <?= !empty($row['body_shape']) ? htmlspecialchars($row['body_shape']) : '-'; ?>
                                    </span>
                                </td>

                                <td><?= htmlspecialchars($row['title']); ?></td>
                                <td><?= $itemPreview; ?></td>
                                <td class="text-center"><?= date('d M Y H:i', strtotime($row['created_at'])); ?></td>

                                <td class="text-center">
                                    <button class="btn btn-sm btn-warning me-1" data-bs-toggle="modal"
                                        data-bs-target="#editRecommendationModal" data-id="<?= $row['id']; ?>"
                                        data-upload="<?= $row['upload_id']; ?>"
                                        data-title="<?= htmlspecialchars($row['title']); ?>"
                                        data-items='<?= htmlspecialchars($row['items_json'], ENT_QUOTES); ?>'
                                        data-skin="<?= htmlspecialchars($row['skin_tone'] ?? ''); ?>"
                                        data-shape="<?= htmlspecialchars($row['body_shape'] ?? ''); ?>">
                                        <iconify-icon icon="solar:pen-broken"></iconify-icon>
                                    </button>

                                    <button onclick="hapusRecommendation(<?= (int)$row['id']; ?>)"
                                        class="btn btn-sm btn-danger">
                                        <iconify-icon icon="solar:trash-bin-trash-broken"></iconify-icon>
                                    </button>
                                </td>
                            </tr>
                        <?php }
                    } else { ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                Belum ada data rekomendasi.
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="addRecommendationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="?q=add_recommendations" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Tambah Recommendation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Pilih Upload (Opsional)</label>
                            <select name="upload_id" class="form-select">
                                <option value="">-- Tanpa Upload (Umum) --</option>
                                <?php
                                $uploads = $conn->query("SELECT id, slug FROM uploads ORDER BY created_at DESC");
                                while ($up = $uploads->fetch_assoc()) {
                                    echo "<option value='" . (int)$up['id'] . "'>#" . (int)$up['id'] . " - " . htmlspecialchars($up['slug']) . "</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Skin Tone</label>
                            <select name="skin_tone" class="form-select">
                                <option value="">-- Pilih --</option>
                                <option value="warm">Warm</option>
                                <option value="cool">Cool</option>
                                <option value="neutral">Neutral</option>
                                <option value="neutral-warm">Neutral-Warm</option>
                                <option value="neutral-cool">Neutral-Cool</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Body Shape</label>
                            <select name="body_shape" class="form-select">
                                <option value="">-- Pilih --</option>
                                <option value="rectangle">Rectangle</option>
                                <option value="triangle">Triangle</option>
                                <option value="inverted triangle">Inverted Triangle</option>
                                <option value="hourglass">Hourglass</option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Title</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Items JSON</label>
                            <textarea name="items_json" class="form-control" rows="5"
                                placeholder='{"recommended_colors":["#c49a6c","#8b5a2b"],"tips":["Gunakan warna netral hangat","Hindari warna kebiruan"]}'
                                required></textarea>
                            <small class="text-muted">Gunakan format JSON yang valid.</small>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="editRecommendationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="editForm" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Edit Recommendation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="id" id="editId">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Pilih Upload (Opsional)</label>
                            <select name="upload_id" id="editUpload" class="form-select">
                                <option value="">-- Tanpa Upload (Umum) --</option>
                                <?php
                                $uploads = $conn->query("SELECT id, slug FROM uploads ORDER BY created_at DESC");
                                while ($up = $uploads->fetch_assoc()) {
                                    echo "<option value='" . (int)$up['id'] . "'>#" . (int)$up['id'] . " - " . htmlspecialchars($up['slug']) . "</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Skin Tone</label>
                            <select name="skin_tone" id="editSkin" class="form-select">
                                <option value="">-- Pilih --</option>
                                <option value="warm">Warm</option>
                                <option value="cool">Cool</option>
                                <option value="neutral">Neutral</option>
                                <option value="neutral-warm">Neutral-Warm</option>
                                <option value="neutral-cool">Neutral-Cool</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Body Shape</label>
                            <select name="body_shape" id="editShape" class="form-select">
                                <option value="">-- Pilih --</option>
                                <option value="rectangle">Rectangle</option>
                                <option value="triangle">Triangle</option>
                                <option value="inverted triangle">Inverted Triangle</option>
                                <option value="hourglass">Hourglass</option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Title</label>
                            <input type="text" name="title" id="editTitle" class="form-control" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Items JSON</label>
                            <textarea name="items_json" id="editItems" class="form-control" rows="5"
                                required></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const editModal = document.getElementById('editRecommendationModal');
    editModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;

        const id = button.getAttribute('data-id');
        const upload = button.getAttribute('data-upload');
        const title = button.getAttribute('data-title');
        const items = button.getAttribute('data-items');
        const skin = button.getAttribute('data-skin');
        const shape = button.getAttribute('data-shape');

        document.getElementById('editId').value = id;
        document.getElementById('editTitle').value = title;
        document.getElementById('editItems').value = items;

        const uploadSelect = document.getElementById('editUpload');
        Array.from(uploadSelect.options).forEach(opt => {
            opt.selected = (opt.value === upload);
        });

        document.getElementById('editSkin').value = skin;
        document.getElementById('editShape').value = shape;

        document.getElementById('editForm').action = '?q=edit_recommendations&id=' + id;
    });

    function hapusRecommendation(id) {
        Swal.fire({
            title: "Yakin hapus data ini?",
            text: "Data yang dihapus tidak bisa dikembalikan!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Ya, hapus!"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "?q=delete_recommendations&id=" + id;
            }
        });
    }
</script>
