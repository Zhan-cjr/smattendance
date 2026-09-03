@extends('layouts.mobile.modern')
@section('title', 'Ajuan Perubahan Jadwal')

@section('header_left')
    <a href="{{ route('ajuanjadwal.index') }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/10 text-white active:scale-95 transition-all">
        <ion-icon name="chevron-back-outline" class="text-lg"></ion-icon>
    </a>
@endsection

@push('mystyle')
    <style>
        body {
            background: #e6fcf5 !important;
        }

        .form-container {
            padding: 10px 5px;
        }

        .form-label-group {
            position: relative;
            margin-bottom: 12px;
            background: transparent !important;
            border: 1px solid #32745e;
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.2s ease;
        }

        .form-label-group .input-icon {
            position: absolute;
            left: 14px;
            top: 11px;
            font-size: 20px;
            color: #32745e;
            z-index: 10;
            pointer-events: none;
        }

        .form-label-group input,
        .form-label-group select,
        .form-label-group textarea {
            width: 100% !important;
            height: 44px;
            padding: 18px 14px 2px 42px !important;
            font-size: 14px;
            font-weight: 500;
            color: #2a6350;
            background: transparent !important;
            border: none !important;
            outline: none !important;
            box-shadow: none !important;
            display: block !important;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
        }

        .form-label-group textarea {
            height: 80px !important;
            padding-top: 22px !important;
            resize: none;
        }

        .form-label-group label {
            position: absolute;
            top: 11px;
            left: 42px;
            font-size: 14px;
            color: #32745e;
            opacity: 0.8;
            pointer-events: none;
            transition: all 0.2s ease-in-out;
            margin-bottom: 0;
            z-index: 5;
            background: transparent;
        }

        .form-label-group input:focus ~ label,
        .form-label-group input:not(:placeholder-shown) ~ label,
        .form-label-group select:focus ~ label,
        .form-label-group select:valid ~ label,
        .form-label-group textarea:focus ~ label,
        .form-label-group textarea:not(:placeholder-shown) ~ label {
            top: 2px;
            left: 42px;
            font-size: 10px;
            font-weight: 600;
            color: #32745e;
        }

        .btn-submit-modern {
            width: 100%;
            height: 48px;
            background: #32745e;
            color: #ffffff;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 5px;
            transition: all 0.3s;
        }

        .btn-submit-modern:active {
            transform: scale(0.97);
            background: #2a6350;
        }

        .fade-up {
            animation: fadeUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
            transform: translateY(15px);
        }

        @keyframes fadeUp {
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
@endpush

@section('content')
    <div class="fade-up form-container">
        <form action="{{ route('ajuanjadwal.store') }}" method="POST" id="formAjuan" autocomplete="off">
            @csrf
            
            @if(isset($karyawan) && count($karyawan) > 0)
            <div class="form-label-group">
                <ion-icon name="people-outline" class="input-icon"></ion-icon>
                <select name="nik" id="nik" required>
                    <option value="" disabled selected></option>
                    @foreach ($karyawan as $d)
                        <option value="{{ $d->nik }}">{{ $d->nama_karyawan }} ({{ $d->nik }})</option>
                    @endforeach
                </select>
                <label for="nik">Pilih Karyawan</label>
            </div>
            @endif

            <div class="form-label-group">
                <ion-icon name="calendar-outline" class="input-icon"></ion-icon>
                <input type="text" name="tanggal" id="tanggal" placeholder=" " required readonly>
                <label for="tanggal">Tanggal Perubahan</label>
            </div>
            
            <div class="form-label-group">
                <ion-icon name="time-outline" class="input-icon"></ion-icon>
                <select name="kode_jam_kerja_tujuan" id="kode_jam_kerja_tujuan" required>
                    <option value="" disabled selected></option>
                    @foreach ($jamkerja as $d)
                        <option value="{{ $d->kode_jam_kerja }}">{{ $d->nama_jam_kerja }} ({{ $d->jam_masuk }} - {{ $d->jam_pulang }})</option>
                    @endforeach
                </select>
                <label for="kode_jam_kerja_tujuan">Shift Tujuan</label>
            </div>

            <div class="form-label-group">
                <ion-icon name="document-text-outline" class="input-icon"></ion-icon>
                <textarea name="keterangan" id="keterangan" placeholder=" " required></textarea>
                <label for="keterangan">Alasan / Keterangan</label>
            </div>

            <button type="submit" class="btn-submit-modern" id="btnSimpan">
                <ion-icon name="paper-plane-outline"></ion-icon>
                <span>Kirim Pengajuan</span>
            </button>
        </form>
    </div>
@endsection

@push('myscript')
    <script src="https://cdn.jsdelivr.net/npm/air-datepicker@3.5.0/air-datepicker.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Custom locale for Air Datepicker
            const localeIndo = {
                days: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
                daysShort: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                daysMin: ['Mg', 'Sn', 'Sl', 'Rb', 'Km', 'Jm', 'Sb'],
                months: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
                monthsShort: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                today: 'Hari ini',
                clear: 'Hapus',
                dateFormat: 'yyyy-MM-dd',
                timeFormat: 'HH:mm',
                firstDay: 1
            };

            new AirDatepicker('#tanggal', {
                locale: localeIndo,
                autoClose: true,
                isMobile: true,
                buttons: ['today', 'clear'],
                position: 'bottom center'
            });

            const form = document.getElementById('formAjuan');
            form.addEventListener('submit', function(e) {
                let nikSelect = document.getElementById('nik');
                let nik = nikSelect ? nikSelect.value : null;
                let tanggal = document.getElementById('tanggal').value;
                let kode_jam_kerja = document.getElementById('kode_jam_kerja_tujuan').value;
                let keterangan = document.getElementById('keterangan').value;

                // Validate NIK only if the select element exists (Admin mode)
                if (nikSelect && (!nik || nik === "")) {
                    e.preventDefault();
                    Swal.fire({title: "Oops!", text: 'Karyawan Harus Dipilih !', icon: "warning"});
                    return;
                }

                if (!tanggal) {
                    e.preventDefault();
                    Swal.fire({title: "Oops!", text: 'Tanggal Harus Diisi !', icon: "warning"});
                    return;
                } else if (!kode_jam_kerja) {
                    e.preventDefault();
                    Swal.fire({title: "Oops!", text: 'Shift Tujuan Harus Dipilih !', icon: "warning"});
                    return;
                } else if (!keterangan.trim()) {
                    e.preventDefault();
                    Swal.fire({title: "Oops!", text: 'Keterangan Harus Diisi !', icon: "warning"});
                    return;
                }
                
                const btn = document.getElementById('btnSimpan');
                btn.disabled = true;
                btn.innerHTML = `<ion-icon name="sync-outline" class="animate-spin"></ion-icon><span>Memproses...</span>`;
            });
        });
    </script>
@endpush
