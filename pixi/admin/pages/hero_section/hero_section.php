<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold mb-0">
                <iconify-icon icon="solar:star-fall-line-duotone" width="24"></iconify-icon>
                Hero Section
            </h4>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addHeroModal">
                <iconify-icon icon="solar:add-square-broken" width="18"></iconify-icon> Tambah Hero
            </button>
        </div>

        <div class="table-responsive">
            <table id="heroTable" class="table table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="text-center">#</th>
                        <th>Title</th>
                        <th>Subtitle</th>
                        <th>Buttons</th>
                        <th>Image</th>
                        <th>Tanggal</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT * FROM hero_section ORDER BY created_at DESC");
                    if ($result->num_rows > 0) {
                        $no = 1;
                        while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td class="text-center fw-semibold"><?= $no++; ?></td>
                        <td><?= htmlspecialchars($row['title']); ?></td>
                        <td><?= htmlspecialchars($row['subtitle']); ?></td>
                        <td>
                            <span class="badge bg-primary"><?= htmlspecialchars($row['btn_start_text']); ?></span><br>
                            <span class="badge bg-secondary"><?= htmlspecialchars($row['btn_video_text']); ?></span>
                        </td>
                        <td>
                            <?php if (!empty($row['image_path'])): ?>
                            <img src="../<?= htmlspecialchars($row['image_path']); ?>" width="100"
                                class="rounded shadow-sm border">
                            <?php else: ?>
                            <span class="text-muted">No Image</span>
                            <?php endif; ?>
                        </td>
                        <td><?= date('d M Y H:i', strtotime($row['created_at'])); ?></td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-warning me-1" data-bs-toggle="modal"
                                data-bs-target="#editHeroModal" data-id="<?= $row['id']; ?>"
                                data-title="<?= htmlspecialchars($row['title']); ?>"
                                data-subtitle="<?= htmlspecialchars($row['subtitle']); ?>"
                                data-btn1text="<?= htmlspecialchars($row['btn_start_text']); ?>"
                                data-btn1link="<?= htmlspecialchars($row['btn_start_link']); ?>"
                                data-btn2text="<?= htmlspecialchars($row['btn_video_text']); ?>"
                                data-btn2link="<?= htmlspecialchars($row['btn_video_link']); ?>">
                                <iconify-icon icon="solar:pen-broken"></iconify-icon>
                            </button>
                            <button onclick="hapusHero(<?= (int)$row['id']; ?>)" class="btn btn-sm btn-danger">
                                <iconify-icon icon="solar:trash-bin-trash-broken"></iconify-icon>
                            </button>
                        </td>
                    </tr>
                    <?php }
                    } else { ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">Belum ada data Hero Section.</td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<div class="modal fade" id="addHeroModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="?q=add_hero_section" method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Tambah Hero Section</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Subtitle</label>
                            <textarea name="subtitle" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Button 1 Text</label>
                            <input type="text" name="btn_start_text" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Button 1 Link</label>
                            <input type="text" name="btn_start_link" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Button 2 Text</label>
                            <input type="text" name="btn_video_text" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Button 2 Link</label>
                            <input type="text" name="btn_video_link" class="form-control">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Image</label>
                            <input type="file" name="image_path" class="form-control">
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

<div class="modal fade" id="editHeroModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="editForm" method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Edit Hero Section</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="editId">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" id="editTitle" class="form-control" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Subtitle</label>
                            <textarea name="subtitle" id="editSubtitle" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Button 1 Text</label>
                            <input type="text" name="btn_start_text" id="editBtn1Text" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Button 1 Link</label>
                            <input type="text" name="btn_start_link" id="editBtn1Link" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Button 2 Text</label>
                            <input type="text" name="btn_video_text" id="editBtn2Text" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Button 2 Link</label>
                            <input type="text" name="btn_video_link" id="editBtn2Link" class="form-control">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Image</label>
                            <input type="file" name="image_path" class="form-control">
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
const editModal = document.getElementById('editHeroModal');
editModal.addEventListener('show.bs.modal', function(event) {
    const button = event.relatedTarget;
    const id = button.getAttribute('data-id');
    document.getElementById('editForm').action = '?q=edit_hero_section&id=' + id;
    document.getElementById('editId').value = id;
    document.getElementById('editTitle').value = button.getAttribute('data-title');
    document.getElementById('editSubtitle').value = button.getAttribute('data-subtitle');
    document.getElementById('editBtn1Text').value = button.getAttribute('data-btn1text');
    document.getElementById('editBtn1Link').value = button.getAttribute('data-btn1link');
    document.getElementById('editBtn2Text').value = button.getAttribute('data-btn2text');
    document.getElementById('editBtn2Link').value = button.getAttribute('data-btn2link');
});

function hapusHero(id) {
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
            window.location.href = "?q=delete_hero_section&id=" + id;
        }
    });
}
</script>