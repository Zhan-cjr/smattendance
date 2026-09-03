@extends('layouts.app')
@section('titlepage', 'Monitoring Master')

@section('content')
<!-- Password authentication modal -->
<div class="modal fade" id="monitoringAuthModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Autentikasi Monitoring Master</h5>
            </div>
            <div class="modal-body">
                <p>Masukkan password untuk mengakses menu Monitoring Master.</p>
                <div class="mb-3">
                    <input type="password" id="monitoringPasswordInput" class="form-control" placeholder="Password">
                </div>
                <div id="monitoringPasswordError" class="text-danger small d-none">Password salah. Coba lagi.</div>
            </div>
            <div class="modal-footer">
                <button type="button" id="monitoringPasswordBack" class="btn btn-secondary">Kembali</button>
                <button type="button" id="monitoringPasswordSubmit" class="btn btn-primary">Masuk</button>
            </div>
        </div>
    </div>
</div>

<div id="monitoringMasterContent" style="display: none;">
@section('navigasi')
    <div class="d-flex justify-content-between align-items-center w-100">
        <div>
            Monitoring Master
            <div class="text-muted mt-1" style="font-size: 0.75rem;">
                Kelola karyawan yang akan dipantau lokasi real-time nya.
            </div>
        </div>
        <nav aria-label="breadcrumb" class="d-none d-md-block" style="font-size: 0.75rem;">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard.index') }}"><i class="ti ti-home-2 ti-xs"></i></a>
                </li>
                <li class="breadcrumb-item">
                    <a href="javascript:void(0);"><i class="ti ti-database ti-xs me-1"></i> Data Master</a>
                </li>
                <li class="breadcrumb-item active">
                    <i class="ti ti-map-pin ti-xs me-1"></i> Monitoring Master
                </li>
            </ol>
        </nav>
    </div>
@endsection

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="ti ti-eye me-2 text-success"></i>
                    Karyawan Dipantau ({{ $spyEmployees->count() }})
                </h5>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#searchModal">
                    <i class="ti ti-plus me-1"></i> Tambah Karyawan
                </button>
            </div>
            <div class="card-body">
                @if($spyEmployees->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="15%">NIK</th>
                                    <th width="30%">Nama</th>
                                    <th width="20%">Jabatan</th>
                                    <th width="20%">Dept</th>
                                    <th width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($spyEmployees as $i => $emp)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td><span class="badge bg-primary">{{ $emp->nik }}</span></td>
                                    <td>{{ $emp->nama_karyawan }}</td>
                                    <td><small>{{ $emp->jabatan->nama_jabatan ?? '-' }}</small></td>
                                    <td><small>{{ $emp->departemen->nama_dept ?? '-' }}</small></td>
                                    <td>
                                        <button class="btn btn-sm btn-danger" type="button" onclick="hapus('{{ $emp->nik }}', '{{ addslashes($emp->nama_karyawan) }}')">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5 text-muted">
                        <i class="ti ti-inbox" style="font-size: 48px; opacity: 0.3;"></i>
                        <p class="mt-3">Belum ada karyawan yang dipantau</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Search -->
<div class="modal fade" id="searchModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Karyawan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <input type="text" class="form-control" id="searchInput" placeholder="Cari nama atau NIK...">
                </div>
                <div id="searchResults"></div>
            </div>
        </div>
    </div>
</div>

</div>
@endsection

@section('scripts')
<script>
const CSRF = '{{ csrf_token() }}';

// Helper function to escape single quotes for onclick
function escapeQuotes(str) {
    return String(str).replace(/'/g, "\\'");
}

// ============ FUNCTIONS DEFINED IMMEDIATELY ============

function hapus(nik, nama) {
    Swal.fire({
        icon: 'warning',
        title: 'Konfirmasi Hapus',
        text: 'Hapus ' + nama + ' dari monitoring?',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (!result.isConfirmed) return;
        
        fetch('{{ route("monitoring-master.remove") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF
            },
            body: JSON.stringify({ nik: nik })
        })
        .then(r => r.json())
        .then(data => {
            if (data.ok) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Karyawan berhasil dihapus dari monitoring',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => location.reload());
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: data.error || 'Terjadi kesalahan'
                });
            }
        })
        .catch(e => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: e.message
            });
        });
    });
}

