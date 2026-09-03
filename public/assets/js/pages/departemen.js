(function () {
    const formDepartemen = document.querySelector('#formDepartemen');
    // Form validation for Add new record
    if (formDepartemen) {
        const fv = FormValidation.formValidation(formDepartemen, {
            fields: {
                kode_dept: {
                    validators: {
                        notEmpty: {
                            message: 'Kode Departemen Harus Diisi !'
                        },
                        stringLength: {
                            min: 1,
                            max: 3,
                            message: 'Kode Departemen maksimal 3 karakter'
                        },
                        regexp: {
                            regexp: /^[A-Z0-9]+$/,
                            message: 'Kode Departemen hanya boleh huruf kapital dan angka'
                        }
                    }
                },
                nama_dept: {
                    validators: {
                        notEmpty: {
                            message: 'Nama Departemen Harus Diisi !'
                        },
                        stringLength: {
                            min: 1,
                            max: 30,
                            message: 'Nama Departemen maksimal 30 karakter'
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

        // Auto uppercase untuk kode_dept
        const kodeDeptInput = formDepartemen.querySelector('[name="kode_dept"]');
        if (kodeDeptInput) {
            kodeDeptInput.addEventListener('input', function(e) {
                e.target.value = e.target.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
            });
        }

        // Validasi panjang real-time
        const namaDeptInput = formDepartemen.querySelector('[name="nama_dept"]');
        if (namaDeptInput) {
            namaDeptInput.addEventListener('input', function(e) {
                const value = e.target.value;
                if (value.length > 30) {
                    e.target.value = value.substring(0, 30);
                }
            });
        }
    }
})();
