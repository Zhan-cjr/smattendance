(function () {
    const formGrup = document.querySelector('#formGrup');
    // Form validation for Add new record
    if (formGrup) {
        const fv = FormValidation.formValidation(formGrup, {
            fields: {
                kode_grup: {
                    validators: {
                        notEmpty: {
                            message: 'Kode Grup Harus Diisi !'
                        },
                        stringLength: {
                            min: 1,
                            max: 3,
                            message: 'Kode Grup maksimal 3 karakter'
                        },
                        regexp: {
                            regexp: /^[A-Z0-9]+$/,
                            message: 'Kode Grup hanya boleh huruf kapital dan angka'
                        }
                    }
                },
                nama_grup: {
                    validators: {
                        notEmpty: {
                            message: 'Nama Grup Harus Diisi !'
                        },
                        stringLength: {
                            min: 1,
                            max: 50,
                            message: 'Nama Grup maksimal 50 karakter'
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

        // Auto uppercase untuk kode_grup
        const kodeGrupInput = formGrup.querySelector('[name="kode_grup"]');
        if (kodeGrupInput) {
            kodeGrupInput.addEventListener('input', function(e) {
                e.target.value = e.target.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
            });
        }

        // Validasi panjang real-time
        const namaGrupInput = formGrup.querySelector('[name="nama_grup"]');
        if (namaGrupInput) {
            namaGrupInput.addEventListener('input', function(e) {
                const value = e.target.value;
                if (value.length > 50) {
                    e.target.value = value.substring(0, 50);
                }
            });
        }
    }
})();



































