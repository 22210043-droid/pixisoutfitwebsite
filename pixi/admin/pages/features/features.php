<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold mb-0">
                <iconify-icon icon="solar:layers-line-duotone" width="24"></iconify-icon>
                Features Section
            </h4>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addFeatureModal">
                <iconify-icon icon="solar:add-square-broken" width="18"></iconify-icon> Tambah Feature
            </button>
        </div>

        <div class="table-responsive">
            <table id="featureTable" class="table table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="text-center">#</th>
                        <th>Tab Key</th>
                        <th>Tab Title</th>
                        <th>Description</th>
                        <th>Images</th>
                        <th>Tanggal</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT * FROM features_section ORDER BY created_at DESC");
                    if ($result->num_rows > 0) {
                        $no = 1;
                        while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td class="text-center fw-semibold"><?= $no++; ?></td>
                        <td><span class="badge bg-dark"><?= htmlspecialchars($row['tab_key']); ?></span></td>
                        <td><?= htmlspecialchars($row['tab_title']); ?></td>
                        <td><?= htmlspecialchars($row['description']); ?></td>
                        <td>
                            <div class="d-flex flex-wrap gap-2">
                                <?php if (!empty($row['image_before'])): ?>
                                <img src="./<?= htmlspecialchars($row['image_before']); ?>" width="70"
                                    class="rounded shadow-sm border">
                                <?php endif; ?>
                                <?php if (!empty($row['image_reference'])): ?>
                                <img src="./<?= htmlspecialchars($row['image_reference']); ?>" width="70"
                                    class="rounded shadow-sm border">
                                <?php endif; ?>
                                <?php if (!empty($row['image_after'])): ?>
                                <img src="./<?= htmlspecialchars($row['image_after']); ?>" width="70"
                                    class="rounded shadow-sm border">
                                <?php endif; ?>
                            </div>
                        </td>
                        <td><?= date('d M Y H:i', strtotime($row['created_at'])); ?></td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-warning me-1" data-bs-toggle="modal"
                                data-bs-target="#editFeatureModal" data-id="<?= $row['id']; ?>"
                                data-tabkey="<?= htmlspecialchars($row['tab_key']); ?>"
                                data-tabtitle="<?= htmlspecialchars($row['tab_title']); ?>"
                                data-description="<?= htmlspecialchars($row['description']); ?>">
                                <iconify-icon icon="solar:pen-broken"></iconify-icon>
                            </button>
                            <button onclick="hapusFeature(<?= (int)$row['id']; ?>)" class="btn btn-sm btn-danger">
                                <iconify-icon icon="solar:trash-bin-trash-broken"></iconify-icon>
                            </button>
                        </td>
                    </tr>
                    <?php }
                    } else { ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">Belum ada data Features Section.</td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="addFeatureModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="?q=add_features" method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Tambah Feature</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Tab Key</label>
                            <input type="text" name="tab_key" class="form-control" placeholder="tryon, swap, editing..."
                                required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tab Title</label>
                            <input type="text" name="tab_title" class="form-control" placeholder="Clothing Try-On"
                                required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Image Before</label>
                            <input type="file" name="image_before" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Image Reference</label>
                            <input type="file" name="image_reference" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Image After</label>
                            <input type="file" name="image_after" class="form-control">
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
<div class="modal fade" id="editFeatureModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="editForm" method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Edit Feature</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="editId">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Tab Key</label>
                            <input type="text" name="tab_key" id="editTabKey" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tab Title</label>
                            <input type="text" name="tab_title" id="editTabTitle" class="form-control" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" id="editDescription" class="form-control" rows="3"
                                required></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Image Before</label>
                            <input type="file" name="image_before" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Image Reference</label>
                            <input type="file" name="image_reference" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Image After</label>
                            <input type="file" name="image_after" class="form-control">
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
const editModal = document.getElementById('editFeatureModal');
editModal.addEventListener('show.bs.modal', function(event) {
    const button = event.relatedTarget;
    const id = button.getAttribute('data-id');
    document.getElementById('editForm').action = '?q=edit_features&id=' + id;
    document.getElementById('editId').value = id;
    document.getElementById('editTabKey').value = button.getAttribute('data-tabkey');
    document.getElementById('editTabTitle').value = button.getAttribute('data-tabtitle');
    document.getElementById('editDescription').value = button.getAttribute('data-description');
});

function hapusFeature(id) {
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
            window.location.href = "?q=delete_features&id=" + id;
        }
    });
}
</script>