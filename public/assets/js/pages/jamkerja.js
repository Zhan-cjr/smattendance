(function () {
    const formcreateJamKerja = document.querySelector('#formcreateJamKerja');
    // Form validation for Add new record
    if (formcreateJamKerja) {
        const fv = FormValidation.formValidation(formcreateJamKerja, {
            fields: {
                kode_jam_kerja: {
                    validators: {
                        notEmpty: {
                            message: 'Kode Jam Kerja Harus Diisi !'
                        },
                        stringLength: {
                            min: 1,
                            max: 4,
                            message: 'Kode Jam Kerja maksimal 4 karakter'
                        },
                        regexp: {
                            regexp: /^[A-Z0-9]+$/,
                            message: 'Kode Jam Kerja hanya boleh huruf kapital dan angka'
                        }
                    }
                },
                nama_jam_kerja: {
                    validators: {
                        notEmpty: {
                            message: 'Nama Jam Kerja Harus Diisi !'
                        },
                        stringLength: {
                            min: 1,
                            max: 50,
                            message: 'Nama Jam Kerja maksimal 50 karakter'
                        }
                    }
                },

                jam_masuk: {
                    validators: {
                        notEmpty: {
                            message: 'Jam Masuk Harus Diisi !'
                        },
                        regexp: {
                            regexp: /^(0[0-9]|1[0-9]|2[0-3]):([0-5][0-9])$/,
                            message: 'Format Jam Masuk harus hh:mm'
                        }
                    }
                },

                jam_pulang: {
                    validators: {
                        notEmpty: {
                            message: 'Jam Pulang Harus Diisi !'
                        },
                        regexp: {
                            regexp: /^(0[0-9]|1[0-9]|2[0-3]):([0-5][0-9])$/,
                            message: 'Format Jam Pulang harus hh:mm'
                        }
                    }
                },

                istirahat: {
                    validators: {
                        notEmpty: {
                            message: 'Istirahat Harus Diisi'
                        },
                    }
                },

                jam_awal_istirahat: {
                    validators: {
                        callback: {
                            message: 'Jam Awal Istirahat Harus Diisi',
                            callback: function (input) {
                                const istirahatValue = document.querySelector('[name="istirahat"]').value;
                                if (istirahatValue === '1') {
                                    return input.value !== '';
                                }
                                return true;
                            }
                        },
                        regexp: {
                            regexp: /^(0[0-9]|1[0-9]|2[0-3]):([0-5][0-9])$/,
                            message: 'Format Jam Awal Istirahat harus hh:mm',
                            enabled: function () {
                                const istirahatValue = document.querySelector('[name="istirahat"]').value;
                                return istirahatValue === '1';
                            }
                        }
                    }
                },
                jam_akhir_istirahat: {
                    validators: {
                        callback: {
                            message: 'Jam Akhir Istirahat Harus Diisi',
                            callback: function (input) {
                                const istirahatValue = document.querySelector('[name="istirahat"]').value;
                                if (istirahatValue === '1') {
                                    return input.value !== '';
                                }
                                return true;
                            }
                        },
                        regexp: {
                            regexp: /^(0[0-9]|1[0-9]|2[0-3]):([0-5][0-9])$/,
                            message: 'Format Jam Akhir Istirahat harus hh:mm',
                            enabled: function () {
                                const istirahatValue = document.querySelector('[name="istirahat"]').value;
                                return istirahatValue === '1';
                            }
                        }
                    }
                },

                total_jam: {
                    validators: {
                        notEmpty: {
                            message: 'Total Jam Harus Diisi'
                        },
                        integer: {
                            message: 'Total Jam harus berupa angka'
                        },
                        between: {
                            min: 1,
                            max: 24,
                            message: 'Total Jam harus antara 1 sampai 24 jam'
                        }
                    }
                },
                keterangan: {
                    validators: {
                        stringLength: {
                            max: 255,
                            message: 'Keterangan maksimal 255 karakter'
                        }
                    }
                },

                lintashari: {
                    validators: {
                        notEmpty: {
                            message: 'Lintas Hari Harus Diisi'
                        },
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

                // Tambahkan event listener untuk select istirahat
                document.querySelector('[name="istirahat"]').addEventListener('change', function () {
                    // Revalidate jam_awal_istirahat dan jam_akhir_istirahat
                    fv.revalidateField('jam_awal_istirahat');
                    fv.revalidateField('jam_akhir_istirahat');
                });
            }
        });

        // Auto uppercase untuk kode_jam_kerja
        const kodeJamKerjaInput = formcreateJamKerja.querySelector('[name="kode_jam_kerja"]');
        if (kodeJamKerjaInput && !kodeJamKerjaInput.readOnly) {
            kodeJamKerjaInput.addEventListener('input', function(e) {
                e.target.value = e.target.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
            });
        }

        // Validasi panjang real-time untuk nama_jam_kerja
        const namaJamKerjaInput = formcreateJamKerja.querySelector('[name="nama_jam_kerja"]');
        if (namaJamKerjaInput) {
            namaJamKerjaInput.addEventListener('input', function(e) {
                const value = e.target.value;
                if (value.length > 50) {
                    e.target.value = value.substring(0, 50);
                }
            });
        }

        // Validasi total_jam real-time
        const totalJamInput = formcreateJamKerja.querySelector('[name="total_jam"]');
        if (totalJamInput) {
            totalJamInput.addEventListener('input', function(e) {
                let value = parseInt(e.target.value) || 0;
                if (value < 1) {
                    e.target.value = 1;
                } else if (value > 24) {
                    e.target.value = 24;
                }
            });
        }

        // Validasi panjang real-time untuk keterangan
        const keteranganInput = formcreateJamKerja.querySelector('[name="keterangan"]');
        if (keteranganInput) {
            keteranganInput.addEventListener('input', function(e) {
                const value = e.target.value;
                if (value.length > 255) {
                    e.target.value = value.substring(0, 255);
                }
            });
        }
    }

    // Form validation for Edit record
    const formeditJamKerja = document.querySelector('#formeditJamKerja');
    if (formeditJamKerja) {
        const fvEdit = FormValidation.formValidation(formeditJamKerja, {
            fields: {
                nama_jam_kerja: {
                    validators: {
                        notEmpty: {
                            message: 'Nama Jam Kerja Harus Diisi !'
                        },
                        stringLength: {
                            min: 1,
                            max: 50,
                            message: 'Nama Jam Kerja maksimal 50 karakter'
                        }
                    }
                },
                jam_masuk: {
                    validators: {
                        notEmpty: {
                            message: 'Jam Masuk Harus Diisi !'
                        },
                        regexp: {
                            regexp: /^(0[0-9]|1[0-9]|2[0-3]):([0-5][0-9])$/,
                            message: 'Format Jam Masuk harus hh:mm'
                        }
                    }
                },
                jam_pulang: {
                    validators: {
                        notEmpty: {
                            message: 'Jam Pulang Harus Diisi !'
                        },
                        regexp: {
                            regexp: /^(0[0-9]|1[0-9]|2[0-3]):([0-5][0-9])$/,
                            message: 'Format Jam Pulang harus hh:mm'
                        }
                    }
                },
                istirahat: {
                    validators: {
                        notEmpty: {
                            message: 'Istirahat Harus Diisi'
                        },
                    }
                },
                jam_awal_istirahat: {
                    validators: {
                        callback: {
                            message: 'Jam Awal Istirahat Harus Diisi',
                            callback: function (input) {
                                const istirahatValue = document.querySelector('#formeditJamKerja [name="istirahat"]').value;
                                if (istirahatValue === '1') {
                                    return input.value !== '';
                                }
                                return true;
                            }
                        },
                        regexp: {
                            regexp: /^(0[0-9]|1[0-9]|2[0-3]):([0-5][0-9])$/,
                            message: 'Format Jam Awal Istirahat harus hh:mm',
                            enabled: function () {
                                const istirahatValue = document.querySelector('#formeditJamKerja [name="istirahat"]').value;
                                return istirahatValue === '1';
                            }
                        }
                    }
                },
                jam_akhir_istirahat: {
                    validators: {
                        callback: {
                            message: 'Jam Akhir Istirahat Harus Diisi',
                            callback: function (input) {
                                const istirahatValue = document.querySelector('#formeditJamKerja [name="istirahat"]').value;
                                if (istirahatValue === '1') {
                                    return input.value !== '';
                                }
                                return true;
                            }
                        },
                        regexp: {
                            regexp: /^(0[0-9]|1[0-9]|2[0-3]):([0-5][0-9])$/,
                            message: 'Format Jam Akhir Istirahat harus hh:mm',
                            enabled: function () {
                                const istirahatValue = document.querySelector('#formeditJamKerja [name="istirahat"]').value;
                                return istirahatValue === '1';
                            }
                        }
                    }
                },
                total_jam: {
                    validators: {
                        notEmpty: {
                            message: 'Total Jam Harus Diisi'
                        },
                        integer: {
                            message: 'Total Jam harus berupa angka'
                        },
                        between: {
                            min: 1,
                            max: 24,
                            message: 'Total Jam harus antara 1 sampai 24 jam'
                        }
                    }
                },
                lintashari: {
                    validators: {
                        notEmpty: {
                            message: 'Lintas Hari Harus Diisi'
                        },
                    }
                },
                keterangan: {
                    validators: {
                        stringLength: {
                            max: 255,
                            message: 'Keterangan maksimal 255 karakter'
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

                // Tambahkan event listener untuk select istirahat
                document.querySelector('#formeditJamKerja [name="istirahat"]').addEventListener('change', function () {
                    // Revalidate jam_awal_istirahat dan jam_akhir_istirahat
                    fvEdit.revalidateField('jam_awal_istirahat');
                    fvEdit.revalidateField('jam_akhir_istirahat');
                });
            }
        });

        // Validasi panjang real-time untuk nama_jam_kerja
        const namaJamKerjaInputEdit = formeditJamKerja.querySelector('[name="nama_jam_kerja"]');
        if (namaJamKerjaInputEdit) {
            namaJamKerjaInputEdit.addEventListener('input', function(e) {
                const value = e.target.value;
                if (value.length > 50) {
                    e.target.value = value.substring(0, 50);
                }
            });
        }

        // Validasi total_jam real-time
        const totalJamInputEdit = formeditJamKerja.querySelector('[name="total_jam"]');
        if (totalJamInputEdit) {
            totalJamInputEdit.addEventListener('input', function(e) {
                let value = parseInt(e.target.value) || 0;
                if (value < 1) {
                    e.target.value = 1;
                } else if (value > 24) {
                    e.target.value = 24;
                }
            });
        }

        // Validasi panjang real-time untuk keterangan
        const keteranganInputEdit = formeditJamKerja.querySelector('[name="keterangan"]');
        if (keteranganInputEdit) {
            keteranganInputEdit.addEventListener('input', function(e) {
                const value = e.target.value;
                if (value.length > 255) {
                    e.target.value = value.substring(0, 255);
                }
            });
        }
    }
})();
