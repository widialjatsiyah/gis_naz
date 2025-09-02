<div class="map-container" id="map"></div>

<!-- Tombol Cari Mengambang di Atas Peta -->
<div class="search-button-floating">
    <button type="button" class="btn btn-primary" id="openSearchModal">
        <i class="fas fa-search"></i> Cari Nama / No Objek
    </button>
</div>

<!-- Modal Pencarian -->
<div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="searchModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="searchModalLabel">Cari Nama / No Objek</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="searchPolygon" class="form-label">Masukkan nama atau nomor objek:</label>
                    <input type="text" class="form-control" id="searchPolygon" placeholder="Cari...">
                </div>
                
                <!-- Checkbox kategori di modal pencarian -->
                <div class="form-group" id="searchCategoryFilterContainer" style="display: none;">
                    <label>Filter Kategori:</label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="selectAllCategories" checked>
                        <label class="form-check-label" for="selectAllCategories">Semua Kategori</label>
                    </div>
                    <div id="searchCategoryCheckboxes">
                        <!-- Checkbox kategori akan dimuat di sini -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="performSearch">Cari</button>
            </div>
        </div>
    </div>
</div>

<!-- Hasil Pencarian Melayang di Atas Peta -->
<div id="searchResultsContainer" class="search-results-container floating-above-map">
    <div class="card">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Hasil Pencarian</h5>
            <button type="button" class="btn-close" id="closeSearchResults" aria-label="Close"></button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-sm mb-0" id="searchResultsTable">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Kelurahan</th>
                            <th>Properties</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Hasil pencarian akan ditampilkan di sini -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal detail polygon di sisi kanan -->
<div id="polygonDetailModal" class="polygon-detail-modal">
    <div class="polygon-detail-content">
        <div class="polygon-detail-header">
            <h5>Detail Polygon</h5>
            <button type="button" class="btn-close" id="closeDetailModal" aria-label="Close"></button>
        </div>
        <div class="polygon-detail-body">
            <div id="polygonProperties">
                <!-- Properties akan dimuat di sini -->
            </div>
             <?php if (isset($user) && is_admin()): ?>

            <div class="polygon-actions mt-3">
                <button id="editPolygonBtn" class="btn btn-warning btn-sm">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button id="deletePolygonBtn" class="btn btn-danger btn-sm">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            </div>
             <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal untuk notifikasi -->
<div class="modal fade" id="notificationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Perhatian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Silakan pilih kelurahan terlebih dahulu sebelum menggambar.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk konfirmasi hapus -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus polygon ini?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Hapus</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk edit polygon -->
<div class="modal fade" id="editInstructionsModal" tabindex="-1" aria-labelledby="editInstructionsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editInstructionsModalLabel">Edit Polygon</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Klik tombol di bawah untuk memulai mengedit polygon. Setelah selesai mengedit, klik tombol "Simpan Perubahan".</p>
                <div class="mb-3">
                    <label for="polygonColor" class="form-label">Warna Polygon:</label>
                    <input type="color" class="form-control form-control-color" id="polygonColor" value="#3388ff" title="Pilih warna polygon">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="startEditBtn">Mulai Edit</button>
            </div>
        </div>
    </div>
</div>

<!-- Tambahkan alert untuk pencarian -->
<div class="alert alert-info" id="infoAlert" role="alert">
    <i class="fas fa-info-circle"></i> 
    <span id="infoMessage"></span>
</div>

<!-- Tombol Simpan Perubahan (awalnya disembunyikan) -->
<div id="saveButtonContainer" style="position: absolute; top: 10px; right: 10px; z-index: 1000; display: none;">
    <button id="saveChangesBtn" class="btn btn-success">
        <i class="fas fa-save"></i> Simpan Perubahan
    </button>
</div>

