<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card mt-3">
                <div class="card-header">
                    <h4>Master Kecamatan</h4>
                </div>
                <div class="card-body">
                    <?php if($this->session->flashdata('msg')): ?>
                    <div class="alert alert-info">
                        <?= htmlspecialchars($this->session->flashdata('msg')) ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php if($this->session->flashdata('error')): ?>
                    <div class="alert alert-danger">
                        <?= htmlspecialchars($this->session->flashdata('error')) ?>
                    </div>
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#kecamatanModal" onclick="openCreateModal()">
                            <i class="fas fa-plus"></i> Tambah Kecamatan
                        </button>
                    </div>
                    
                    <?php if (isset($kecamatan_list) && !empty($kecamatan_list)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered datatable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nama Kecamatan</th>
                                    <th>Deskripsi</th>
                                    <th>Tanggal Dibuat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($kecamatan_list as $kecamatan): ?>
                                <tr>
                                    <td><?= htmlspecialchars($kecamatan['id']) ?></td>
                                    <td><?= htmlspecialchars($kecamatan['name']) ?></td>
                                    <td><?= htmlspecialchars($kecamatan['description'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($kecamatan['created_at']) ?></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-warning" onclick="openEditModal(<?= $kecamatan['id'] ?>, '<?= htmlspecialchars($kecamatan['name'], ENT_QUOTES) ?>', '<?= htmlspecialchars($kecamatan['description'] ?? '', ENT_QUOTES) ?>')">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <a href="#" class="btn btn-sm btn-danger" onclick="confirmDelete(<?= $kecamatan['id'] ?>, '<?= htmlspecialchars($kecamatan['name'], ENT_QUOTES) ?>')">
                                            <i class="fas fa-trash"></i> Hapus
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-info">
                        Tidak ada data kecamatan yang tersedia.
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Kecamatan -->
<div class="modal fade" id="kecamatanModal" tabindex="-1" aria-labelledby="kecamatanModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="kecamatanModalLabel">Tambah Kecamatan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?php echo form_open('', ['id' => 'kecamatanForm']); ?>
                <div class="modal-body">
                    <input type="hidden" id="kecamatan_id" name="id">
                    
                    <div class="mb-3">
                        <label for="kecamatan_name" class="form-label">Nama Kecamatan *</label>
                        <input type="text" class="form-control" id="kecamatan_name" name="name" required>
                        <div class="invalid-feedback" id="name_error"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="kecamatan_description" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="kecamatan_description" name="description" rows="3"></textarea>
                    </div>
                    
                    <div id="formErrorMessage" class="alert alert-danger d-none"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="saveBtn">Simpan</button>
                </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteConfirmModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus kecamatan <strong id="deleteKecamatanName"></strong>?</p>
                <p class="text-danger">Peringatan: Hanya kecamatan yang tidak digunakan oleh data KML yang dapat dihapus.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <a href="#" id="confirmDeleteBtn" class="btn btn-danger">Hapus</a>
            </div>
        </div>
    </div>
</div>

<!-- Modal Sukses -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="successModalLabel">Sukses</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="successMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<script>
// Fungsi untuk membuka modal tambah kecamatan
function openCreateModal() {
    // Reset form
    document.getElementById('kecamatanForm').reset();
    document.getElementById('kecamatan_id').value = '';
    document.getElementById('kecamatanModalLabel').textContent = 'Tambah Kecamatan';
    document.getElementById('kecamatanForm').action = '<?= base_url('kecamatan/create') ?>';
    
    // Reset error messages
    document.getElementById('name_error').textContent = '';
    document.getElementById('formErrorMessage').classList.add('d-none');
    
    // Reset input validation state
    document.getElementById('kecamatan_name').classList.remove('is-invalid');
}

// Fungsi untuk membuka modal edit kecamatan
function openEditModal(id, name, description) {
    // Set form values
    document.getElementById('kecamatan_id').value = id;
    document.getElementById('kecamatan_name').value = name;
    document.getElementById('kecamatan_description').value = description;
    document.getElementById('kecamatanModalLabel').textContent = 'Edit Kecamatan';
    document.getElementById('kecamatanForm').action = '<?= base_url('kecamatan/edit/') ?>' + id;
    
    // Reset error messages
    document.getElementById('name_error').textContent = '';
    document.getElementById('formErrorMessage').classList.add('d-none');
    
    // Reset input validation state
    document.getElementById('kecamatan_name').classList.remove('is-invalid');
    
    // Show modal
    var kecamatanModal = new bootstrap.Modal(document.getElementById('kecamatanModal'));
    kecamatanModal.show();
}

function confirmDelete(id, name) {
    document.getElementById('deleteKecamatanName').textContent = name;
    document.getElementById('confirmDeleteBtn').href = '<?= base_url('kecamatan/delete/') ?>' + id;
    var deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
    deleteModal.show();
}

// Handle form submission via AJAX
document.getElementById('kecamatanForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Disable tombol simpan
    const saveBtn = document.getElementById('saveBtn');
    const originalText = saveBtn.textContent;
    saveBtn.textContent = 'Menyimpan...';
    saveBtn.disabled = true;
    
    // Reset pesan error
    document.getElementById('name_error').textContent = '';
    document.getElementById('formErrorMessage').classList.add('d-none');
    document.getElementById('kecamatan_name').classList.remove('is-invalid');
    
    // Create FormData object
    const formData = new FormData(this);
    const action = this.action;
    
    // Send data via AJAX
    fetch(action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // Tampilkan pesan sukses
            document.getElementById('successMessage').textContent = data.message;
            var successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();
            
            // Tutup modal kecamatan
            var kecamatanModal = bootstrap.Modal.getInstance(document.getElementById('kecamatanModal'));
            kecamatanModal.hide();
            
            // Reload halaman setelah 1 detik
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else if (data.status === 'error') {
            // Tampilkan pesan error
            if (data.errors && data.errors.name) {
                document.getElementById('name_error').textContent = data.errors.name;
                document.getElementById('kecamatan_name').classList.add('is-invalid');
            } else {
                document.getElementById('formErrorMessage').textContent = data.message;
                document.getElementById('formErrorMessage').classList.remove('d-none');
            }
        }
    })
    .catch(error => {
        document.getElementById('formErrorMessage').textContent = 'Terjadi kesalahan saat menyimpan data';
        document.getElementById('formErrorMessage').classList.remove('d-none');
    })
    .finally(() => {
        // Aktifkan kembali tombol simpan
        saveBtn.textContent = originalText;
        saveBtn.disabled = false;
    });
});
</script>