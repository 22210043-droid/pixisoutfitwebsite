<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">
                <iconify-icon icon="solar:user-id-line-duotone" width="24"></iconify-icon> Users
            </h4>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <iconify-icon icon="solar:add-square-broken" width="18"></iconify-icon> Tambah User
            </button>
        </div>

        <div class="table-responsive">
            <table id="usersTable" class="table table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="text-center">#</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Tanggal Dibuat</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
                    if ($result->num_rows > 0) {
                        $no = 1;
                        while ($row = $result->fetch_assoc()) { ?>
                            <tr>
                                <td class="text-center fw-semibold"><?= $no++; ?></td>
                                <td><?= htmlspecialchars($row['name']); ?></td>
                                <td><?= htmlspecialchars($row['email']); ?></td>
                                <td><span
                                        class="badge bg-primary-subtle text-primary fw-semibold"><?= htmlspecialchars($row['role']); ?></span>
                                </td>
                                <td><?= date('d M Y H:i', strtotime($row['created_at'])); ?></td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-warning me-1" data-bs-toggle="modal"
                                        data-bs-target="#editUserModal" data-id="<?= $row['id']; ?>"
                                        data-name="<?= htmlspecialchars($row['name']); ?>"
                                        data-email="<?= htmlspecialchars($row['email']); ?>"
                                        data-role="<?= htmlspecialchars($row['role']); ?>">
                                        <iconify-icon icon="solar:pen-broken"></iconify-icon>
                                    </button>
                                    <button onclick="hapusUser(<?= $row['id']; ?>)" class="btn btn-sm btn-danger">
                                        <iconify-icon icon="solar:trash-bin-trash-broken"></iconify-icon>
                                    </button>
                                </td>
                            </tr>
                        <?php }
                    } else { ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Belum ada data user.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- 🔹 Modal Tambah -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <form action="?q=add_users" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Tambah User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Role</label>
                        <select name="role" class="form-select" required>
                            <option value="">-- Pilih Role --</option>
                            <option value="admin">Admin</option>
                            <option value="user">User</option>
                        </select>
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

<!-- 🔹 Modal Edit -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <form id="editForm" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="editId">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama</label>
                        <input type="text" name="name" id="editName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="email" id="editEmail" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Role</label>
                        <select name="role" id="editRole" class="form-select" required>
                            <option value="admin">Admin</option>
                            <option value="user">User</option>
                        </select>
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
    // Modal edit isi otomatis
    const editModal = document.getElementById('editUserModal');
    editModal.addEventListener('show.bs.modal', function(event) {
        const btn = event.relatedTarget;
        document.getElementById('editId').value = btn.getAttribute('data-id');
        document.getElementById('editName').value = btn.getAttribute('data-name');
        document.getElementById('editEmail').value = btn.getAttribute('data-email');
        document.getElementById('editRole').value = btn.getAttribute('data-role');
        document.getElementById('editForm').action = '?q=edit_users&id=' + btn.getAttribute('data-id');
    });

    // SweetAlert Hapus
    function hapusUser(id) {
        Swal.fire({
            title: "Yakin hapus user ini?",
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Ya, hapus!"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "?q=delete_users&id=" + id;
            }
        });
    }
</script>