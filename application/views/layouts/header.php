<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Peta </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="<?=base_url('/public/assets/togeojson.js')?>"></script>
    <style>
        body {
            height: 100vh;
            display: flex;
            flex-direction: column
        }
        .app {
            flex: 1;
            display: flex
        }
        .sidebar {
            width: 220px;
            padding: 12px;
            border-right: 1px solid #e9ecef;
            transition: all 0.3s ease;
        }
        .sidebar.collapsed {
            width: 0;
            padding: 12px 0;
            overflow: hidden;
            border-right: none;
        }
        .content {
            flex: 1;
            position: relative
        }
        .topbar {
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 16px;
            border-bottom: 1px solid #e9ecef;
            background: #fff
        }
        .map-container {
            position: absolute;
            inset: 0
        }
        
        /* Hidden container for storing original options */
        #filterKelContainer {
            display: none;
        }
    </style>
    <!-- Memuat library Leaflet -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <!-- Memuat library jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Memuat library DataTables -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <!-- Memuat library Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <!-- Memuat library Leaflet.draw jika diperlukan -->
    <?php // if (isset($load_leaflet_draw) && $load_leaflet_draw): ?>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>
    <?php // endif; ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleButton = document.getElementById('toggleSidebar');
            const sidebar = document.querySelector('.sidebar');
            
            if (toggleButton && sidebar) {
                toggleButton.addEventListener('click', function() {
                    sidebar.classList.toggle('collapsed');
                    
                    // Ubah ikon tombol berdasarkan status sidebar
                    const icon = toggleButton.querySelector('i');
                    if (sidebar.classList.contains('collapsed')) {
                        icon.classList.remove('fa-bars');
                        icon.classList.add('fa-arrow-right');
                    } else {
                        icon.classList.remove('fa-arrow-right');
                        icon.classList.add('fa-bars');
                    }
                    
                    // Trigger resize event untuk menyesuaikan ukuran peta
                    window.dispatchEvent(new Event('resize'));
                });
            }
        });
    </script>
</head>
<body>
    <div class="topbar">
        <div>
            <button id="toggleSidebar" class="btn btn-sm btn-outline-primary me-2">
                <i class="fas fa-bars"></i>
            </button>
            <strong>Peta</strong>
        </div>
        <div>
            <span class="me-3">Hi, <b><?= htmlspecialchars($user['username']) ?></b></span>
            <a class="btn btn-sm btn-outline-secondary" href="<?= base_url('auth/logout') ?>">Logout</a>
        </div>
    </div>
    <div class="app">
        <div class="sidebar">
            <h6>Menu</h6>
            <ul class="nav flex-column">
                <li class="nav-item"><a class="nav-link" href="<?= base_url('map') ?>">Peta</a></li>
                <?php if(is_admin()): ?>
                <li class="nav-item"><a class="nav-link" href="<?= base_url('data/list_kml') ?>">Data KML</a></li>
                <!-- <li class="nav-item"><a class="nav-link" href="<?= base_url('upload/kml') ?>">Upload KML</a></li> -->
                <!-- <li class="nav-item"><a class="nav-link" href="<?= base_url('geojson/import') ?>">Import GeoJSON</a></li> -->
                <li class="nav-item"><a class="nav-link" href="<?= base_url('kecamatan') ?>">Master Kecamatan</a></li>
                <?php endif; ?>
                <!-- <li class="nav-item"><a class="nav-link" href="<?= base_url('export/geojson') ?>">Export GeoJSON</a></li> -->
            </ul>
            <hr/>
            <div class="mb-2">
                <h6>Filter</h6>
                <div class="form-group">
                    <label for="filterKec">Kecamatan</label>
                      <select id="filterKec" class="form-select mb-2 select2-filter">
                    <option value="">Semua Kecamatan</option>
                    <?php if (isset($kecamatan_list)): ?>
                        <?php foreach($kecamatan_list as $k): ?>
                            <option value="<?= htmlspecialchars($k['id']) ?>"><?= htmlspecialchars($k['name']) ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                </div>
                <div class="form-group">
                    <label for="filterKel">Kelurahan</label>
                    
                <select id="filterKel" class="form-select select2-filter">
                    <option value="">Semua Kelurahan</option>
                    <?php foreach($kelurahan_list as $k): ?>
                        <?php if (property_exists($k, 'id') && is_numeric($k->id)): ?>
                            <!-- Ini adalah file KML -->
                            <option value="<?= htmlspecialchars($k->id) ?>" data-kecamatan="<?= isset($k->kecamatan_id) ? htmlspecialchars($k->kecamatan_id) : '' ?>">[KML] <?= htmlspecialchars($k->kelurahan) ?></option>
                        <?php else: ?>
                            <!-- Ini adalah kelurahan biasa -->
                            <option value="<?= htmlspecialchars($k->kelurahan) ?>"><?= htmlspecialchars($k->kelurahan) ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
                </div>
                
                <hr>
                <!-- Tambahkan input pencarian polygon Select2 -->
                <div class="form-group">
                    <label for="searchPolygonSelect2">Cari Nama / No Objek</label>
                    <select id="searchPolygonSelect2" class="form-select select2-filter" style="width: 100%;">
                        <option value="">Pilih atau cari ...</option>
                    </select>
                </div>
                
                <!-- Checkbox kategori -->
                <div class="form-group" id="categoryFilterContainer" style="display: none;">
                    <label>Filter Kategori:</label>
                    <div id="categoryCheckboxes">
                        <!-- Checkbox kategori akan dimuat di sini -->
                    </div>
                </div>
                
                <!-- Hidden container for storing original options -->
                <div id="filterKelContainer" style="display: none;">
                    <?php foreach($kelurahan_list as $k): ?>
                        <?php if (property_exists($k, 'id') && is_numeric($k->id)): ?>
                            <!-- Ini adalah file KML -->
                            <option value="<?= htmlspecialchars($k->id) ?>" data-kecamatan="<?= isset($k->kecamatan_id) ? htmlspecialchars($k->kecamatan_id) : '' ?>">[KML] <?= htmlspecialchars($k->kelurahan) ?></option>
                        <?php else: ?>
                            <!-- Ini adalah kelurahan biasa -->
                            <option value="<?= htmlspecialchars($k->kelurahan) ?>"><?= htmlspecialchars($k->kelurahan) ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                
                <!-- Container untuk hasil pencarian -->
                <!-- <div id="searchResultsContainer" class="mt-3" style="display: none;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">Hasil Pencarian</h6>
                        <button id="closeSearchResults" class="btn btn-sm btn-outline-secondary py-0">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div style="max-height: 300px; overflow-y: auto;">
                        <table id="searchResultsTable" class="table table-striped table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Kelurahan</th>
                                    <th>Kategori</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                 Hasil pencarian akan dimuat di s
                            </tbody>
                        </table>
                    </div>
                </div> -->
            </div>
        </div>
        <div class="content">