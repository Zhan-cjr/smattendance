(function () {
    const formCuti = document.querySelector('#formCuti');
    // Form validation for Add new record
    if (formCuti) {
        const fv = FormValidation.formValidation(formCuti, {
            fields: {
                kode_cuti: {
                    validators: {
                        notEmpty: {
                            message: 'Kode Cuti Harus Diisi !'
                        },
                        stringLength: {
                            min: 1,
                            max: 3,
                            message: 'Kode Cuti maksimal 3 karakter'
                        },
                        regexp: {
                            regexp: /^[A-Z0-9]+$/,
                            message: 'Kode Cuti hanya boleh huruf kapital dan angka'
                        }
                    }
                },
                jenis_cuti: {
                    validators: {
                        notEmpty: {
                            message: 'Jenis Cuti Harus Diisi !'
                        },
                        stringLength: {
                            min: 1,
                            max: 50,
                            message: 'Jenis Cuti maksimal 50 karakter'
                        }
                    }
                },
                jumlah_hari: {
                    validators: {
                        notEmpty: {
                            message: 'Jumlah Hari Harus Diisi !'
                        },
                        integer: {
                            message: 'Jumlah Hari harus berupa angka'
                        },
                        between: {
                            min: 1,
                            max: 365,
                            message: 'Jumlah Hari harus antara 1 sampai 365 hari'
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

        // Auto uppercase untuk kode_cuti
        const kodeCutiInput = formCuti.querySelector('[name="kode_cuti"]');
        if (kodeCutiInput && !kodeCutiInput.readOnly) {
            kodeCutiInput.addEventListener('input', function(e) {
                e.target.value = e.target.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
            });
        }

        // Validasi panjang real-time untuk jenis_cuti
        const jenisCutiInput = formCuti.querySelector('[name="jenis_cuti"]');
        if (jenisCutiInput) {
            jenisCutiInput.addEventListener('input', function(e) {
                const value = e.target.value;
                if (value.length > 50) {
                    e.target.value = value.substring(0, 50);
                }
            });
        }

        // Validasi jumlah_hari real-time
        const jumlahHariInput = formCuti.querySelector('[name="jumlah_hari"]');
        if (jumlahHariInput) {
            jumlahHariInput.addEventListener('input', function(e) {
                let value = parseInt(e.target.value) || 0;
                if (value < 1) {
                    e.target.value = 1;
                } else if (value > 365) {
                    e.target.value = 365;
                }
            });
        }
    }
})();
