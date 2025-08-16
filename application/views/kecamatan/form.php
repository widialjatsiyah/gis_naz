<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card mt-3">
                <div class="card-header">
                    <h4><?= isset($kecamatan) ? 'Edit Kecamatan' : 'Tambah Kecamatan' ?></h4>
                </div>
                <div class="card-body">
                    <?php if(validation_errors()): ?>
                    <div class="alert alert-danger">
                        <?= validation_errors() ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php echo form_open(); ?>
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Kecamatan *</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?= set_value('name', isset($kecamatan) ? $kecamatan['name'] : '') ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="description" name="description" rows="3"><?= set_value('description', isset($kecamatan) ? $kecamatan['description'] : '') ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <a href="<?= base_url('kecamatan') ?>" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>
    </div>
</div>