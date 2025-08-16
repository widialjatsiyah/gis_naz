<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card mt-3">
                <div class="card-header">
                    <h4>Upload KML</h4>
                </div>
                <div class="card-body">
                    <form method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="kecamatan_id" class="form-label">Kecamatan</label>
                            <select class="form-select" id="kecamatan_id" name="kecamatan_id">
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
                        
                        <button type="submit" class="btn btn-primary">Upload & Import</button>
                        <a href="<?= base_url('data/list_kml') ?>" class="btn btn-secondary">Batal</a>
                    </form>
                    
                    <?php if($this->session->flashdata('msg')): ?>
                    <div class="mt-3 alert alert-info">
                        <?= htmlspecialchars($this->session->flashdata('msg')) ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>