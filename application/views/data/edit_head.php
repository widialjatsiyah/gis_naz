<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card mt-3">
                <div class="card-header">
                    <h4>Edit Data KML</h4>
                </div>
                <div class="card-body">
                    <?php if(validation_errors()): ?>
                    <div class="alert alert-danger">
                        <?= validation_errors() ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php echo form_open(); ?>
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama File KML *</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?= set_value('name', isset($kml_head) ? $kml_head['name'] : '') ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="description" name="description" rows="3"><?= set_value('description', isset($kml_head) ? $kml_head['description'] : '') ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="kecamatan_id" class="form-label">Kecamatan</label>
                            <select class="form-select" id="kecamatan_id" name="kecamatan_id">
                                <option value="">Pilih Kecamatan (Opsional)</option>
                                <?php if (isset($kecamatan_list) && !empty($kecamatan_list)): ?>
                                    <?php foreach ($kecamatan_list as $kecamatan): ?>
                                        <option value="<?= $kecamatan['id'] ?>" <?= set_select('kecamatan_id', $kecamatan['id'], isset($kml_head) && $kml_head['kecamatan_id'] == $kecamatan['id'] ? true : false) ?>>
                                            <?= htmlspecialchars($kecamatan['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <div class="form-text">Pilih kecamatan untuk mengelompokkan data KML ini (opsional)</div>
                        </div>
                        
                        <div class="mb-3">
                            <a href="<?= base_url('data/list_kml') ?>" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>
    </div>
</div>