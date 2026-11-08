<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold mb-0">
                <iconify-icon icon="solar:info-circle-line-duotone" width="24"></iconify-icon>
                About Section
            </h4>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addAboutModal">
                <iconify-icon icon="solar:add-square-broken" width="18"></iconify-icon> Tambah Data
            </button>
        </div>

        <div class="table-responsive">
            <table id="aboutTable" class="table table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="text-center">#</th>
                        <th>Title</th>
                        <th>Subtitle</th>
                        <th>Description</th>
                        <th>Buttons</th>
                        <th>Images</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT * FROM about_section ORDER BY created_at DESC");
                    if ($result->num_rows > 0) {
                        $no = 1;
                        while ($row = $result->fetch_assoc()) { ?>
                            <tr>
                                <td class="text-center fw-semibold"><?= $no++; ?></td>
                                <td><?= htmlspecialchars($row['title']); ?></td>
                                <td><?= htmlspecialchars($row['subtitle']); ?></td>
                                <td><?= htmlspecialchars($row['description']); ?></td>
                                <td>
                                    <span class="badge bg-primary"><?= htmlspecialchars($row['btn_primary_text']); ?></span><br>
                                    <span class="badge bg-secondary"><?= htmlspecialchars($row['btn_secondary_text']); ?></span>
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-2">
                                        <?php if (!empty($row['image_main'])): ?>
                                            <img src="./<?= htmlspecialchars($row['image_main']); ?>" width="70"
                                                class="rounded shadow-sm border">
                                        <?php endif; ?>
                                        <?php if (!empty($row['image_secondary'])): ?>
                                            <img src="./<?= htmlspecialchars($row['image_secondary']); ?>" width="70"
                                                class="rounded shadow-sm border">
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-warning me-1" data-bs-toggle="modal"
                                        data-bs-target="#editAboutModal" data-id="<?= $row['id']; ?>"
                                        data-title="<?= htmlspecialchars($row['title']); ?>"
                                        data-subtitle="<?= htmlspecialchars($row['subtitle']); ?>"
                                        data-description="<?= htmlspecialchars($row['description']); ?>"
                                        data-btn_primary_text="<?= htmlspecialchars($row['btn_primary_text']); ?>"
                                        data-btn_primary_link="<?= htmlspecialchars($row['btn_primary_link']); ?>"
                                        data-btn_secondary_text="<?= htmlspecialchars($row['btn_secondary_text']); ?>"
                                        data-btn_secondary_link="<?= htmlspecialchars($row['btn_secondary_link']); ?>">
                                        <iconify-icon icon="solar:pen-broken"></iconify-icon>
                                    </button>
                                    <button onclick="hapusAbout(<?= (int)$row['id']; ?>)" class="btn btn-sm btn-danger">
                                        <iconify-icon icon="solar:trash-bin-trash-broken"></iconify-icon>
                                    </button>
                                </td>
                            </tr>
                        <?php }
                    } else { ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Belum ada data About Section.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="addAboutModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="?q=add_about_section" method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Tambah About Section</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Subtitle</label>
                            <input type="text" name="subtitle" class="form-control">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3" required></textarea>
                        </div>

                        <hr class="mt-3 mb-1">
                        <h6>Button Section</h6>

                        <div class="col-md-6">
                            <label class="form-label">Primary Button Text</label>
                            <input type="text" name="btn_primary_text" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Primary Button Link</label>
                            <input type="text" name="btn_primary_link" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Secondary Button Text</label>
                            <input type="text" name="btn_secondary_text" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Secondary Button Link</label>
                            <input type="text" name="btn_secondary_link" class="form-control">
                        </div>

                        <hr class="mt-3 mb-1">
                        <h6>Images</h6>

                        <div class="col-md-6">
                            <label class="form-label">Image Main</label>
                            <input type="file" name="image_main" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Image Secondary</label>
                            <input type="file" name="image_secondary" class="form-control">
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
<div class="modal fade" id="editAboutModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="editForm" method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Edit About Section</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="editId">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" id="editTitle" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Subtitle</label>
                            <input type="text" name="subtitle" id="editSubtitle" class="form-control">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" id="editDescription" class="form-control" rows="3"
                                required></textarea>
                        </div>

                        <hr class="mt-3 mb-1">
                        <h6>Button Section</h6>

                        <div class="col-md-6">
                            <label class="form-label">Primary Button Text</label>
                            <input type="text" name="btn_primary_text" id="editBtnPrimaryText" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Primary Button Link</label>
                            <input type="text" name="btn_primary_link" id="editBtnPrimaryLink" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Secondary Button Text</label>
                            <input type="text" name="btn_secondary_text" id="editBtnSecondaryText" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Secondary Button Link</label>
                            <input type="text" name="btn_secondary_link" id="editBtnSecondaryLink" class="form-control">
                        </div>

                        <hr class="mt-3 mb-1">
                        <h6>Images</h6>

                        <div class="col-md-6">
                            <label class="form-label">Image Main</label>
                            <input type="file" name="image_main" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Image Secondary</label>
                            <input type="file" name="image_secondary" class="form-control">
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
    const editModal = document.getElementById('editAboutModal');
    editModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const id = button.getAttribute('data-id');
        document.getElementById('editForm').action = '?q=edit_about_section&id=' + id;
        document.getElementById('editId').value = id;
        document.getElementById('editTitle').value = button.getAttribute('data-title');
        document.getElementById('editSubtitle').value = button.getAttribute('data-subtitle');
        document.getElementById('editDescription').value = button.getAttribute('data-description');
        document.getElementById('editBtnPrimaryText').value = button.getAttribute('data-btn_primary_text');
        document.getElementById('editBtnPrimaryLink').value = button.getAttribute('data-btn_primary_link');
        document.getElementById('editBtnSecondaryText').value = button.getAttribute('data-btn_secondary_text');
        document.getElementById('editBtnSecondaryLink').value = button.getAttribute('data-btn_secondary_link');
    });

    function hapusAbout(id) {
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
                window.location.href = "?q=delete_about_section&id=" + id;
            }
        });
    }
</script>