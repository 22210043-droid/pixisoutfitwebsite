<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">
                <iconify-icon icon="solar:t-shirt-line-duotone" width="24"></iconify-icon> Outfit Rules
            </h4>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addRuleModal">
                <iconify-icon icon="solar:add-square-broken" width="18"></iconify-icon> Tambah Rule
            </button>
        </div>

        <div class="table-responsive">
            <table id="outfitRulesTable" class="table table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="text-center">#</th>
                        <th>Skin Tone Group</th>
                        <th>Body Shape</th>
                        <th>Color Palette</th>
                        <th>Do List</th>
                        <th>Don't List</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT * FROM outfit_rules ORDER BY id DESC");
                    if ($result && $result->num_rows > 0) {
                        $no = 1;
                        while ($row = $result->fetch_assoc()) {

                            $paletteItems = array_filter(array_map('trim', explode(',', $row['color_palette'] ?? '')));
                    ?>
                            <tr>
                                <td class="text-center fw-semibold"><?= $no++; ?></td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary fw-semibold">
                                        <?= htmlspecialchars($row['skin_tone_group']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-success-subtle text-success fw-semibold">
                                        <?= htmlspecialchars($row['body_shape']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1 align-items-center">
                                        <?php foreach ($paletteItems as $color): ?>
                                            <?php
                                            $isHex = preg_match('/^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$/', $color);
                                            ?>
                                            <?php if ($isHex): ?>
                                                <span title="<?= htmlspecialchars($color); ?>"
                                                    style="display:inline-block;width:22px;height:22px;border-radius:50%;background:<?= htmlspecialchars($color); ?>;border:1px solid #ccc;"></span>
                                            <?php else: ?>
                                                <span class="badge bg-light text-dark border"><?= htmlspecialchars($color); ?></span>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>
                                </td>
                                <td><?= nl2br(htmlspecialchars($row['do_list'])); ?></td>
                                <td><?= nl2br(htmlspecialchars($row['dont_list'])); ?></td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-warning me-1" data-bs-toggle="modal"
                                        data-bs-target="#editRuleModal" data-id="<?= $row['id']; ?>"
                                        data-skin="<?= htmlspecialchars($row['skin_tone_group']); ?>"
                                        data-shape="<?= htmlspecialchars($row['body_shape']); ?>"
                                        data-palette="<?= htmlspecialchars($row['color_palette']); ?>"
                                        data-do="<?= htmlspecialchars($row['do_list']); ?>"
                                        data-dont="<?= htmlspecialchars($row['dont_list']); ?>">
                                        <iconify-icon icon="solar:pen-broken"></iconify-icon>
                                    </button>
                                    <button onclick="hapusRule(<?= $row['id']; ?>)" class="btn btn-sm btn-danger">
                                        <iconify-icon icon="solar:trash-bin-trash-broken"></iconify-icon>
                                    </button>
                                </td>
                            </tr>
                        <?php }
                    } else { ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Belum ada aturan outfit.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Add -->
<div class="modal fade" id="addRuleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="?q=add_outfit_rules" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Tambah Outfit Rule</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Skin Tone Group</label>
                            <select name="skin_tone_group" class="form-control" required>
                                <option value="">-- Pilih Skin Tone Group --</option>
                                <option value="warm">Warm</option>
                                <option value="cool">Cool</option>
                                <option value="neutral">Neutral</option>
                                <option value="neutral-warm">Neutral-Warm</option>
                                <option value="neutral-cool">Neutral-Cool</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Body Shape</label>
                            <select name="body_shape" class="form-control" required>
                                <option value="">-- Pilih Body Shape --</option>
                                <option value="rectangle">Rectangle</option>
                                <option value="triangle">Triangle</option>
                                <option value="inverted triangle">Inverted Triangle</option>
                                <option value="hourglass">Hourglass</option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Color Palette</label>
                            <input type="text" name="color_palette" class="form-control"
                                placeholder="contoh: beige, olive, terracotta atau #c49a6c,#8b5a2b,#deb887" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Do List</label>
                            <textarea name="do_list" class="form-control" rows="4" required></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Don't List</label>
                            <textarea name="dont_list" class="form-control" rows="4" required></textarea>
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
<div class="modal fade" id="editRuleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="editForm" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Edit Outfit Rule</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="editId">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Skin Tone Group</label>
                            <select name="skin_tone_group" id="editSkin" class="form-control" required>
                                <option value="warm">Warm</option>
                                <option value="cool">Cool</option>
                                <option value="neutral">Neutral</option>
                                <option value="neutral-warm">Neutral-Warm</option>
                                <option value="neutral-cool">Neutral-Cool</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Body Shape</label>
                            <select name="body_shape" id="editShape" class="form-control" required>
                                <option value="rectangle">Rectangle</option>
                                <option value="triangle">Triangle</option>
                                <option value="inverted triangle">Inverted Triangle</option>
                                <option value="hourglass">Hourglass</option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Color Palette</label>
                            <input type="text" name="color_palette" id="editPalette" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Do List</label>
                            <textarea name="do_list" id="editDo" class="form-control" rows="4" required></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Don't List</label>
                            <textarea name="dont_list" id="editDont" class="form-control" rows="4" required></textarea>
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
    const editModal = document.getElementById('editRuleModal');
    editModal.addEventListener('show.bs.modal', function(event) {
        const btn = event.relatedTarget;

        document.getElementById('editId').value = btn.getAttribute('data-id');
        document.getElementById('editSkin').value = btn.getAttribute('data-skin');
        document.getElementById('editShape').value = btn.getAttribute('data-shape');
        document.getElementById('editPalette').value = btn.getAttribute('data-palette');
        document.getElementById('editDo').value = btn.getAttribute('data-do');
        document.getElementById('editDont').value = btn.getAttribute('data-dont');

        document.getElementById('editForm').action = '?q=edit_outfit_rules&id=' + btn.getAttribute('data-id');
    });

    function hapusRule(id) {
        Swal.fire({
            title: "Yakin hapus data ini?",
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Ya, hapus!"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "?q=delete_outfit_rules&id=" + id;
            }
        });
    }
</script>
