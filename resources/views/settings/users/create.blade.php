<form action="{{ route('users.store') }}" id="formcreateUser" method="POST">
    @csrf
    <x-input-with-icon icon="ti ti-user" label="Nama User" name="name" />
    <x-input-with-icon icon="ti ti-user" label="Username" name="username" />
    <x-input-with-icon icon="ti ti-mail" label="Email" name="email" />
    <x-input-with-icon icon="ti ti-key" label="Password" name="password" type="password" />
    <x-select label="Role" name="role" :data="$roles" key="name" textShow="name" />
    
    <!-- Hak Akses Cabang -->
    <div class="form-group" id="cabang-access-group">
        <label class="form-label">Hak Akses Cabang</label>
        <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;" id="cabang-checkbox-container">
            @if(isset($cabangs) && count($cabangs) > 0)
                @foreach($cabangs as $cabang)
                    <div class="form-check mb-2">
                        <input class="form-check-input cabang-checkbox" 
                               type="checkbox" 
                               name="cabangs[]" 
                               value="{{ $cabang->kode_cabang }}" 
                               id="cabang_create_{{ $cabang->kode_cabang }}">
                        <label class="form-check-label" for="cabang_create_{{ $cabang->kode_cabang }}">
                            {{ $cabang->kode_cabang }} - {{ $cabang->nama_cabang }}
                        </label>
                    </div>
                @endforeach
            @else
                <p class="text-muted mb-0">Tidak ada data cabang</p>
            @endif
        </div>
        <small class="text-muted" id="cabang-help-text">Pilih cabang yang dapat diakses oleh user ini</small>
        <div class="invalid-feedback" id="cabang-error" style="display: none;">Minimal 1 cabang harus dipilih</div>
        @error('cabangs')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>

    <!-- Hak Akses Departemen -->
    <div class="form-group" id="departemen-access-group">
        <label class="form-label">Hak Akses Departemen</label>
        <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;" id="departemen-checkbox-container">
            @if(isset($departemens) && count($departemens) > 0)
                @foreach($departemens as $departemen)
                    <div class="form-check mb-2">
                        <input class="form-check-input departemen-checkbox" 
                               type="checkbox" 
                               name="departemens[]" 
                               value="{{ $departemen->kode_dept }}" 
                               id="dept_create_{{ $departemen->kode_dept }}">
                        <label class="form-check-label" for="dept_create_{{ $departemen->kode_dept }}">
                            {{ $departemen->kode_dept }} - {{ $departemen->nama_dept }}
                        </label>
                    </div>
                @endforeach
            @else
                <p class="text-muted mb-0">Tidak ada data departemen</p>
            @endif
        </div>
        <small class="text-muted" id="departemen-help-text">Pilih departemen yang dapat diakses oleh user ini</small>
        <div class="invalid-feedback" id="departemen-error" style="display: none;">Minimal 1 departemen harus dipilih</div>
        @error('departemens')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>
    
    <div class="form-group">
        <button class="btn btn-primary w-100" type="submit">
            <ion-icon name="send-outline" class="me-1"></ion-icon>
            Submit
        </button>
    </div>
</form>