<style>
    #map {
        height: 100vh;
        position: relative;
    }
    
    /* Gaya untuk alert pencarian */
    #infoAlert {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 1000;
        display: none;
    }

    /* Gaya untuk tombol pencarian mengambang */
    .search-button-floating {
        position: absolute;
        top: 10px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 1000;
        padding: 10px;
    }

    /* Gaya untuk hasil pencarian melayang di atas peta */
    .search-results-container.floating-above-map {
        position: absolute;
        top: 70px; /* Di bawah tombol pencarian */
        left: 20px;
        right: 20px;
        max-height: 300px;
        z-index: 999;
        display: none;
        overflow: hidden;
    }

    .search-results-container.floating-above-map .card {
        border: 1px solid rgba(0, 0, 0, 0.125);
        border-radius: 0.375rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    .search-results-container.floating-above-map .card-body {
        max-height: 250px;
        overflow-y: auto;
    }

    .polygon-detail-modal {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 300px;
        max-height: calc(100% - 20px);
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        display: none;
        overflow: hidden;
        flex-direction: column;
    }

    .polygon-detail-modal.show {
        display: flex;
    }

    .polygon-detail-header {
        padding: 15px;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .polygon-detail-body {
        padding: 15px;
        overflow-y: auto;
        flex: 1;
    }

    .polygon-actions {
        display: flex;
        gap: 10px;
        justify-content: center;
    }

    .polygon-actions .btn {
        flex: 1;
    }

    .property-item {
        margin-bottom: 10px;
    }

    .property-label {
        font-weight: bold;
        margin-bottom: 3px;
    }

    .property-value {
        background-color: #f8f9fa;
        padding: 5px 10px;
        border-radius: 4px;
        word-break: break-word;
    }

    .formatted-properties div {
        margin-bottom: 3px;
    }

    .formatted-properties strong {
        display: inline-block;
        width: 120px;
        vertical-align: top;
    }

    .polygon-label {
        font-size: 10px;
        color: #333;
        font-weight: bold;
        text-shadow: 1px 1px 1px rgba(255, 255, 255, 0.8);
        z-index: 1000;
        pointer-events: auto;
    }
    
</style>

<script>
    // Set default kelurahan from PHP
    var default_kelurahan = <?= isset($default_kelurahan) ? json_encode($default_kelurahan) : 'undefined' ?>;

    $(document).ready(function() {
        // Initialize Select2 for filter dropdowns
        $('.select2-filter').select2({
            placeholder: "Pilih atau cari...",
            allowClear: true,
            width: '100%'
        });
        
        // Inisialisasi Select2 untuk pencarian polygon
        $('#searchPolygonSelect2').select2({
            placeholder: "Cari polygon berdasarkan nama...",
            allowClear: true,
            width: '100%',
            ajax: {
                url: '<?= base_url("index.php/map/search_polygon") ?>',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        term: params.term, // kata kunci pencarian
                        page: params.page || 1
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    
                    return {
                        results: data.results,
                        pagination: {
                            more: data.pagination.more
                        }
                    };
                },
                cache: true
            },
            minimumInputLength: 1,
            templateResult: function (data) {
                if (data.loading) {
                    return data.text;
                }
                
                return $('<span>' + data.text + '</span>');
            },
            templateSelection: function (data) {
                return data.text || data.name || 'Pilih atau cari polygon...';
            }
        });

        // Event handler untuk pemilihan polygon dari Select2
        $('#searchPolygonSelect2').on('select2:select', function (e) {
            var data = e.params.data;
            // console.log("Polygon selected:", data);
            highlightPolygon(data.id);
        });

        // Event handler untuk menghapus pilihan polygon
        $('#searchPolygonSelect2').on('select2:unselect', function (e) {
            resetPolygonHighlight();
        });

        // Initialize the map
        var map = L.map('map').setView([1.093, 103.386], 12);

        // Base layers
        var osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 20,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        });

        var satelliteLayer = L.tileLayer('https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
            attribution: '&copy; <a href="https://www.google.com/maps">Google Maps</a>'
        });

        // Layer Kecamatan dari file KML
        var kecamatanLayer = L.layerGroup();

        // Memuat file KML kecamatan
        fetch('<?= base_url('public/data/kecamata.kml') ?>')
            .then(response => response.text())
            .then(data => {
                var parser = new DOMParser();
                var kml = parser.parseFromString(data, 'text/xml');
                var geojson = toGeoJSON.kml(kml);
                
                L.geoJSON(geojson, {
                    style: function(feature) {
                        return {
                            color: '#0000CC',
                            weight: 2,
                            opacity: 1,
                            fillColor: '#0000CC',
                            fillOpacity: 0.1
                        };
                    },
                    onEachFeature: function(feature, layer) {
                        if (feature.properties.name) {
                            layer.bindTooltip(feature.properties.name, {permanent: false, direction: 'center'});
                        }
                    }
                }).addTo(kecamatanLayer);
            })
            .catch(error => {
                console.error('Error loading KML file:', error);
            });

        // Add default layer to map
        osmLayer.addTo(map);

        // Layer control
        var baseLayers = {
            "OpenStreetMap": osmLayer,
            "Satelit": satelliteLayer
        };

        var overlays = {
            "Batas Kecamatan": kecamatanLayer
        };

        L.control.layers(baseLayers, overlays).addTo(map);

        // Tambahkan kecamatanLayer ke map agar terlihat secara default
        kecamatanLayer.addTo(map);

        var group = new L.FeatureGroup().addTo(map);
        var layerMap = {}; // Untuk menyimpan referensi layer berdasarkan ID
        var currentDeleteId = null; // Untuk menyimpan ID yang akan dihapus
        var currentEditLayer = null; // Untuk menyimpan layer yang sedang diedit

        // Filter kelurahan berdasarkan kecamatan yang dipilih
        $('#filterKec').on('change', function() {
            var kecId = $(this).val();
            
            // Reset dropdown kelurahan
            $('#filterKel').empty();
            
            // Tambahkan opsi default
            $('#filterKel').append(new Option("Semua Kelurahan", "", false, false));
            
            if (kecId) {
                // Filter kelurahan berdasarkan kecamatan yang dipilih
                $('#filterKelContainer option[data-kecamatan]').each(function() {
                    var optionKecId = $(this).data('kecamatan');
                    if (optionKecId == kecId) {
                        $('#filterKel').append($(this).clone());
                    }
                });
            } else {
                // Jika tidak ada kecamatan yang dipilih, tampilkan semua kelurahan
                $('#filterKelContainer option').each(function() {
                    if ($(this).val() !== "") {
                        $('#filterKel').append($(this).clone());
                    }
                });
            }
            
            // Trigger change event untuk Select2
            // $('#filterKel').trigger('change');
            
            // Load data
            // loadData();
        });
        
        // Reload data when filter changes
        $('#filterKel').on('change', function() {
            // Sembunyikan container checkbox kategori
            $('#categoryFilterContainer').hide();
            
            // Kosongkan checkbox kategori
            $('#categoryCheckboxes').empty();
            
            // Jika ada nilai yang dipilih dan merupakan angka (ID KML)
            var selectedValue = $(this).val();
            // console.log("Selected value:", selectedValue);
            // console.log("Is numeric:", !isNaN(parseInt(selectedValue)));
            
            if (selectedValue && !isNaN(parseInt(selectedValue)) && selectedValue.trim() !== '') {
                console.log("Loading categories for KML file");
                // Muat kategori untuk file KML yang dipilih
                loadCategoryCheckboxes(selectedValue);
            }
            
            loadData();
        });
        
        // Tambahkan event listener untuk memicu pencarian ketika dropdown berubah
        $('#filterKec, #filterKel').on('change', function() {
            var searchTerm = $('#searchPolygon').val();
            if (searchTerm) {
                // Beri jeda sebentar untuk memastikan data telah dimuat
                setTimeout(function() {
                    searchPolygon(searchTerm);
                }, 100);
            }
        });

        // Tambahkan event listener untuk pencarian polygon dengan debounce
        var searchTimeout;
        $('#searchPolygon').on('input', function() {
            var searchTerm = $(this).val().toLowerCase();
            
            // Clear timeout sebelumnya
            if (searchTimeout) {
                clearTimeout(searchTimeout);
            }
            
            // Set timeout baru untuk delay pencarian
            searchTimeout = setTimeout(function() {
                searchPolygon(searchTerm);
            }, 300); // Delay 300ms
        });

        // Event listener untuk membuka modal pencarian
        $('#openSearchModal').on('click', function() {
            $('#searchModal').modal('show');
        });

        // Event listener untuk tombol pencarian di modal
        $('#performSearch').on('click', function() {
            var searchTerm = $('#searchPolygon').val();
            if (searchTerm) {
                performSearch(searchTerm);
                $('#searchModal').modal('hide');
            }
        });

        // Inisialisasi feature group untuk drawing
        var drawnItems = new L.FeatureGroup().addTo(map);

        // Konfigurasi drawing tools - sembunyikan toolbar edit dan remove
        var drawControl = new L.Control.Draw({
            position: 'topright',
            draw: {
                polygon: {
                    allowIntersection: false,
                    drawError: {
                        color: '#e1e100',
                        message: '<strong>Error!<strong> tidak boleh berpotongan!'
                    },
                    shapeOptions: {
                        color: '#97009c'
                    }
                },
                polyline: false,
                circle: false,
                rectangle: false,
                marker: false,
                circlemarker: false
            },
            edit: {
                featureGroup: group, // Menggunakan group sebagai featureGroup untuk edit
                remove: false // Sembunyikan tombol remove
            }
        });


        // Hanya aktifkan drawing tools untuk admin
        <?php if (isset($user) && is_admin()): ?>

            map.addControl(drawControl);
        <?php endif; ?>

        // Sembunyikan toolbar edit dan remove setelah dibuat
        setTimeout(function() {
            var editToolbar = document.querySelector('.leaflet-draw-edit-edit');
            var removeToolbar = document.querySelector('.leaflet-draw-edit-remove');
            if (editToolbar) editToolbar.style.display = 'none';
            if (removeToolbar) removeToolbar.style.display = 'none';
        }, 100);

        // Tambahkan event listener untuk memeriksa apakah kelurahan sudah dipilih sebelum menggambar
        map.on('draw:created', function(e) {
            // Periksa apakah sudah ada kelurahan yang dipilih
            var selectedKelurahan = $('#filterKel').val();
            var selectedKelurahanText = $("#filterKel option:selected").text();

            if (!selectedKelurahan) {
                // Tampilkan notifikasi jika belum ada kelurahan yang dipilih
                var notificationModal = new bootstrap.Modal(document.getElementById('notificationModal'));
                notificationModal.show();
                return;
            }

            var type = e.layerType,
                layer = e.layer;

            if (type === 'polygon') {
                drawnItems.addLayer(layer);

                // Konfirmasi dan simpan data
                var name = prompt("Masukkan nama untuk polygon ini:", selectedKelurahanText.replace('[KML] ', ''));
                if (name !== null) {
                    savePolygon(layer, name, selectedKelurahan);
                }
            }
        });

        // Event handler untuk edit dan delete
        map.on('draw:edited', function(e) {
            var layers = e.layers;
            layers.eachLayer(function(layer) {
                updatePolygon(layer);
            });
        });

        map.on('draw:deleted', function(e) {
            var layers = e.layers;
            layers.eachLayer(function(layer) {
                deletePolygon(layer);
            });
        });

        // Event listener untuk tombol konfirmasi hapus
        $(document).ready(function() {
            $('#confirmDeleteBtn').on('click', function() {
                if (window.currentDeleteId) {
                    deletePolygon(window.currentDeleteId);
                }
            });
        });

        // Fungsi untuk menghapus polygon
        function deletePolygon(id) {
            // Sembunyikan modal konfirmasi hapus
            $('#deleteConfirmModal').modal('hide');

            $.ajax({
                url: '<?= base_url('index.php/map/delete_polygon') ?>/' + id,
                type: 'POST',
                success: function(response) {
                    showSuccessAlert('Polygon berhasil dihapus!');

                    // Hapus layer dari peta
                    if (layerMap && layerMap[id]) {
                        group.removeLayer(layerMap[id]);
                        delete layerMap[id];
                    }
                    loadData();
                },
                error: function(xhr, status, error) {
                    console.error('Delete polygon error:', xhr, status, error);
                    showErrorAlert('Gagal menghapus polygon: ' + (xhr.responseText || error));
                }
            });
        }

        // Event listener untuk tombol edit di modal instruksi
        $('#startEditBtn').on('click', function() {
            var editModal = bootstrap.Modal.getInstance(document.getElementById('editInstructionsModal'));
            if (editModal) {
                editModal.hide();
            }

            if (window.currentEditLayer) {
                // Aktifkan mode edit secara manual
                window.currentEditLayer.editing.enable();

                // Tampilkan tombol simpan perubahan
                var saveButtonContainer = document.getElementById('saveButtonContainer');
                if (saveButtonContainer) {
                    saveButtonContainer.style.display = 'block';
                }

                // Tampilkan pesan instruksi
                showSuccessAlert('Mode edit diaktifkan. Klik dan seret titik untuk mengedit polygon. Klik "Simpan Perubahan" ketika selesai.', 5000);
            } else {
                showErrorAlert('Tidak ada polygon yang sedang diedit');
            }
        });

        // Event listener untuk tombol simpan perubahan
        $('#saveChangesBtn').on('click', function() {
            if (window.currentEditLayer) {
                // Nonaktifkan mode edit
                window.currentEditLayer.editing.disable();

                // Sembunyikan tombol simpan perubahan
                document.getElementById('saveButtonContainer').style.display = 'none';

                // Update polygon di database
                updatePolygon(window.currentEditLayer);
            } else {
                showErrorAlert('Tidak ada polygon yang sedang diedit');
            }
        });

        // Fungsi untuk menyimpan polygon ke database
        function savePolygon(layer, name, kelurahan) {
            var geojson = layer.toGeoJSON();
            var color = layer.options.color || '#3388ff';

            $.ajax({
                url: '<?= base_url('index.php/map/save_polygon') ?>',
                type: 'POST',
                data: {
                    name: name,
                    kelurahan: kelurahan,
                    geometry: JSON.stringify(geojson.geometry),
                    color: color
                },
                success: function(response) {
                    showSuccessAlert('Polygon berhasil disimpan!');
                    // Hapus layer yang digambar dari drawnItems
                    drawnItems.removeLayer(layer);
                    // Reload data untuk menampilkan polygon yang baru saja disimpan
                    loadData();
                },
                error: function() {
                    showErrorAlert('Gagal menyimpan polygon!');
                }
            });
        }

        // Fungsi untuk mengupdate polygon di database
        function updatePolygon(layer) {
            // Dapatkan ID dari layer
            var id = null;
            for (var key in layerMap) {
                if (layerMap[key] === layer) {
                    id = key;
                    break;
                }
            }

            if (!id) {
                showErrorAlert('Polygon tidak memiliki ID, tidak dapat diupdate');
                return;
            }

            // Hapus label yang ada sebelum mengupdate
            if (layer.labels) {
                layer.labels.forEach(function(label) {
                    map.removeLayer(label);
                });
                layer.labels = [];
            }

            var geojson = layer.toGeoJSON();
            var color = layer.options.color || '#3388ff';

            // Periksa apakah ada warna baru dari input
            var colorInput = document.getElementById('polygonColor');
            if (colorInput) {
                color = colorInput.value;
            }

            $.ajax({
                url: '<?= base_url('index.php/map/update_polygon') ?>/' + id,
                type: 'POST',
                data: {
                    geometry: JSON.stringify(geojson.geometry),
                    color: color
                },
                success: function(response) {
                    try {
                        var result = JSON.parse(response);
                        if (result.success) {
                            showSuccessAlert('Polygon berhasil diupdate!');
                            // Update layer di peta tanpa reload
                            updateLayerOnMap(layer, id, geojson, color, layer.feature.properties);

                            // Tambahkan kembali label dengan 5 digit terakhir jika nama sesuai pola
                            var lastFiveDigits = extractLastFiveDigits(layer.feature.properties.name);
                            if (lastFiveDigits) {
                                // Buat label di tengah poligon
                                var label = L.marker(layer.getBounds().getCenter(), {
                                    icon: L.divIcon({
                                        className: 'polygon-label',
                                        html: '<div style="background: rgba(255, 255, 255, 0.8); padding: 2px 5px; border-radius: 3px; font-weight: bold; text-align: center;">' + lastFiveDigits + '</div>',
                                        iconSize: [50, 20]
                                    })
                                }).addTo(map);

                                // Tambahkan event listener untuk menampilkan detail ketika label diklik
                                label.on('click', function(e) {
                                    showPolygonDetail(id, layer.feature.properties, layer);
                                });

                                // Simpan referensi label
                                if (!layer.labels) layer.labels = [];
                                layer.labels.push(label);
                            }
                        } else {
                            throw new Error(result.message || 'Unknown error occurred');
                        }
                    } catch (e) {
                        console.error('Error parsing response:', e);
                        showErrorAlert('Gagal mengupdate polygon: Format respons tidak valid');
                        loadData();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Update polygon error:', xhr, status, error);
                    showErrorAlert('Gagal mengupdate polygon: ' + (xhr.responseText || error));
                    // Reload data jika gagal
                    loadData();
                }
            });
        }

        // Fungsi untuk menampilkan detail polygon
        function showPolygonDetail(id, properties, layer) {
            var detailModal = document.getElementById('polygonDetailModal');
            var propertiesContainer = document.getElementById('polygonProperties');

            // Bangun konten properties
            var propertiesHtml = '';
            if (properties) {
                // Tampilkan kategori dengan lebih menonjol
                if (properties.kategori) {
                    propertiesHtml += '<div class="property-item">' +
                        '<div class="property-label">Kategori</div>' +
                        '<div class="property-value"><span class="badge bg-primary">' + properties.kategori + '</span></div>' +
                        '</div>';
                }
                
                for (var key in properties) {
                    if (properties.hasOwnProperty(key)) {
                        // Lewati kategori karena sudah ditampilkan di atas
                        if (key === 'kategori') continue;
                        
                        // Khusus untuk field properties, parse dan tampilkan dengan format yang lebih baik
                        // console.log('Processing property:', key);
                        if (key === 'properties' && typeof properties[key] === 'string') {
                            try {
                                // Coba parse sebagai JSON dulu
                                var parsedProps = JSON.parse(properties[key]);
                                // console.log('Parsed properties:', parsedProps);
                                propertiesHtml += '<div class="property-item">' +
                                    '<div class="property-value">';

                                for (var propKey in parsedProps) {
                                    propertiesHtml += '<div><strong>' + propKey + ':</strong> ' + (parsedProps[propKey] || '-') + '</div><br>';
                                }
                                // console.log(propertiesHtml);

                                propertiesHtml += '</div></div>';
                            } catch (e) {
                                // Jika bukan JSON, tampilkan sebagai teks biasa dengan format yang lebih baik
                                var formattedText = formatPropertiesText(properties[key]);
                                propertiesHtml += '<div class="property-item">' +
                                    '<div class="property-label">Properties</div>' +
                                    '<div class="property-value">' + formattedText + '</div>' +
                                    '</div>';
                            }
                        } else {
                            propertiesHtml += '<div class="property-item">' +
                                '<div class="property-label">' + key.charAt(0).toUpperCase() + key.slice(1) + '</div>' +
                                '<div class="property-value">' + (properties[key] || '-') + '</div>' +
                                '</div>';
                        }
                    }
                }
            }

            // Jika tidak ada properties, tampilkan pesan
            if (!propertiesHtml) {
                propertiesHtml = '<p>Tidak ada detail properties tersedia.</p>';
            }

            propertiesContainer.innerHTML = propertiesHtml;

            // Simpan ID polygon yang sedang ditampilkan untuk aksi edit/hapus
            window.currentPolygonId = id;
            window.currentPolygonLayer = layer;

            // Tampilkan modal
            detailModal.classList.add('show');
        }

        // Fungsi untuk memformat teks properties
        function formatPropertiesText(text) {
            if (!text) return '-';

            // Ganti karakter newline dengan break tag
            var formatted = text.replace(/\\n/g, '<br>');

            // Jika teks mengandung pola key-value dengan tab, format dengan lebih baik
            if (formatted.indexOf('\t') !== -1) {
                var lines = formatted.split('<br>');
                var result = '<div class="formatted-properties">';

                lines.forEach(function(line) {
                    if (line.trim() !== '') {
                        var parts = line.split('\t');
                        if (parts.length >= 2) {
                            result += '<div><strong>' + parts[0] + ':</strong> ' + parts[1] + '</div>';
                        } else {
                            result += '<div>' + line + '</div>';
                        }
                    }
                });

                result += '</div>';
                return result;
            }

            return formatted;
        }

        // Fungsi untuk menyembunyikan modal detail
        function hidePolygonDetail() {
            var detailModal = document.getElementById('polygonDetailModal');
            detailModal.classList.remove('show');
        }

        // Event listener untuk tombol close modal
        var closeDetailModal = document.getElementById('closeDetailModal');
        if (closeDetailModal) {
            closeDetailModal.addEventListener('click', function() {
                hidePolygonDetail();
            });
        }

        // Event listener untuk tombol edit
        $('#editPolygonBtn').on('click', function() {
            if (window.currentPolygonId && window.currentPolygonLayer) {
                // Sembunyikan modal detail
                hidePolygonDetail();

                // Panggil fungsi edit polygon
                window.editPolygon(window.currentPolygonId);
            }
        });

        // Event listener untuk tombol hapus
        $('#deletePolygonBtn').on('click', function() {
            if (window.currentPolygonId) {
                // Sembunyikan modal detail
                hidePolygonDetail();

                // Tampilkan modal konfirmasi hapus
                window.deletePolygonById(window.currentPolygonId);
            }
        });

        // Fungsi untuk menyorot polygon yang dipilih
        function highlightPolygon(polygonId, headId) {
            // Pastikan layerMap dan map sudah terdefinisi
            if (typeof layerMap === 'undefined' || typeof map === 'undefined') {
                console.error('layerMap atau map belum terdefinisi');
                return;
            }
            
            // Reset semua polygon ke style awal
            resetPolygonHighlight();
            
            // Jika headId diberikan, muat semua polygon dengan head_id tersebut terlebih dahulu
            if (headId && !isNaN(headId)) {
                // Set filter kelurahan ke headId (ID file KML)
                $('#filterKel').val(headId).trigger('change');
                
                // Tunggu sebentar untuk memuat data, lalu sorot polygon
                setTimeout(function() {
                    // Sorot semua polygon dengan head_id yang sama
                    highlightAllPolygonsWithHeadId(headId, polygonId);
                }, 1500); // Memberi waktu lebih untuk memuat data
            } else {
                // Jika tidak ada headId, langsung sorot polygon
                actuallyHighlightPolygon(polygonId);
            }
        }
        
        // Fungsi baru untuk menyorot semua polygon dengan head_id tertentu
        function highlightAllPolygonsWithHeadId(headId, targetPolygonId) {
            var highlightedLayers = [];
            var targetLayer = null;

            // Pastikan layerMap dan map sudah terdefinisi
            if (typeof layerMap === 'undefined' || typeof map === 'undefined') {
                console.error('layerMap atau map belum terdefinisi');
                return;
            }

            // Reset semua polygon ke style awal
            resetPolygonHighlight();

            // Sorot semua polygon dengan head_id yang sama
            group.eachLayer(function(layer) {
                // Pastikan layer memiliki feature dan properties
                if (layer.feature && layer.feature.properties) {
                    // Cek apakah layer ini memiliki head_id yang sesuai
                    if (layer.feature.properties.id == targetPolygonId) {
                        targetLayer = layer;
                    }
                    
                    // Cek apakah layer ini berasal dari KML dengan head_id yang sesuai
                    if (layer.feature.properties.head_id == headId) {
                        // Gunakan warna asli dari database
                        var originalColor = layer.options.originalColor || '#3388ff';
                        layer.setStyle({
                            fillOpacity: 0.4,
                            weight: 2,
                            color: originalColor
                        });
                        
                        // Simpan layer yang disorot
                        highlightedLayers.push(layer);
                        
                        // Jika ini adalah polygon target, sorot dengan gaya berbeda
                        if (layer.feature.properties.id == targetPolygonId) {
                            layer.setStyle({
                                fillOpacity: 0.7,
                                weight: 4,
                                color: originalColor
                            });
                        }
                    }
                }
            });
            
            // Jika tidak menemukan polygon dengan head_id yang sama dalam layer yang sudah ada,
            // coba muat polygon target secara spesifik
            if (highlightedLayers.length === 0 && targetLayer === null) {
                loadPolygonFromDatabase(targetPolygonId);
                return;
            }
            
            // Jika ditemukan polygon dengan head_id yang sama, fokuskan ke semua polygon tersebut
            if (highlightedLayers.length > 0) {
                // Tunggu sejenak untuk memastikan styling diterapkan
                setTimeout(function() {
                    var groupBounds = new L.featureGroup(highlightedLayers).getBounds();
                    if (groupBounds.isValid()) {
                        map.fitBounds(groupBounds, { 
                            padding: [50, 50],
                            maxZoom: 18
                        });
                    }
                    
                    // Jika ada target layer, fokuskan lebih spesifik ke target tersebut
                    if (targetLayer) {
                        setTimeout(function() {
                            try {
                                var bounds = targetLayer.getBounds();
                                if (bounds.isValid()) {
                                    map.flyToBounds(bounds, {
                                        padding: [30, 30],
                                        maxZoom: 19
                                    });
                                }
                            } catch (e) {
                                console.error('Error focusing on target polygon:', e);
                            }
                        }, 300);
                    }
                }, 100);
            } else if (targetLayer) {
                // Jika hanya ada target layer, fokuskan ke sana
                actuallyHighlightPolygon(targetPolygonId);
            } else {
                // Jika tidak ditemukan, sorot hanya polygon target
                loadPolygonFromDatabase(targetPolygonId);
            }
        }

        // 新增函数：根据 head_id 从数据库加载多边形
        function loadPolygonsFromDatabaseByHeadId(headId, targetPolygonId) {
            $.ajax({
                url: '<?= base_url("index.php/map/data_by_head_id") ?>',
                type: 'GET',
                data: { head_id: headId },
                success: function(response) {
                    if (response && response.length > 0) {
                        response.forEach(function(polygonData) {
                            try {
                                if (polygonData.geometry) {
                                    var gj = JSON.parse(polygonData.geometry);
                                    var layer = L.geoJSON(gj, {
                                        style: styleFor(polygonData.color)
                                    });

                                    // 添加数据到 layer
                                    layer.eachLayer(function(l) {
                                        // 保存原始颜色
                                        l.options.originalColor = polygonData.color || '#3388ff';

                                        l.feature = {};
                                        l.feature.properties = {
                                            id: polygonData.id,
                                            name: polygonData.name,
                                            kelurahan: polygonData.kelurahan,
                                            kecamatan_id: polygonData.kecamatan_id,
                                            properties: polygonData.properties,
                                            head_id: polygonData.head_id // 添加 head_id 属性
                                        };

                                        // 保存 layer 引用
                                        layerMap[polygonData.id] = l;

                                        // 添加到 group
                                        l.addTo(group);

                                        // 添加标签（如果有）
                                        var lastFiveDigits = extractLastFiveDigits(polygonData.name);
                                        if (lastFiveDigits) {
                                            setTimeout(function() {
                                                try {
                                                    if (l.getBounds && l.getBounds().isValid && l.getBounds().isValid()) {
                                                        var center = l.getBounds().getCenter();

                                                        // 创建标签
                                                        var label = L.marker(center, {
                                                            icon: L.divIcon({
                                                                className: 'polygon-label',
                                                                html: '<div style="background: rgba(255, 255, 255, 0.9); padding: 2px 5px; border-radius: 3px; font-weight: bold; text-align: center; box-shadow: 0 1px 2px rgba(0,0,0,0.3);">' + lastFiveDigits + '</div>',
                                                                iconSize: [50, 20]
                                                            }),
                                                            zIndexOffset: 1000
                                                        }).addTo(map);

                                                        // 添加点击事件以显示详情
                                                        label.on('click', function(e) {
                                                            showPolygonDetail(polygonData.id, l.feature.properties, l);
                                                        });

                                                        // 保存标签引用
                                                        if (!l.labels) l.labels = [];
                                                        l.labels.push(label);
                                                    }
                                                } catch (e) {
                                                    console.error('为多边形 ' + polygonData.id + ' 创建标签时出错:', e);
                                                }
                                            }, 10);
                                        }

                                        // 添加点击事件以显示详情
                                        l.on('click', function(e) {
                                            showPolygonDetail(polygonData.id, l.feature.properties, l);
                                        });

                                        // Jika adalah target polygon, sorot dengan gaya berbeda
                                        if (polygonData.id == targetPolygonId) {
                                            l.setStyle({
                                                fillOpacity: 0.6,
                                                weight: 5,
                                                color: '#FF0000'
                                            });
                                        } else {
                                            l.setStyle({
                                                fillOpacity: 0.4,
                                                weight: 3,
                                                color: '#FFA500'
                                            });
                                        }
                                    });
                                }
                            } catch (e) {
                                console.error('解析项目几何信息时出错:', polygonData, e);
                                showErrorAlert('加载多边形失败: ' + e.message);
                            }
                        });

                        // 调整地图视图以展示所有相关多边形
                        var relatedLayers = [];
                        group.eachLayer(function(layer) {
                            if (layer.feature && layer.feature.properties && layer.feature.properties.head_id == headId) {
                                relatedLayers.push(layer);
                            }
                        });

                        if (relatedLayers.length > 0) {
                            var groupBounds = new L.featureGroup(relatedLayers).getBounds();
                            if (groupBounds.isValid()) {
                                map.fitBounds(groupBounds, { 
                                    padding: [50, 50],
                                    maxZoom: 18
                                });
                            }
                        }
                    } else {
                        showErrorAlert('未找到具有相同 head_id 的多边形');
                    }
                },
                error: function(xhr, status, error) {
                    console.error("根据 head_id 加载多边形时失败:", status, error);
                    showErrorAlert("根据 head_id 加载多边形时失败: " + error);
                }
            });
        }

        // Fungsi untuk memuat data untuk kelurahan tertentu
        function loadDataForKelurahan(kelurahan, callback) {
            // Simpan filter saat ini
            var currentKelFilter = $('#filterKel').val();
            var currentKecFilter = $('#filterKec').val();
            
            // Set filter kelurahan
            $('#filterKel').val(kelurahan);
            
            // Muat data
            loadData();
            
            // Kembalikan filter setelah selesai (jika diperlukan)
            if (callback) {
                // Tunggu sebentar untuk memastikan data dimuat
                setTimeout(callback, 500);
            }
        }
        
        // Fungsi aktual untuk menyorot polygon
        function actuallyHighlightPolygon(polygonId) {
            // Temukan polygon berdasarkan ID
            var layer = layerMap[polygonId];
            
            // Jika polygon tidak ditemukan di layer saat ini, ambil dari database
            if (!layer) {
                loadPolygonFromDatabase(polygonId);
            } else {
                // Sorot polygon yang ditemukan
                layer.setStyle({
                    fillOpacity: 0.6,
                    weight: 4,
                    color: layer.options.originalColor || '#3388ff'
                });
                
                // Fokuskan peta ke polygon yang dipilih
                setTimeout(function() {
                    try {
                        var bounds = layer.getBounds();
                        if (bounds.isValid()) {
                            map.fitBounds(bounds, {
                                padding: [50, 50],
                                maxZoom: 18
                            });
                        }
                    } catch (e) {
                        console.error('Error focusing on polygon:', e);
                    }
                }, 50);
                
                // Simpan referensi polygon yang disorot
                window.highlightedPolygon = polygonId;
            }
        }
        
        // Fungsi untuk memuat polygon dari database
        function loadPolygonFromDatabase(polygonId) {
            $.ajax({
                url: '<?= base_url("index.php/map/data") ?>',
                type: 'GET',
                data: { polygon_id: polygonId },
                success: function(response) {
                    if (response && response.length > 0) {
                        var polygonData = response[0];

                        try {
                            if (polygonData.geometry) {
                                var gj = JSON.parse(polygonData.geometry);
                                var layer = L.geoJSON(gj, {
                                    style: styleFor(polygonData.color)
                                });

                                // Tambahkan data ke layer
                                layer.eachLayer(function(l) {
                                    // Simpan warna asli
                                    l.options.originalColor = polygonData.color || '#3388ff';

                                    l.feature = {};
                                    l.feature.properties = {
                                        id: polygonData.id,
                                        name: polygonData.name,
                                        kelurahan: polygonData.kelurahan,
                                        kecamatan_id: polygonData.kecamatan_id,
                                        properties: polygonData.properties
                                    };

                                    // Simpan referensi layer
                                    layerMap[polygonData.id] = l;

                                    // Tambahkan ke group terlebih dahulu
                                    l.addTo(group);
                                    // Tambahkan label dengan 5 digit terakhir jika nama sesuai pola
                                    var lastFiveDigits = extractLastFiveDigits(polygonData.name);
                                    if (lastFiveDigits) {
                                        // Tunggu sampai layer benar-benar ditambahkan ke peta
                                        setTimeout(function() {
                                            try {
                                                if (l.getBounds && l.getBounds().isValid && l.getBounds().isValid()) {
                                                    var center = l.getBounds().getCenter();

                                                    // Buat label di tengah poligon
                                                    var label = L.marker(center, {
                                                        icon: L.divIcon({
                                                            className: 'polygon-label',
                                                            html: '<div style="background: rgba(255, 255, 255, 0.9); padding: 2px 5px; border-radius: 3px; font-weight: bold; text-align: center; box-shadow: 0 1px 2px rgba(0,0,0,0.3);">' + lastFiveDigits + '</div>',
                                                            iconSize: [50, 20]
                                                        }),
                                                        // Pastikan label muncul di atas semua layer lain
                                                        zIndexOffset: 1000
                                                    }).addTo(map);

                                                    // Tambahkan event listener untuk menampilkan detail ketika label diklik
                                                    label.on('click', function(e) {
                                                        showPolygonDetail(polygonData.id, l.feature.properties, l);
                                                    });

                                                    // Simpan referensi label agar bisa dihapus nanti
                                                    if (!l.labels) l.labels = [];
                                                    l.labels.push(label);
                                                }
                                            } catch (e) {
                                                console.error('Error creating label for polygon ' + polygonData.id + ':', e);
                                            }
                                        }, 10);
                                    }

                                    // Tambahkan event klik untuk menampilkan detail
                                    l.on('click', function(e) {
                                        showPolygonDetail(polygonData.id, l.feature.properties, l);
                                    });

                                    // Sorot polygon yang baru dimuat dengan warna asli dari database
                                    l.setStyle({
                                        fillOpacity: 0.6,
                                        weight: 4,
                                        color: polygonData.color || '#3388ff'
                                    });

                                    // Fokuskan peta ke polygon yang dipilih
                                    setTimeout(function() {
                                        try {
                                            var bounds = l.getBounds();
                                            if (bounds.isValid()) {
                                                map.fitBounds(bounds, {
                                                    padding: [50, 50],
                                                    maxZoom: 18
                                                });
                                            }
                                        } catch (e) {
                                            console.error('Error focusing on polygon:', e);
                                        }
                                    }, 50);

                                    // Simpan referensi polygon yang sedang disorot
                                    window.highlightedPolygon = polygonData.id;
                                });
                            }
                        } catch (e) {
                            console.error('Error parsing geometry for item:', polygonData, e);
                            showErrorAlert('Gagal memuat polygon: ' + e.message);
                        }
                    } else {
                        showErrorAlert('Polygon tidak ditemukan di database');
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Failed to load polygon from database:", status, error);
                    showErrorAlert("Gagal memuat polygon dari database: " + error);
                }
            });
        }
        
        // Fungsi aktual untuk memuat polygon dari database
        function actualLoadPolygon(polygonId) {
            $.ajax({
                url: '<?= base_url("index.php/map/data") ?>',
                type: 'GET',
                data: { polygon_id: polygonId },
                success: function(response) {
                    if (response && response.length > 0) {
                        var polygonData = response[0];

                        try {
                            if (polygonData.geometry) {
                                var gj = JSON.parse(polygonData.geometry);
                                var layer = L.geoJSON(gj, {
                                    style: styleFor(polygonData.color)
                                });

                                // Tambahkan data ke layer
                                layer.eachLayer(function(l) {
                                    // Simpan warna asli
                                    l.options.originalColor = polygonData.color || '#3388ff';

                                    l.feature = {};
                                    l.feature.properties = {
                                        id: polygonData.id,
                                        name: polygonData.name,
                                        kelurahan: polygonData.kelurahan,
                                        kecamatan_id: polygonData.kecamatan_id,
                                        properties: polygonData.properties
                                    };

                                    // Simpan referensi layer
                                    layerMap[polygonData.id] = l;

                                    // Tambahkan ke group terlebih dahulu
                                    l.addTo(group);
                                    // console.log("Added polygon with ID:", polygonData);
                                    // Tambahkan label dengan 5 digit terakhir jika nama sesuai pola
                                    var lastFiveDigits = extractLastFiveDigits(polygonData.name);
                                    // console.log("Last five digits extracted:", polygonData.name, lastFiveDigits);
                                    if (lastFiveDigits) {
                                        // Tunggu sampai layer benar-benar ditambahkan ke peta
                                        setTimeout(function() {
                                            try {
                                                if (l.getBounds && l.getBounds().isValid && l.getBounds().isValid()) {
                                                    var center = l.getBounds().getCenter();

                                                    // Buat label di tengah poligon
                                                    var label = L.marker(center, {
                                                        icon: L.divIcon({
                                                            className: 'polygon-label',
                                                            html: '<div style="background: rgba(255, 255, 255, 0.9); padding: 2px 5px; border-radius: 3px; font-weight: bold; text-align: center; box-shadow: 0 1px 2px rgba(0,0,0,0.3);">' + lastFiveDigits + '</div>',
                                                            iconSize: [50, 20]
                                                        }),
                                                        // Pastikan label muncul di atas semua layer lain
                                                        zIndexOffset: 1000
                                                    }).addTo(map);

                                                    // Tambahkan event listener untuk menampilkan detail ketika label diklik
                                                    label.on('click', function(e) {
                                                        showPolygonDetail(polygonData.id, l.feature.properties, l);
                                                    });

                                                    // Simpan referensi label agar bisa dihapus nanti
                                                    if (!l.labels) l.labels = [];
                                                    l.labels.push(label);
                                                }
                                            } catch (e) {
                                                console.error('Error creating label for polygon ' + polygonData.id + ':', e);
                                            }
                                        }, 10);
                                    }

                                    // Tambahkan event klik untuk menampilkan detail
                                    l.on('click', function(e) {
                                        showPolygonDetail(polygonData.id, l.feature.properties, l);
                                    });

                                    // Sorot polygon yang baru dimuat dengan warna asli dari database
                                    l.setStyle({
                                        fillOpacity: 0.6,
                                        weight: 4,
                                        color: polygonData.color || '#3388ff'
                                    });

                                    // Fokuskan peta ke polygon yang dipilih
                                    setTimeout(function() {
                                        try {
                                            var bounds = l.getBounds();
                                            if (bounds.isValid()) {
                                                map.fitBounds(bounds, {
                                                    padding: [50, 50],
                                                    maxZoom: 18
                                                });
                                            }
                                        } catch (e) {
                                            console.error('Error focusing on polygon:', e);
                                        }
                                    }, 50);

                                    // Simpan referensi polygon yang sedang disorot
                                    window.highlightedPolygon = polygonData.id;
                                    
                                    // Pastikan polygon muncul di atas layer kecamatan
                                    map.panTo(l.getBounds().getCenter());
                                });
                            }
                        } catch (e) {
                            console.error('Error parsing geometry for item:', polygonData, e);
                            showErrorAlert('Gagal memuat polygon: ' + e.message);
                        }
                    } else {
                        showErrorAlert('Polygon tidak ditemukan di database');
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Failed to load polygon from database:", status, error);
                    showErrorAlert("Gagal memuat polygon dari database: " + error);
                }
            });
        }
        
        // Fungsi untuk mereset sorotan polygon
        function resetPolygonHighlight() {
            group.eachLayer(function(layer) {
                // Kembalikan style ke default
                if (layer.options.originalColor) {
                    layer.setStyle({
                        fillOpacity: 0.2,
                        weight: 2,
                        color: layer.options.originalColor
                    });
                } else {
                    layer.setStyle({
                        fillOpacity: 0.2,
                        weight: 2,
                        color: layer.options.color || '#3388ff'
                    });
                }
                
                // Hapus label pencarian yang mungkin ada
                if (layer.searchLabel) {
                    if (map.hasLayer(layer.searchLabel)) {
                        map.removeLayer(layer.searchLabel);
                    }
                    // Hapus juga dari array labels jika ada
                    if (layer.labels) {
                        var index = layer.labels.indexOf(layer.searchLabel);
                        if (index > -1) {
                            layer.labels.splice(index, 1);
                        }
                    }
                    delete layer.searchLabel;
                }
            });
            
            // Hapus referensi polygon yang disorot
            window.highlightedPolygon = null;
        }
        
        // Fungsi untuk memuat data polygon
        function loadData() {
            group.clearLayers();
            layerMap = {}; // Reset layer map
            var k = $('#filterKel').val();
            var kec = $('#filterKec').val();

            // Hapus semua label yang ada
            $('.polygon-label').remove();

            // Jika tidak ada filter yang dipilih, gunakan default kelurahan (Karimun)
            if (!k && typeof default_kelurahan !== 'undefined' && !kec) {
                // Set nilai filter ke default kelurahan hanya jika tidak ada filter kecamatan
                $('#filterKel').val(default_kelurahan);
                k = default_kelurahan;
            }

            // Tentukan URL berdasarkan filter
            var url;
            if (k && !isNaN(k)) {
                // Jika kelurahan adalah ID file KML (angka)
                // Periksa apakah ada kategori yang dipilih
                var selectedCategories = [];
                $('input[name="categoryFilter"]:checked').each(function() {
                    selectedCategories.push($(this).val());
                });
                
                if (selectedCategories.length > 0) {
                    // Jika ada kategori yang dipilih, gunakan endpoint filtered
                    url = '<?= base_url('index.php/map/kml_filtered_data') ?>?head_id=' + k + '&categories[]=' + selectedCategories.join('&categories[]=');
                } else {
                    // Jika tidak ada kategori yang dipilih, gunakan endpoint biasa
                    url = '<?= base_url('index.php/map/kml_data/') ?>' + k;
                }
            } else if (k) {
                // Jika kelurahan biasa (bukan angka)
                url = '<?= base_url('index.php/map/data') ?>?kelurahan=' + encodeURIComponent(k);
            } else if (kec) {
                // Jika hanya kecamatan yang dipilih
                url = '<?= base_url('index.php/map/data') ?>?kecamatan=' + encodeURIComponent(kec);
            } else {
                // Jika tidak ada filter
                url = '<?= base_url('index.php/map/data') ?>';
            }

            $.getJSON(url, function(rows) {
                if (rows && Array.isArray(rows)) {
                    rows.forEach(function(r) {
                        try {
                            if (r.geometry) {
                                var gj = JSON.parse(r.geometry);
                                var layer = L.geoJSON(gj, {
                                    style: styleFor(r.color)
                                });

                                // Tambahkan data ke layer
                                layer.eachLayer(function(l) {
                                    // Simpan warna asli
                                    l.options.originalColor = r.color || '#3388ff';
                                    
                                    l.feature = {};
                                    l.feature.properties = {
                                        id: r.id,
                                        name: r.name,
                                        kelurahan: r.kelurahan,
                                        kecamatan_id: r.kecamatan_id,
                                        properties: r.properties
                                    };

                                    // Simpan referensi layer
                                    layerMap[r.id] = l;

                                    // console.log("Added polygon with ID:", r.id);
                                    // Tambahkan label dengan 5 digit terakhir jika nama sesuai pola
                                    var lastFiveDigits = extractLastFiveDigits(r.name);
                                    if (lastFiveDigits) {
                                        // Buat label di tengah poligon
                                        var label = L.marker(l.getBounds().getCenter(), {
                                            icon: L.divIcon({
                                                className: 'polygon-label',
                                                html: '<div style="background: rgba(255, 255, 255, 0.8); padding: 2px 5px; border-radius: 3px; font-weight: bold; text-align: center;">' + lastFiveDigits + '</div>',
                                                iconSize: [50, 20]
                                            })
                                        }).addTo(map);

                                        // Tambahkan event listener untuk menampilkan detail ketika label diklik
                                        label.on('click', function(e) {
                                            showPolygonDetail(r.id, l.feature.properties, l);
                                        });

                                        // Simpan referensi label agar bisa dihapus nanti
                                        if (!l.labels) l.labels = [];
                                        l.labels.push(label);
                                    }

                                    // Tambahkan event klik untuk menampilkan detail
                                    l.on('click', function(e) {
                                        showPolygonDetail(r.id, l.feature.properties, l);
                                    });
                                });

                                layer.addTo(group);
                            }
                        } catch (e) {
                            console.error('Error parsing geometry for item:', r, e);
                        }
                    });

                    if (group.getBounds().isValid()) {
                        map.fitBounds(group.getBounds());
                    }
                    
                    // Terapkan pencarian yang sedang aktif setelah data dimuat
                    var searchTerm = $('#searchPolygon').val();
                    if (searchTerm) {
                        searchPolygon(searchTerm);
                    }
                }
            }).fail(function(jqxhr, textStatus, error) {
                console.error("Failed to load data:", textStatus, error);
                showErrorAlert("Gagal memuat data peta: " + textStatus);
            });
        }

        // Fungsi untuk mencari polygon berdasarkan nama
        function searchPolygon(searchTerm) {
            // Reset warna semua polygon ke warna asli
            group.eachLayer(function(layer) {
                // Simpan warna asli jika belum ada
                if (!layer.options.originalColor && layer.options.color) {
                    layer.options.originalColor = layer.options.color;
                }
                
                // Kembalikan style ke default
                layer.setStyle({
                    fillOpacity: 0.2,
                    weight: 2,
                    color: layer.options.originalColor || '#3388ff'
                });
                
                // Hapus label pencarian yang mungkin ada
                if (layer.searchLabel) {
                    if (map.hasLayer(layer.searchLabel)) {
                        map.removeLayer(layer.searchLabel);
                    }
                    delete layer.searchLabel;
                }
            });

            // Jika tidak ada kata kunci pencarian, hanya reset tampilan tanpa zoom
            if (!searchTerm) return;

            // Dapatkan nilai filter yang dipilih
            var selectedKelurahan = $('#filterKel').val();
            var selectedKecamatan = $('#filterKec').val();

            // Cari polygon yang sesuai dengan kata kunci
            var foundLayers = [];
            searchTerm = searchTerm.toLowerCase();
            
            group.eachLayer(function(layer) {
                // Pastikan layer memiliki feature dan properties
                if (layer.feature?.properties?.name) {
                    var name = layer.feature.properties.name.toString().toLowerCase();
                    
                    // Periksa apakah nama mengandung kata kunci pencarian
                    if (name.includes(searchTerm)) {
                        // Jika ada filter kelurahan, hanya tampilkan polygon dari kelurahan yang dipilih
                        if (selectedKelurahan && layer.feature.properties.kelurahan) {
                            // Untuk kelurahan biasa
                            if (layer.feature.properties.kelurahan === selectedKelurahan) {
                                // Sorot polygon yang ditemukan dengan warna asli dari database
                                layer.setStyle({
                                    fillOpacity: 0.6,
                                    weight: 4,
                                    color: layer.options.originalColor || '#3388ff'
                                });
                                
                                // Simpan layer yang ditemukan
                                foundLayers.push(layer);
                            }
                        } 
                        // Jika ada filter kecamatan, hanya tampilkan polygon dari kecamatan yang dipilih
                        else if (selectedKecamatan && layer.feature.properties.kecamatan_id) {
                            if (layer.feature.properties.kecamatan_id == selectedKecamatan) {
                                // Sorot polygon yang ditemukan dengan warna asli dari database
                                layer.setStyle({
                                    fillOpacity: 0.6,
                                    weight: 4,
                                    color: layer.options.originalColor || '#3388ff'
                                });
                                
                                // Simpan layer yang ditemukan
                                foundLayers.push(layer);
                            }
                        }
                        // Jika tidak ada filter, tampilkan semua polygon yang cocok
                        else if (!selectedKelurahan && !selectedKecamatan) {
                            // Sorot polygon yang ditemukan dengan warna asli dari database
                            layer.setStyle({
                                fillOpacity: 0.6,
                                weight: 4,
                                color: layer.options.originalColor || '#3388ff'
                            });
                            
                            // Simpan layer yang ditemukan
                            foundLayers.push(layer);
                        }
                    }
                }
            });

            // Jika ditemukan polygon, fokuskan ke polygon yang ditemukan
            if (foundLayers.length > 0) {
                // Tambahkan label untuk hasil pencarian
                setTimeout(function() {
                    foundLayers.forEach(function(layer) {
                        // Hapus label pencarian yang sudah ada
                        if (layer.searchLabel) {
                            if (map.hasLayer(layer.searchLabel)) {
                                map.removeLayer(layer.searchLabel);
                            }
                            // Hapus juga dari array labels jika ada
                            if (layer.labels) {
                                var index = layer.labels.indexOf(layer.searchLabel);
                                if (index > -1) {
                                    layer.labels.splice(index, 1);
                                }
                            }
                            delete layer.searchLabel;
                        }
                        
                        // Buat label baru dengan 5 digit terakhir dari nama
                        var name = layer.feature.properties.name;
                        var labelContent = extractLastFiveDigits(name);
                        
                        if (labelContent) {
                            // Gunakan pendekatan yang lebih konsisten dengan loadData
                            setTimeout(function() {
                                try {
                                    if (layer.getBounds && layer.getBounds().isValid && layer.getBounds().isValid()) {
                                        // Buat label di tengah poligon dengan styling yang konsisten
                                        var searchLabel = L.marker(layer.getBounds().getCenter(), {
                                            icon: L.divIcon({
                                                className: 'polygon-label',
                                                html: '<div style="background: rgba(255, 255, 255, 0.8); padding: 2px 5px; border-radius: 3px; font-weight: bold; text-align: center;">' + labelContent + '</div>',
                                                iconSize: [50, 20]
                                            }),
                                            // Pastikan label muncul di atas semua layer lain
                                            zIndexOffset: 1000
                                        }).addTo(map);
                                        
                                        // Tambahkan event listener untuk menampilkan detail ketika label diklik
                                        searchLabel.on('click', function(e) {
                                            showPolygonDetail(layer.feature.properties.id, layer.feature.properties, layer);
                                        });
                                        
                                        // Simpan referensi label agar bisa dihapus nanti
                                        if (!layer.labels) layer.labels = [];
                                        layer.labels.push(searchLabel);
                                        
                                        // Simpan juga sebagai searchLabel untuk manajemen khusus
                                        layer.searchLabel = searchLabel;
                                        
                                        // Pastikan label muncul di atas layer kecamatan
                                        map.panTo(layer.getBounds().getCenter());
                                    }
                                } catch(e) {
                                    console.error('Error creating search label:', e);
                                }
                            }, 10);
                        }
                    });
                }, 100);
                
                var groupBounds = new L.featureGroup(foundLayers).getBounds();
                if (groupBounds.isValid()) {
                    map.fitBounds(groupBounds, { 
                        padding: [50, 50],
                        maxZoom: 18,
                        duration: 0.5
                    });
                }
                
                // Tampilkan pesan jumlah hasil yang ditemukan
                showInfoAlert('Ditemukan ' + foundLayers.length + ' polygon');
            } else if (searchTerm) {
                // Jika tidak ada hasil pencarian
                showInfoAlert('Tidak ditemukan polygon dengan nama "' + searchTerm + '"');
            }
        }
        
        // Fungsi tambahan untuk menampilkan alert info
        function showInfoAlert(message) {
            $('#infoMessage').text(message);
            $('#infoAlert').show();
            setTimeout(function() {
                $('#infoAlert').hide();
            }, 3000);
        }

        // Fungsi global untuk edit polygon (hanya untuk admin)
        window.editPolygon = function(id, element) {
            // Hanya admin yang bisa mengedit polygon
            <?php if (!isset($user) || !is_admin()): ?>
                return;
            <?php endif; ?>

            // Sembunyikan modal detail
            hidePolygonDetail();

            // Dapatkan layer
            var layer = layerMap[id];
            if (!layer) {
                showErrorAlert('Polygon tidak ditemukan');
                return;
            }

            // Simpan layer yang sedang diedit
            window.currentEditLayer = layer;
            window.currentEditLayerId = id;

            // Set nilai color picker ke warna polygon saat ini
            var colorInput = document.getElementById('polygonColor');
            if (colorInput) {
                colorInput.value = layer.options.color || '#3388ff';
            }

            // Tampilkan modal edit
            var editModal = new bootstrap.Modal(document.getElementById('editInstructionsModal'));
            editModal.show();
        };

        // Fungsi global untuk hapus polygon (hanya untuk admin)
        window.deletePolygonById = function(id) {
            // Hanya admin yang bisa menghapus polygon
            <?php if (!isset($user) || !is_admin()): ?>
                return;
            <?php endif; ?>

            // Sembunyikan modal detail
            hidePolygonDetail();

            // Simpan ID untuk penghapusan
            window.currentDeleteId = id;

            // Tampilkan modal konfirmasi secara langsung
            $('#deleteConfirmModal').modal('show');
        };


        function styleFor(color) {
            return {
                color: color || '#3388ff',
                weight: 2,
                fillOpacity: .2
            };
        }

        function updateLayerOnMap(layer, id, geojson, color, properties) {
            // Update style layer dengan warna baru
            layer.setStyle({
                color: color,
                weight: 2,
                fillOpacity: .2
            });

            // Update popup content jika ada
            if (layer.getPopup()) {
                var popupContent = '<div class="polygon-popup-content">' +
                    '<strong>' + (properties.name || properties.kelurahan) + '</strong><br>' +
                    '<button class="btn btn-sm btn-warning mt-2" onclick="editPolygon(' + id + ', this)"><i class="fas fa-edit"></i> Edit</button>' +
                    '<button class="btn btn-sm btn-danger mt-2" onclick="deletePolygonById(' + id + ', this)"><i class="fas fa-trash"></i> Delete</button>' +
                    '</div>';
                layer.setPopupContent(popupContent);
            }
        }

        // Fungsi untuk menampilkan alert sukses
        function showSuccessAlert(message) {
            $('#successMessage').text(message);
            $('#successAlert').show();
            setTimeout(function() {
                $('#successAlert').hide();
            }, 5000); // Perpanjang waktu tampilan menjadi 5 detik agar pengguna sempat membaca instruksi
        }

        // Fungsi untuk menampilkan alert error
        function showErrorAlert(message) {
            $('#errorMessage').text(message);
            $('#errorAlert').show();
            setTimeout(function() {
                $('#errorAlert').hide();
            }, 3000);
        }

        // Fungsi untuk mengekstrak 5 digit terakhir dari nama jika sesuai pola
        function extractLastFiveDigits(name) {
            // Cek apakah nama memiliki format seperti 210200300101005380 (hanya angka)
            if (name && /^\d+$/.test(name) && name.length >= 5) {
                console.log("Extracting last five digits from:", name);
                // Ambil 5 digit terakhir
                var hasil = name.slice(-5);
                console.log("hasil:", hasil);
                return hasil;
            }
            
            // Handle format seperti "210200500800305550 / ACHAMAD RAHMADI" atau "210200500800305550"
            if (name) {
                // Ekstrak bagian angka di awal string (asumsi format: angka diikuti oleh spasi dan karakter lain)
                var match = name.match(/^(\d+)/);
                if (match && match[1].length >= 5) {
                    var numberPart = match[1];
                    var hasil = numberPart.slice(-5);
                    console.log("Extracted from complex format:", name, "=>", hasil);
                    return hasil;
                }
            }
            
            return null;
        }
        
        // Fungsi untuk memuat checkbox kategori berdasarkan ID file KML
        function loadCategoryCheckboxes(headId) {
            // Pastikan headId adalah angka (menunjukkan ID file KML)
            if (!isNaN(headId)) {
                $.getJSON('<?= base_url('index.php/map/kml_polygon_categories/') ?>' + headId, function(categories) {
                    if (categories && categories.length > 0) {
                        var container = $('#categoryCheckboxes');
                        container.empty();
                        
                        // Tambahkan checkbox untuk setiap kategori
                        categories.forEach(function(category) {
                            var checkboxId = 'category_' + category.replace(/\s+/g, '_');
                            var checkboxHtml = 
                                '<div class="form-check">' +
                                '<input class="form-check-input" type="checkbox" name="categoryFilter" value="' + category + '" id="' + checkboxId + '">' +
                                '<label class="form-check-label" for="' + checkboxId + '">' + category + '</label>' +
                                '</div>';
                            container.append(checkboxHtml);
                        });
                        
                        // Tampilkan container
                        $('#categoryFilterContainer').show();
                    } else {
                        // Sembunyikan container jika tidak ada kategori
                        $('#categoryFilterContainer').hide();
                    }
                }).fail(function() {
                    // Sembunyikan container jika gagal memuat
                    $('#categoryFilterContainer').hide();
                });
            } else {
                // Sembunyikan container jika headId bukan angka (bukan file KML)
                $('#categoryFilterContainer').hide();
            }
        }
        
        // Initial load
        loadData();

        // Export button
        $('#btnExportGeo').on('click', function() {
            window.location = '<?= base_url('index.php/export/geojson') ?>';
        });
        
        // Event listener untuk checkbox kategori
        $(document).on('change', 'input[name="categoryFilter"]', function() {
            loadData();
        });
        
        // Event listener untuk checkbox kategori di modal pencarian
        $(document).on('change', 'input[name="searchCategoryFilter"]', function() {
            // Tidak perlu melakukan apa-apa saat checkbox di modal berubah
            // Kategori akan digunakan saat pencarian dilakukan
        });
        
        // Event listener untuk checkbox "Semua Kategori"
        $(document).on('change', '#selectAllCategories', function() {
            if (this.checked) {
                // Jika "Semua Kategori" dicentang, nonaktifkan checkbox kategori lainnya
                $('input[name="searchCategoryFilter"]').prop('disabled', true);
            } else {
                // Jika "Semua Kategori" tidak dicentang, aktifkan checkbox kategori lainnya
                $('input[name="searchCategoryFilter"]').prop('disabled', false);
            }
        });
        
        // Event listener untuk checkbox kategori individual
        $(document).on('change', 'input[name="searchCategoryFilter"]', function() {
            // Periksa apakah ada checkbox kategori yang dicentang
            var anyCategoryChecked = $('input[name="searchCategoryFilter"]:checked').length > 0;
            
            if (anyCategoryChecked) {
                // Jika ada kategori yang dicentang, noncentang "Semua Kategori"
                $('#selectAllCategories').prop('checked', false);
            } else {
                // Jika tidak ada kategori yang dicentang, centang "Semua Kategori"
                $('#selectAllCategories').prop('checked', true);
            }
        });
        
        // Event listener untuk menutup hasil pencarian
        $('#btnCloseSearchResults').on('click', function() {
            $('#searchResultsContainer').hide();
        });
        
        // Fungsi pencarian objek
        function performSearch(searchTerm) {
            if (!searchTerm) {
                $('#searchResultsContainer').hide();
                return;
            }
            
            // Periksa apakah "Semua Kategori" dicentang
            var selectAllCategories = $('#selectAllCategories').is(':checked');
            
            // Dapatkan kategori yang dipilih di modal pencarian (jika tidak memilih semua)
            var selectedCategories = [];
            if (!selectAllCategories) {
                $('input[name="searchCategoryFilter"]:checked').each(function() {
                    selectedCategories.push($(this).val());
                });
            }
            
            // Siapkan data untuk dikirim
            var requestData = {
                term: searchTerm
            };
            
            // Tambahkan kategori jika ada yang dipilih
            if (!selectAllCategories && selectedCategories.length > 0) {
                requestData.categories = selectedCategories;
            }
            
            // Lakukan pencarian ke server
            $.ajax({
                url: '<?= base_url("index.php/map/search_polygon") ?>',
                type: 'GET',
                data: requestData,
                success: function(response) {
                    if (response.results && response.results.length > 0) {
                        displaySearchResults(response.results);
                        // Tampilkan hasil pencarian
                        $('#searchResultsContainer').show();
                    } else {
                        $('#searchResultsContainer').hide();
                        showInfoAlert('Tidak ditemukan hasil untuk "' + searchTerm + '"');
                    }
                },
                error: function() {
                    showErrorAlert('Gagal melakukan pencarian');
                }
            });
        }
        
        // Fungsi untuk menampilkan hasil pencarian dalam tabel
        function displaySearchResults(results) {
            var tbody = $('#searchResultsTable tbody');
            tbody.empty();
            
            results.forEach(function(result) {
                var row = $('<tr>');
                row.append($('<td>').text(result.text || '-'));
                row.append($('<td>').text(result.kelurahan || '-'));
                row.append($('<td>').text(result.properties || '-'));
                var actionCell = $('<td>');
                var viewBtn = $('<button class="btn btn-sm btn-primary">')
                    .html('<i class="fas fa-eye"></i> Lihat')
                    .on('click', function() {
                        highlightPolygon(result.id, result.head_id);
                        $('#searchResultsContainer').hide();
                    });
                actionCell.append(viewBtn);
                row.append(actionCell);
                tbody.append(row);
            });
            
            // Tampilkan container hasil pencarian melayang di atas peta
            $('#searchResultsContainer').show();
        }
        
        // Event listener untuk memuat kategori saat modal pencarian dibuka
        $('#searchModal').on('show.bs.modal', function () {
            // Dapatkan nilai filter kelurahan yang saat ini dipilih
            var selectedKelurahan = $('#filterKel').val();
            
            // Jika ada nilai yang dipilih dan merupakan angka (ID KML)
            if (selectedKelurahan && !isNaN(parseInt(selectedKelurahan)) && selectedKelurahan.trim() !== '') {
                // Muat kategori untuk file KML yang dipilih
                loadSearchCategoryCheckboxes(selectedKelurahan);
            } else {
                // Sembunyikan container kategori jika tidak ada file KML yang dipilih
                $('#searchCategoryFilterContainer').hide();
            }
        });
    });
</script>