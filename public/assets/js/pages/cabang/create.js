(function () {
    const formcreateCabang = document.querySelector('#formcreateCabang');
    // Form validation for Add new record
    if (formcreateCabang) {
        const fv = FormValidation.formValidation(formcreateCabang, {
            fields: {
                kode_cabang: {
                    validators: {
                        notEmpty: {
                            message: 'Kode Cabang Harus Diisi'
                        },
                        stringLength: {
                            min: 1,
                            max: 3,
                            message: 'Kode Cabang maksimal 3 karakter'
                        },
                        regexp: {
                            regexp: /^[A-Z0-9]+$/,
                            message: 'Kode Cabang hanya boleh huruf kapital dan angka'
                        }
                    }
                },
                nama_cabang: {
                    validators: {
                        notEmpty: {
                            message: 'Nama Cabang Harus Diisi'
                        },
                        stringLength: {
                            min: 1,
                            max: 50,
                            message: 'Nama Cabang maksimal 50 karakter'
                        }
                    }
                },
                alamat_cabang: {
                    validators: {
                        notEmpty: {
                            message: 'Alamat Cabang Harus Diisi'
                        },
                        stringLength: {
                            min: 1,
                            max: 100,
                            message: 'Alamat Cabang maksimal 100 karakter'
                        }
                    }
                },
                telepon_cabang: {
                    validators: {
                        notEmpty: {
                            message: 'Telepon Cabang Harus Diisi'
                        },
                        stringLength: {
                            min: 1,
                            max: 13,
                            message: 'Telepon Cabang maksimal 13 karakter'
                        },
                        regexp: {
                            regexp: /^[0-9]+$/,
                            message: 'Telepon Cabang hanya boleh angka'
                        }
                    }
                },
                lokasi_cabang: {
                    validators: {
                        notEmpty: {
                            message: 'Lokasi Cabang Harus Diisi'
                        }
                    }
                },
                radius_cabang: {
                    validators: {
                        notEmpty: {
                            message: 'Radius Cabang Harus Diisi'
                        },
                        integer: {
                            message: 'Radius Cabang harus berupa angka'
                        },
                        between: {
                            min: 1,
                            max: 9999,
                            message: 'Radius Cabang harus antara 1 sampai 9999 meter'
                        }
                    }
                },

                kode_regional: {
                    validators: {
                        notEmpty: {
                            message: 'Regional Harus Dipilih'
                        }
                    }
                },

                kode_pt: {
                    validators: {
                        notEmpty: {
                            message: 'Kode PT Harus Diisi'
                        },
                        stringLength: {
                            max: 3,
                            min: 3,
                            message: 'Kode PT Harus 3 Karakter'
                        },


                    }
                },

                nama_pt: {
                    validators: {
                        notEmpty: {
                            message: 'Nama PT Harus Diisi'
                        }
                    }
                },

                urutan: {
                    validators: {
                        notEmpty: {
                            message: 'Urutan Harus Diisi'
                        }
                    }
                },


            },
            plugins: {
                trigger: new FormValidation.plugins.Trigger(),
                bootstrap5: new FormValidation.plugins.Bootstrap5({
                    eleValidClass: '',
                    rowSelector: '.mb-3'
                }),
                submitButton: new FormValidation.plugins.SubmitButton(),

                defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
                autoFocus: new FormValidation.plugins.AutoFocus()
            },
            init: instance => {
                instance.on('plugins.message.placed', function (e) {
                    if (e.element.parentElement.classList.contains('input-group')) {
                        e.element.parentElement.insertAdjacentElement('afterend', e.messageElement);
                    }
                });
            }
        });

        // Auto uppercase untuk kode_cabang
        const kodeCabangInput = formcreateCabang.querySelector('[name="kode_cabang"]');
        if (kodeCabangInput) {
            kodeCabangInput.addEventListener('input', function(e) {
                e.target.value = e.target.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
            });
        }

        // Validasi panjang real-time
        const namaCabangInput = formcreateCabang.querySelector('[name="nama_cabang"]');
        if (namaCabangInput) {
            namaCabangInput.addEventListener('input', function(e) {
                const value = e.target.value;
                if (value.length > 50) {
                    e.target.value = value.substring(0, 50);
                }
            });
        }

        const alamatCabangInput = formcreateCabang.querySelector('[name="alamat_cabang"]');
        if (alamatCabangInput) {
            alamatCabangInput.addEventListener('input', function(e) {
                const value = e.target.value;
                if (value.length > 100) {
                    e.target.value = value.substring(0, 100);
                }
            });
        }

        const teleponCabangInput = formcreateCabang.querySelector('[name="telepon_cabang"]');
        if (teleponCabangInput) {
            teleponCabangInput.addEventListener('input', function(e) {
                const value = e.target.value.replace(/[^0-9]/g, '');
                if (value.length > 13) {
                    e.target.value = value.substring(0, 13);
                } else {
                    e.target.value = value;
                }
            });
        }

        // Initialize Leaflet Map
        const mapElement = document.getElementById('map');
        if (mapElement) {
            let map, marker, circle;
            const lokasiInput = formcreateCabang.querySelector('[name="lokasi_cabang"]');
            const radiusInput = formcreateCabang.querySelector('[name="radius_cabang"]');
            
            // Cek dan hapus map yang sudah ada sebelumnya untuk mencegah error "Map container is already initialized"
            if (mapElement._leaflet_id) {
                // Hapus semua child elements dari map container
                mapElement.innerHTML = '';
                // Reset leaflet ID
                delete mapElement._leaflet_id;
            }
            
            // Default location (Tasikmalaya)
            let defaultLat = -7.317623;
            let defaultLng = 108.199358;
            let defaultZoom = 13;

            // Parse existing location if available
            if (lokasiInput && lokasiInput.value) {
                const coords = lokasiInput.value.split(',');
                if (coords.length === 2) {
                    defaultLat = parseFloat(coords[0].trim());
                    defaultLng = parseFloat(coords[1].trim());
                }
            }

            // Initialize map
            map = L.map('map').setView([defaultLat, defaultLng], defaultZoom);

            // Add OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 19
            }).addTo(map);

            // Function to update location input and marker
            function updateLocation(lat, lng) {
                const locationString = lat.toFixed(6) + ',' + lng.toFixed(6);
                if (lokasiInput) {
                    lokasiInput.value = locationString;
                }

                // Remove existing marker and circle
                if (marker) {
                    map.removeLayer(marker);
                }
                if (circle) {
                    map.removeLayer(circle);
                }

                // Add new marker
                marker = L.marker([lat, lng], {
                    draggable: true
                }).addTo(map);

                // Update circle if radius is set
                const radius = radiusInput ? parseInt(radiusInput.value) || 30 : 30;
                if (radius > 0) {
                    circle = L.circle([lat, lng], {
                        color: '#3388ff',
                        fillColor: '#3388ff',
                        fillOpacity: 0.2,
                        radius: radius
                    }).addTo(map);
                }

                // Update marker position when dragged
                marker.on('dragend', function(e) {
                    const position = marker.getLatLng();
                    updateLocation(position.lat, position.lng);
                });

                // Show popup with coordinates
                marker.bindPopup(`
                    <b>Lokasi Dipilih</b><br>
                    Latitude: ${lat.toFixed(6)}<br>
                    Longitude: ${lng.toFixed(6)}<br>
                    <small>Drag marker untuk memindahkan lokasi</small>
                `).openPopup();
            }

            // Initialize marker and circle
            updateLocation(defaultLat, defaultLng);

            // Click on map to set location
            map.on('click', function(e) {
                updateLocation(e.latlng.lat, e.latlng.lng);
            });

            // Update circle when radius changes
            if (radiusInput) {
                radiusInput.addEventListener('input', function() {
                    const radius = parseInt(this.value) || 0;
                    if (marker && radius > 0) {
                        const position = marker.getLatLng();
                        if (circle) {
                            map.removeLayer(circle);
                        }
                        circle = L.circle([position.lat, position.lng], {
                            color: '#3388ff',
                            fillColor: '#3388ff',
                            fillOpacity: 0.2,
                            radius: radius
                        }).addTo(map);
                    }
                });
            }

            // Search location function
            const searchInput = document.getElementById('searchLocation');
            const searchButton = document.getElementById('btnSearchLocation');

            function searchLocation() {
                const query = searchInput.value.trim();
                if (!query) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Silakan masukkan nama lokasi yang ingin dicari'
                    });
                    return;
                }

                // Show loading
                if (searchButton) {
                    const originalText = searchButton.innerHTML;
                    searchButton.disabled = true;
                    searchButton.innerHTML = '<i class="ti ti-loader me-1"></i> Mencari...';

                    // Use Nominatim for geocoding
                    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=1`)
                        .then(response => response.json())
                        .then(data => {
                            if (data && data.length > 0) {
                                const result = data[0];
                                const lat = parseFloat(result.lat);
                                const lon = parseFloat(result.lon);
                                
                                // Update map view and location
                                map.setView([lat, lon], 15);
                                updateLocation(lat, lon);
                                
                                // Update search input with found location
                                searchInput.value = result.display_name;
                                
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Lokasi Ditemukan',
                                    text: result.display_name,
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Lokasi Tidak Ditemukan',
                                    text: 'Silakan coba dengan kata kunci lain'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Terjadi kesalahan saat mencari lokasi'
                            });
                        })
                        .finally(() => {
                            searchButton.disabled = false;
                            searchButton.innerHTML = '<i class="ti ti-search me-1"></i> Cari';
                        });
                }
            }

            // Search button click
            if (searchButton) {
                searchButton.addEventListener('click', searchLocation);
            }

            // Search on Enter key
            if (searchInput) {
                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        searchLocation();
                    }
                });
            }
        }
    }
})();