<script src="{{ asset('/assets/vendor/libs/@form-validation/umd/bundle/popular.min.js') }}"></script>
<script src="{{ asset('/assets/vendor/libs/@form-validation/umd/plugin-bootstrap5/index.min.js') }}"></script>
<script src="{{ asset('/assets/vendor/libs/@form-validation/umd/plugin-auto-focus/index.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/users/create.js') }}"></script>
<script>
    // Auto-check semua cabang dan departemen jika role adalah super admin
    // Auto-check semua cabang dan departemen jika role adalah super admin
    (function() {
        const roleSelect = document.querySelector('#formcreateUser select[name="role"]');
        const cabangCheckboxes = document.querySelectorAll('#formcreateUser .cabang-checkbox');
        const departemenCheckboxes = document.querySelectorAll('#formcreateUser .departemen-checkbox');
        const cabangHelpText = document.getElementById('cabang-help-text');
        const departemenHelpText = document.getElementById('departemen-help-text');

        function toggleAccessBasedOnRole() {
            const selectedRole = roleSelect ? roleSelect.options[roleSelect.selectedIndex].text.toLowerCase() : '';
            const isSuperAdmin = selectedRole === 'super admin';
            const cabangGroup = document.getElementById('cabang-access-group');
            const departemenGroup = document.getElementById('departemen-access-group');

            if (isSuperAdmin) {
                // Hide access groups for Super Admin
                if (cabangGroup) cabangGroup.style.display = 'none';
                if (departemenGroup) departemenGroup.style.display = 'none';
                
                // Hide error messages
                const cabangError = document.getElementById('cabang-error');
                const departemenError = document.getElementById('departemen-error');
                if (cabangError) cabangError.style.display = 'none';
                if (departemenError) departemenError.style.display = 'none';
            } else {
                // Show access groups for other roles
                if (cabangGroup) cabangGroup.style.display = 'block';
                if (departemenGroup) departemenGroup.style.display = 'block';

                // Ensure checkboxes are enabled and unchecked (resets)
                cabangCheckboxes.forEach(checkbox => {
                    checkbox.disabled = false;
                });
                departemenCheckboxes.forEach(checkbox => {
                    checkbox.disabled = false;
                });

                // Update help text
                if (cabangHelpText) {
                    cabangHelpText.textContent = 'Pilih cabang yang dapat diakses oleh user ini (minimal 1)';
                }
                if (departemenHelpText) {
                    departemenHelpText.textContent = 'Pilih departemen yang dapat diakses oleh user ini (minimal 1)';
                }
            }
        }

        // Check when role changes
        if (roleSelect) {
            roleSelect.addEventListener('change', toggleAccessBasedOnRole);
        }

        // Validasi sebelum submit
        const form = document.getElementById('formcreateUser');
        if (form) {
            form.addEventListener('submit', function(e) {
                const selectedRole = roleSelect ? roleSelect.options[roleSelect.selectedIndex].text.toLowerCase() : '';
                const isSuperAdmin = selectedRole === 'super admin';
                
                if (isSuperAdmin) {
                    // Remove disabled attribute temporarily before submit
                    cabangCheckboxes.forEach(checkbox => {
                        checkbox.disabled = false;
                    });
                    departemenCheckboxes.forEach(checkbox => {
                        checkbox.disabled = false;
                    });
                } else {
                    // Validasi untuk role selain super admin
                    const checkedCabangs = Array.from(cabangCheckboxes).filter(cb => cb.checked).length;
                    const checkedDepartemens = Array.from(departemenCheckboxes).filter(cb => cb.checked).length;
                    
                    let hasError = false;
                    
                    // Validasi cabang
                    if (checkedCabangs === 0) {
                        const cabangError = document.getElementById('cabang-error');
                        if (cabangError) {
                            cabangError.style.display = 'block';
                            cabangError.textContent = 'Minimal 1 cabang harus dipilih';
                        }
                        hasError = true;
                    } else {
                        const cabangError = document.getElementById('cabang-error');
                        if (cabangError) {
                            cabangError.style.display = 'none';
                        }
                    }
                    
                    // Validasi departemen
                    if (checkedDepartemens === 0) {
                        const departemenError = document.getElementById('departemen-error');
                        if (departemenError) {
                            departemenError.style.display = 'block';
                            departemenError.textContent = 'Minimal 1 departemen harus dipilih';
                        }
                        hasError = true;
                    } else {
                        const departemenError = document.getElementById('departemen-error');
                        if (departemenError) {
                            departemenError.style.display = 'none';
                        }
                    }
                    
                    if (hasError) {
                        e.preventDefault();
                        // Scroll to first error
                        const firstError = document.getElementById('cabang-error').style.display === 'block' 
                            ? document.getElementById('cabang-access-group') 
                            : document.getElementById('departemen-access-group');
                        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        return false;
                    }
                }
            });
            
            // Real-time validation saat checkbox berubah
            cabangCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const selectedRole = roleSelect ? roleSelect.options[roleSelect.selectedIndex].text.toLowerCase() : '';
                    if (selectedRole !== 'super admin') {
                        const checkedCabangs = Array.from(cabangCheckboxes).filter(cb => cb.checked).length;
                        const cabangError = document.getElementById('cabang-error');
                        if (checkedCabangs === 0) {
                            if (cabangError) {
                                cabangError.style.display = 'block';
                            }
                        } else {
                            if (cabangError) {
                                cabangError.style.display = 'none';
                            }
                        }
                    }
                });
            });
            
            departemenCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const selectedRole = roleSelect ? roleSelect.options[roleSelect.selectedIndex].text.toLowerCase() : '';
                    if (selectedRole !== 'super admin') {
                        const checkedDepartemens = Array.from(departemenCheckboxes).filter(cb => cb.checked).length;
                        const departemenError = document.getElementById('departemen-error');
                        if (checkedDepartemens === 0) {
                            if (departemenError) {
                                departemenError.style.display = 'block';
                            }
                        } else {
                            if (departemenError) {
                                departemenError.style.display = 'none';
                            }
                        }
                    }
                });
            });
        }
    })();
</script>
