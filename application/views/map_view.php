<div class="map-container" id="map"></div>

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

<!-- Tombol Simpan Perubahan (awalnya disembunyikan) -->
<div id="saveButtonContainer" style="position: absolute; top: 10px; right: 10px; z-index: 1000; display: none;">
    <button id="saveChangesBtn" class="btn btn-success">
        <i class="fas fa-save"></i> Simpan Perubahan
    </button>
</div>

<style>
    #map {
        height: 100%;
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

        // Add default layer to map
        osmLayer.addTo(map);

        // Layer control
        var baseLayers = {
            "OpenStreetMap": osmLayer,
            "Satelit": satelliteLayer
        };

        var overlays = {
            // Will be populated with data layers
        };

        L.control.layers(baseLayers, overlays).addTo(map);

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
        $('#filterKel').on('change', loadData);

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
                for (var key in properties) {
                    if (properties.hasOwnProperty(key)) {
                        // Khusus untuk field properties, parse dan tampilkan dengan format yang lebih baik
                        // console.log('Processing property:', key);
                        if (key === 'properties' && typeof properties[key] === 'string') {
                            try {
                                // Coba parse sebagai JSON dulu
                                var parsedProps = JSON.parse(properties[key]);
                                propertiesHtml += '<div class="property-item">' +
                                    '<div class="property-label">Properties</div>' +
                                    '<div class="property-value">';

                                for (var propKey in parsedProps) {
                                    propertiesHtml += '<div><strong>' + propKey + ':</strong> ' + (parsedProps[propKey] || '-') + '</div>';
                                }

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
                url = '<?= base_url('index.php/map/kml_data/') ?>' + k;
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
                                    l.feature = {};
                                    l.feature.properties = {
                                        id: r.id,
                                        name: r.name,
                                        // kelurahan: r.kelurahan,
                                        properties: r.properties
                                    };

                                    // Simpan referensi layer
                                    layerMap[r.id] = l;

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
                }
            }).fail(function(jqxhr, textStatus, error) {
                console.error("Failed to load data:", textStatus, error);
                showErrorAlert("Gagal memuat data peta: " + textStatus);
            });
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
                // Ambil 5 digit terakhir
                return name.slice(-5);
            }
            return null;
        }

        // Initial load
        loadData();

        // Export button
        $('#btnExportGeo').on('click', function() {
            window.location = '<?= base_url('index.php/export/geojson') ?>';
        });
    });
</script>