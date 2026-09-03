@extends('layouts.mobile.app')
@section('content')
    <style>
        #header-section { position: fixed; top: 0; left: 0; right: 0; z-index: 1000; }
        #content-section { margin-top: 60px; padding: 12px 15px 90px; }
        .info-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 1px 6px rgba(0,0,0,0.06);
            overflow: hidden;
            margin-bottom: 12px;
        }
        .info-header {
            background: #32745e;
            color: white;
            padding: 12px 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .info-header .avatar {
            width: 36px; height: 36px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 16px; font-weight: 700;
        }
        .info-header .info { flex: 1; }
        .info-header .info .nama { font-size: 14px; font-weight: 700; }
        .info-header .info .meta { font-size: 11px; opacity: 0.8; }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 14px;
            border-bottom: 1px solid #f3f3f3;
            font-size: 12.5px;
        }
        .detail-row:last-child { border-bottom: none; }
        .detail-row .lbl { color: #888; }
        .detail-row .val { color: #333; font-weight: 600; text-align: right; max-width: 60%; word-break: break-word; }
        .dur-badge {
            display: inline-block;
            background: rgba(50,116,94,0.1);
            color: #32745e;
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            margin-top: 2px;
        }
        .catatan-box { padding: 10px 14px; }
        .catatan-box label { font-size: 11.5px; font-weight: 600; color: #666; margin-bottom: 4px; display: block; }
        .catatan-box textarea {
            width: 100%; border: 1px solid #e0e0e0; border-radius: 8px;
            padding: 8px 10px; font-size: 12.5px; resize: none; min-height: 50px;
        }
        .catatan-box textarea:focus { outline: none; border-color: #32745e; }
        .action-row { display: flex; gap: 8px; }
        .btn-act {
            flex: 1; border: none; border-radius: 10px; padding: 12px;
            font-size: 13px; font-weight: 700;
            display: flex; align-items: center; justify-content: center; gap: 5px;
            cursor: pointer;
        }
        .btn-act.ok { background: #32745e; color: #fff; }
        .btn-act.no { background: #f5f5f5; color: #dc3545; border: 1px solid #eee; }
        .btn-act:active { transform: scale(0.97); }
    </style>

    <div id="header-section">
        <div class="appHeader bg-primary text-light">
            <div class="left">
                <a href="{{ route('karyawan-approval.index') }}" class="headerButton goBack">
                    <ion-icon name="chevron-back-outline"></ion-icon>
                </a>
            </div>
            <div class="pageTitle">Proses Izin Dinas</div>
            <div class="right"></div>
        </div>
    </div>

    <div id="content-section">
        <form action="{{ route('karyawan-approval.izindinas.storeapprove', Crypt::encrypt($izindinas->kode_izin_dinas)) }}" method="POST" id="frmApprove">
            @csrf
            <div class="info-card">
                <div class="info-header">
                    <div class="avatar">{{ strtoupper(substr($izindinas->nama_karyawan, 0, 1)) }}</div>
                    <div class="info">
                        <div class="nama">{{ $izindinas->nama_karyawan }}</div>
                        <div class="meta">{{ $izindinas->nik }} · {{ $izindinas->nama_jabatan }}</div>
                    </div>
                </div>
                <div class="detail-row">
                    <span class="lbl">Departemen</span>
                    <span class="val">{{ $izindinas->nama_dept }}</span>
                </div>
                <div class="detail-row">
                    <span class="lbl">Cabang</span>
                    <span class="val">{{ $izindinas->nama_cabang }}</span>
                </div>
                <div class="detail-row">
                    <span class="lbl">Periode</span>
                    <span class="val">
                        {{ date('d/m/Y', strtotime($izindinas->dari)) }} - {{ date('d/m/Y', strtotime($izindinas->sampai)) }}
                        <br><span class="dur-badge">@php $lama = hitungHari($izindinas->dari, $izindinas->sampai); @endphp {{ $lama }} Hari</span>
                    </span>
                </div>
                <div class="detail-row">
                    <span class="lbl">Keterangan</span>
                    <span class="val">{{ $izindinas->keterangan }}</span>
                </div>
                <div class="catatan-box">
                    <label>Catatan</label>
                    <textarea name="catatan" placeholder="Opsional..."></textarea>
                </div>
            </div>
            <div class="action-row">
                <button class="btn-act ok" name="approve" type="submit" value="approve">
                    <ion-icon name="checkmark-outline"></ion-icon> Approve
                </button>
                <button class="btn-act no" name="tolak" type="submit" value="tolak">
                    <ion-icon name="close-outline"></ion-icon> Tolak
                </button>
            </div>
        </form>
    </div>

    <script>
        $(document).on('click', '.btn-act', function() {
            $(this).prop('disabled', true);
            $(this).closest('form').find('.btn-act').not(this).prop('disabled', true);
            $(this).html("<ion-icon name='hourglass-outline'></ion-icon> Processing...");
        });
    </script>
@endsection
