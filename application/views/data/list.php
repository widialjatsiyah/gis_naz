<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card mt-3">
                <div class="card-header">
                    <h4>Data KML</h4>
                </div>
                <div class="card-body">
                    <?php if($this->session->flashdata('msg')): ?>
                    <div class="alert alert-info">
                        <?= htmlspecialchars($this->session->flashdata('msg')) ?>
                    </div>
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadKmlModal">
                            <i class="fas fa-upload"></i> Upload KML Baru
                        </button>
                    </div>
                    
                    <?php if (isset($kml_files) && !empty($kml_files)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered datatable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Kecamatan</th>
                                    <th>Nama File KML</th>
                                    <th>Deskripsi</th>
                                    <th>Tanggal Upload</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($kml_files as $kml): ?>
                                <tr>
                                    <td><?= htmlspecialchars($kml['id']) ?></td>
                                    <td><?= htmlspecialchars($kml['kecamatan_name'] ?? 'Tidak ditentukan') ?></td>
                                    <td><?= htmlspecialchars($kml['name']) ?></td>
                                    <td><?= htmlspecialchars($kml['description']) ?></td>
                                    <td><?= htmlspecialchars($kml['created_at']) ?></td>
                                    <td>
                                        <a href="<?= base_url('data/view_kml/'.$kml['id']) ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> Lihat Detail
                                        </a>
                                        <button class="btn btn-sm btn-warning" onclick="openEditModal(<?= $kml['id'] ?>, '<?= htmlspecialchars($kml['name'], ENT_QUOTES) ?>', '<?= htmlspecialchars($kml['description'], ENT_QUOTES) ?>', <?= $kml['kecamatan_id'] ?? 'null' ?>)">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <?php if (is_admin()): ?>
                                        <a href="#" class="btn btn-sm btn-danger" onclick="confirmDelete(<?= $kml['id'] ?>, '<?= htmlspecialchars($kml['name'], ENT_QUOTES) ?>')">
                                            <i class="fas fa-trash"></i> Hapus
                                        </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-info">
                        Tidak ada data KML yang tersedia.
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit KML -->
<div class="modal fade" id="editKmlModal" tabindex="-1" aria-labelledby="editKmlModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editKmlModalLabel">Edit Data KML</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?php echo form_open('', ['id' => 'editKmlForm']); ?>
                <div class="modal-body">
                    <input type="hidden" id="edit_kml_id" name="kml_id">
                    
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Nama File KML *</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_kecamatan_id" class="form-label">Kecamatan</label>
                        <select class="form-select" id="edit_kecamatan_id" name="kecamatan_id">
                            <option value="">Pilih Kecamatan (Opsional)</option>
                            <?php if (isset($kecamatan_list) && !empty($kecamatan_list)): ?>
                                <?php foreach ($kecamatan_list as $kecamatan): ?>
                                    <option value="<?= $kecamatan['id'] ?>"><?= htmlspecialchars($kecamatan['name']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <div class="form-text">Pilih kecamatan untuk mengelompokkan data KML ini (opsional)</div>
                    </div>
                    
                    <div id="editErrorMessage" class="alert alert-danger d-none"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="saveEditBtn">Simpan Perubahan</button>
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
                <p>Apakah Anda yakin ingin menghapus data KML <strong id="deleteKmlName"></strong>?</p>
                <p class="text-danger">Peringatan: Tindakan ini akan menghapus seluruh data detail yang terkait dengan file KML ini dan tidak dapat dibatalkan.</p>
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

<!-- Modal Upload KML -->
<div class="modal fade" id="uploadKmlModal" tabindex="-1" aria-labelledby="uploadKmlModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadKmlModalLabel">Upload KML</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?php echo form_open_multipart('upload/kml', ['id' => 'uploadKmlForm']); ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="upload_kecamatan_id" class="form-label">Kecamatan</label>
                        <select class="form-select" id="upload_kecamatan_id" name="kecamatan_id">
                            <option value="">Pilih Kecamatan (Opsional)</option>
                            <?php if (isset($kecamatan_list) && !empty($kecamatan_list)): ?>
                                <?php foreach ($kecamatan_list as $kecamatan): ?>
                                    <option value="<?= $kecamatan['id'] ?>"><?= htmlspecialchars($kecamatan['name']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <div class="form-text">Pilih kecamatan untuk mengelompokkan data KML ini (opsional)</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="kml" class="form-label">File KML</label>
                        <input type="file" class="form-control" id="kml" name="kml" accept=".kml,application/vnd.google-earth.kml+xml" required>
                    </div>
                    
                    <div id="uploadErrorMessage" class="alert alert-danger d-none"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="uploadBtn">Upload & Import</button>
                </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<script>
// Fungsi untuk membuka modal edit
function openEditModal(id, name, description, kecamatanId) {
    // Reset form dan pesan error
    document.getElementById('editErrorMessage').classList.add('d-none');
    document.getElementById('editKmlForm').reset();
    
    // Set nilai form
    document.getElementById('edit_kml_id').value = id;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_description').value = description;
    
    // Set kecamatan jika ada
    const kecamatanSelect = document.getElementById('edit_kecamatan_id');
    if (kecamatanId !== null) {
        kecamatanSelect.value = kecamatanId;
    } else {
        kecamatanSelect.selectedIndex = 0;
    }
    
    // Set action form
    document.getElementById('editKmlForm').action = '<?= base_url('data/edit_kml/') ?>' + id;
    
    // Tampilkan modal
    var editModal = new bootstrap.Modal(document.getElementById('editKmlModal'));
    editModal.show();
}

function confirmDelete(id, name) {
    document.getElementById('deleteKmlName').textContent = name;
    document.getElementById('confirmDeleteBtn').href = '<?= base_url('data/delete_kml/') ?>' + id;
    var deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
    deleteModal.show();
}

// Handle form submission via AJAX
document.getElementById('editKmlForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Disable tombol simpan
    const saveBtn = document.getElementById('saveEditBtn');
    const originalText = saveBtn.textContent;
    saveBtn.textContent = 'Menyimpan...';
    saveBtn.disabled = true;
    
    // Reset pesan error
    document.getElementById('editErrorMessage').classList.add('d-none');
    
    // Kirim data via AJAX
    const formData = new FormData(this);
    const id = document.getElementById('edit_kml_id').value;
    
    fetch('<?= base_url('data/edit_kml/') ?>' + id, {
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
            
            // Tutup modal edit
            var editModal = bootstrap.Modal.getInstance(document.getElementById('editKmlModal'));
            editModal.hide();
            
            // Reload halaman setelah 1 detik
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            // Tampilkan pesan error
            document.getElementById('editErrorMessage').textContent = data.message;
            document.getElementById('editErrorMessage').classList.remove('d-none');
        }
    })
    .catch(error => {
        document.getElementById('editErrorMessage').textContent = 'Terjadi kesalahan saat menyimpan data';
        document.getElementById('editErrorMessage').classList.remove('d-none');
    })
    .finally(() => {
        // Aktifkan kembali tombol simpan
        saveBtn.textContent = originalText;
        saveBtn.disabled = false;
    });
});

// Handle upload form submission via AJAX
document.getElementById('uploadKmlForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Disable tombol upload
    const uploadBtn = document.getElementById('uploadBtn');
    const originalText = uploadBtn.textContent;
    uploadBtn.textContent = 'Mengupload...';
    uploadBtn.disabled = true;
    
    // Reset pesan error
    document.getElementById('uploadErrorMessage').classList.add('d-none');
    
    // Create FormData object
    const formData = new FormData(this);
    
    // Send data via AJAX
    fetch('<?= base_url('upload/kml') ?>', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        // Check if response is JSON
        const contentType = response.headers.get("content-type");
        if (contentType && contentType.indexOf("application/json") !== -1) {
            return response.json();
        } else {
            // If not JSON, it's probably a redirect or HTML response
            return response.text().then(text => {
                return {status: 'success', message: 'KML file berhasil diupload dan diproses'};
            });
        }
    })
    .then(data => {
        if (data.status === 'success') {
            // Tampilkan pesan sukses
            document.getElementById('successMessage').textContent = data.message;
            var successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();
            
            // Tutup modal upload
            var uploadModal = bootstrap.Modal.getInstance(document.getElementById('uploadKmlModal'));
            uploadModal.hide();
            
            // Reload halaman setelah 1 detik
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            // Tampilkan pesan error
            document.getElementById('uploadErrorMessage').textContent = data.message || 'Terjadi kesalahan saat mengupload file';
            document.getElementById('uploadErrorMessage').classList.remove('d-none');
        }
    })
    .catch(error => {
        document.getElementById('uploadErrorMessage').textContent = 'Terjadi kesalahan saat mengupload file: ' + error.message;
        document.getElementById('uploadErrorMessage').classList.remove('d-none');
    })
    .finally(() => {
        // Aktifkan kembali tombol upload
        uploadBtn.textContent = originalText;
        uploadBtn.disabled = false;
    });
});
</script>