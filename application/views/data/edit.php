<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card mt-3">
                <div class="card-header">
                    <h4>
                        <a href="<?= base_url('data/list_kml') ?>" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        Detail Data KML: <?= htmlspecialchars($kml_head['name'] ?? '') ?>
                    </h4>
                </div>
                <div class="card-body">
                    <?php if (isset($kml_head)): ?>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th>ID File:</th>
                                    <td><?= htmlspecialchars($kml_head['id']) ?></td>
                                </tr>
                                <tr>
                                    <th>Nama File:</th>
                                    <td><?= htmlspecialchars($kml_head['name']) ?></td>
                                </tr>
                                <tr>
                                    <th>Tanggal Upload:</th>
                                    <td><?= htmlspecialchars($kml_head['created_at']) ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <?php if (!empty($kml_head['description'])): ?>
                            <div class="form-group">
                                <label><strong>Deskripsi:</strong></label>
                                <textarea class="form-control" rows="3" readonly><?= htmlspecialchars($kml_head['description']) ?></textarea>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <?php if (isset($kml_details) && !empty($kml_details)): ?>
                    <h5>Detail Placemarks (<?= count($kml_details) ?> items)</h5>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered datatable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Kelurahan</th>
                                    <th>Kategori</th>
                                    <th>Warna</th>
                                    <th>Properties</th>
                                    <th>Geometri</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($kml_details as $index => $detail): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= htmlspecialchars($detail['name']) ?></td>
                                    <td><?= htmlspecialchars($detail['kelurahan']) ?></td>
                                    <td><?= htmlspecialchars($detail['kategori'] ?? '-') ?></td>
                                    <td>
                                        <span style="background-color: <?= htmlspecialchars($detail['color']) ?>; 
                                              padding: 4px 8px; 
                                              border-radius: 4px;
                                              color: white;
                                              display: inline-block;">
                                            <?= htmlspecialchars($detail['color']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (!empty($detail['properties'])): ?>
                                            <?php 
                                            $properties = json_decode($detail['properties'], true);
                                            if (!empty($properties)): ?>
                                                <button class="btn btn-sm btn-info" type="button" 
                                                        data-bs-toggle="collapse" 
                                                        data-bs-target="#properties-<?= $detail['id'] ?>">
                                                    Lihat Properties
                                                </button>
                                                <div class="collapse mt-2" id="properties-<?= $detail['id'] ?>">
                                                    <ul class="list-group">
                                                        <?php foreach ($properties as $key => $value): ?>
                                                            <li class="list-group-item">
                                                                <strong><?= htmlspecialchars($key) ?>:</strong> 
                                                                <?= htmlspecialchars($value) ?>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                </div>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php 
                                        $geometry = json_decode($detail['geometry'], true);
                                        if (!empty($geometry)): ?>
                                            <strong>Type:</strong> <?= htmlspecialchars($geometry['type']) ?><br>
                                            <strong>Points:</strong> <?= count($geometry['coordinates'][0] ?? []) ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-info">
                        <p>Tidak ada data placemark pada file KML ini.</p>
                    </div>
                    <?php endif; ?>
                    
                    <?php else: ?>
                    <div class="alert alert-warning">
                        <p>Data KML tidak ditemukan.</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>