function tambah(nik, nama) {
    Swal.fire({
        icon: 'warning',
        title: 'Konfirmasi Tambah',
        text: 'Tambahkan ' + nama + ' ke monitoring?',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Tambahkan',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (!result.isConfirmed) return;
        
        fetch('{{ route("monitoring-master.add") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF
            },
            body: JSON.stringify({ nik: nik })
        })
        .then(r => r.json())
        .then(data => {
            if (data.ok) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Karyawan berhasil ditambahkan ke monitoring',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => location.reload());
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: data.error || 'Terjadi kesalahan'
                });
            }
        })
        .catch(e => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: e.message
            });
        });
    });
}

// ============ INITIALIZE WHEN DOM READY ============

document.addEventListener('DOMContentLoaded', function() {
    const pagePassword = 'eagleeye';
    const authModalEl = document.getElementById('monitoringAuthModal');
    const authPasswordInput = document.getElementById('monitoringPasswordInput');
    const authError = document.getElementById('monitoringPasswordError');
    const authSubmit = document.getElementById('monitoringPasswordSubmit');
    const authBack = document.getElementById('monitoringPasswordBack');
    const contentWrapper = document.getElementById('monitoringMasterContent');

    const authModal = new bootstrap.Modal(authModalEl, {
        backdrop: 'static',
        keyboard: false
    });

    authModal.show();
    authPasswordInput.focus();

    function unlockMonitoringPage() {
        authModal.hide();
        contentWrapper.style.display = 'block';
    }

    authSubmit.addEventListener('click', function() {
        const entered = authPasswordInput.value.trim();
        if (entered === pagePassword) {
            unlockMonitoringPage();
            return;
        }
        authError.classList.remove('d-none');
        authPasswordInput.classList.add('is-invalid');
        authPasswordInput.focus();
    });

    authPasswordInput.addEventListener('keypress', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            authSubmit.click();
        }
    });

    authBack.addEventListener('click', function() {
        window.location.href = '{{ route("dashboard.index") }}';
    });

    // Reset search when modal opens
    const searchModal = document.getElementById('searchModal');
    if (searchModal) {
        searchModal.addEventListener('show.bs.modal', function() {
            document.getElementById('searchInput').value = '';
            document.getElementById('searchResults').innerHTML = '';
        });
    }

    // Search input handler
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const q = e.target.value.trim();
            
            if (q.length < 2) {
                document.getElementById('searchResults').innerHTML = '';
                return;
            }
            
            // Show loading
            document.getElementById('searchResults').innerHTML = '<p class="text-muted">Mencari...</p>';
            
            // Search API
            fetch('{{ route("monitoring-master.search") }}?q=' + encodeURIComponent(q))
                .then(r => r.json())
                .then(data => {
                    if (!data.results || data.results.length === 0) {
                        document.getElementById('searchResults').innerHTML = '<p class="text-muted">Tidak ada hasil</p>';
                        return;
                    }
                    
                    let html = '<div class="list-group">';
                    data.results.forEach(emp => {
                        const nikEscaped = escapeQuotes(emp.nik);
                        const namaEscaped = escapeQuotes(emp.nama);
                        html += `
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold">${emp.nama}</div>
                                    <small class="text-muted">NIK: ${emp.nik} | ${emp.jabatan} | ${emp.dept}</small>
                                </div>
                                <button class="btn btn-sm btn-success" type="button" onclick="tambah('${nikEscaped}', '${namaEscaped}')">
                                    <i class="ti ti-plus"></i> Tambah
                                </button>
                            </div>
                        `;
                    });
                    html += '</div>';
                    document.getElementById('searchResults').innerHTML = html;
                })
                .catch(e => {
                    document.getElementById('searchResults').innerHTML = '<p class="text-danger"><i class="ti ti-alert-circle"></i> Gagal memuat data</p>';
                    console.error('Search error:', e);
                });
        });
    }
});
</script>
@endsection
