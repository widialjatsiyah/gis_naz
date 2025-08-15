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
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-info">
                        <p>Belum ada data KML yang diunggah.</p>
                        <p>Silakan upload file KML terlebih dahulu dengan tombol "Upload KML Baru" di atas.</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>