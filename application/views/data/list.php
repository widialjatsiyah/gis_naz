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
                        <a href="<?= base_url('upload/kml') ?>" class="btn btn-primary">
                            <i class="fas fa-upload"></i> Upload KML Baru
                        </a>
                    </div>
                    
                    <?php if (isset($kml_files) && !empty($kml_files)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered datatable">
                            <thead>
                                <tr>
                                    <th>ID</th>
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
                                    <td><?= htmlspecialchars($kml['name']) ?></td>
                                    <td><?= htmlspecialchars($kml['description']) ?></td>
                                    <td><?= htmlspecialchars($kml['created_at']) ?></td>
                                    <td>
                                        <a href="<?= base_url('data/view_kml/'.$kml['id']) ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> Lihat Detail
                                        </a>
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

<script>
function confirmDelete(id, name) {
    document.getElementById('deleteKmlName').textContent = name;
    document.getElementById('confirmDeleteBtn').href = '<?= base_url('data/delete_kml/') ?>' + id;
    var deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
    deleteModal.show();
}
</script>