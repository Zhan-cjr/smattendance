<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Reimbursement - {{ date('d/m/Y H:i') }}</title>
    
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tabler Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
    
    <style>
        :root {
            --primary: {{ $generalsetting->theme_color_1 ?? '#0a5c41' }};
            --primary-dark: {{ $generalsetting->theme_color_2 ?? '#064430' }};
            --primary-light: #ecfdf5;
            --slate-50: #f8fafc;
            --slate-100: #f1f5f9;
            --slate-200: #e2e8f0;
            --slate-300: #cbd5e1;
            --slate-400: #94a3b8;
            --slate-500: #64748b;
            --slate-600: #475569;
            --slate-700: #334155;
            --slate-800: #1e293b;
            --slate-900: #0f172a;
        }

        @page {
            size: A4 landscape;
            margin: 8mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            font-size: 11px;
            color: var(--slate-800);
            background-color: #f8fafc;
            margin: 0;
            padding: 16px 20px;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Top Action Bar (Screen Only) */
        .top-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #ffffff;
            padding: 12px 18px;
            border-radius: 14px;
            border: 1px solid var(--slate-200);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            margin-bottom: 16px;
        }

        .top-bar-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .top-bar-title {
            font-size: 14px;
            font-weight: 700;
            color: var(--slate-900);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .badge-total {
            background: var(--primary-light);
            color: var(--primary-dark);
            font-size: 11px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 6px;
            border: 1px solid rgba(10, 92, 65, 0.15);
        }

        .top-bar-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 8px;
            border: 1px solid transparent;
            cursor: pointer;
            transition: all 0.15s ease;
            text-decoration: none;
            font-family: inherit;
        }

        .btn-excel {
            background: #059669;
            color: #ffffff;
        }

        .btn-excel:hover {
            background: #047857;
        }

        .btn-print {
            background: var(--slate-900);
            color: #ffffff;
        }

        .btn-print:hover {
            background: var(--slate-800);
        }

        /* Company Header */
        .report-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 20px;
            background: #ffffff;
            border-radius: 14px;
            border: 1px solid var(--slate-200);
            margin-bottom: 14px;
        }

        .company-info {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .company-logo {
            max-height: 46px;
            max-width: 90px;
            object-fit: contain;
        }

        .company-details h2 {
            font-size: 15px;
            font-weight: 800;
            color: var(--slate-900);
            margin: 0 0 2px 0;
            letter-spacing: -0.2px;
        }

        .company-details p {
            font-size: 11px;
            color: var(--slate-500);
            margin: 0;
        }

        .report-title-badge {
            text-align: right;
        }

        .report-title-badge h3 {
            font-size: 14px;
            font-weight: 800;
            color: var(--primary);
            margin: 0 0 4px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-periode {
            display: inline-block;
            background: var(--slate-100);
            color: var(--slate-700);
            font-size: 11px;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 6px;
            border: 1px solid var(--slate-200);
        }

        /* Meta Parameters Bar */
        .meta-param-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            padding: 10px 16px;
            background: #ffffff;
            border-radius: 10px;
            border: 1px solid var(--slate-200);
            margin-bottom: 14px;
            font-size: 11px;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .meta-label {
            color: var(--slate-500);
            font-weight: 500;
        }

        .meta-value {
            font-weight: 700;
            color: var(--slate-900);
        }

        /* Table Card Container */
        .table-card {
            background: #ffffff;
            border-radius: 14px;
            border: 1px solid var(--slate-200);
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            margin-bottom: 20px;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        /* Modern Data Table */
        table.data-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 11px;
        }

        table.data-table thead th {
            background: linear-gradient(180deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: #ffffff;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: 0.3px;
            padding: 8px 8px;
            text-align: center;
            vertical-align: middle;
            border-right: 1px solid rgba(255, 255, 255, 0.15);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        table.data-table thead th:last-child {
            border-right: none;
        }

        table.data-table tbody td {
            padding: 8px 8px;
            border-bottom: 1px solid var(--slate-200);
            border-right: 1px solid var(--slate-200);
            vertical-align: top;
            font-size: 10.5px;
        }

        table.data-table tbody td:last-child {
            border-right: none;
        }

        table.data-table tbody tr:nth-child(even) td {
            background-color: var(--slate-50);
        }

        table.data-table tbody tr:hover td {
            background-color: var(--primary-light);
        }

        table.data-table tfoot td {
            background: #f1f5f9;
            font-weight: 800;
            color: var(--slate-900);
            padding: 9px 8px;
            border-top: 2px solid var(--slate-300);
            border-bottom: 1px solid var(--slate-300);
            border-right: 1px solid var(--slate-200);
        }

        table.data-table tfoot td:last-child {
            border-right: none;
        }

        /* Badges */
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 2px 7px;
            font-size: 9.5px;
            font-weight: 700;
            border-radius: 5px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .badge-pending { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
        .badge-approved { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .badge-rejected { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .badge-paid { background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }
        .badge-cancel { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }

        /* Detail Items List */
        .item-list {
            margin: 0;
            padding-left: 14px;
            list-style-type: disc;
        }

        .item-list li {
            margin-bottom: 3px;
            line-height: 1.35;
        }

        .item-list li:last-child {
            margin-bottom: 0;
        }

        .item-cat {
            font-weight: 700;
            color: var(--slate-900);
        }

        .item-approved-note {
            color: #166534;
            font-weight: 600;
        }

        /* Summary 2-Col Layout */
        .summary-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-top: 8px;
        }

        @media (max-width: 768px) {
            .summary-container {
                grid-template-columns: 1fr;
            }
        }

        .summary-card {
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid var(--slate-200);
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        .summary-card-header {
            background: var(--slate-100);
            padding: 10px 14px;
            font-weight: 800;
            font-size: 11px;
            color: var(--slate-800);
            border-bottom: 1px solid var(--slate-200);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
            }

            .top-bar {
                display: none !important;
            }

            .report-header, .meta-param-bar, .summary-card {
                border: 1px solid var(--slate-300) !important;
                border-radius: 0 !important;
                margin-bottom: 8px !important;
                padding: 8px 12px !important;
            }

            .table-card {
                border: 1px solid var(--slate-300) !important;
                border-radius: 0 !important;
                box-shadow: none !important;
            }

            .table-responsive {
                overflow: visible !important;
            }
        }
    </style>
</head>

<body>
    <!-- Top Action Bar (Screen Only) -->
    <div class="top-bar">
        <div class="top-bar-info">
            <div class="top-bar-title">
                <i class="ti ti-receipt-tax text-primary"></i>
                Laporan Klaim Reimbursement
            </div>
            <span class="badge-total">{{ count($reimbursement) }} Pengajuan</span>
        </div>
        <div class="top-bar-actions">
            <form action="{{ route('laporan.cetakreimbursement') }}" method="POST" style="display: inline;">
                @csrf
                <input type="hidden" name="kode_cabang" value="{{ request('kode_cabang') }}">
                <input type="hidden" name="kode_dept" value="{{ request('kode_dept') }}">
                <input type="hidden" name="nik" value="{{ request('nik') }}">
                <input type="hidden" name="status" value="{{ request('status') }}">
                <input type="hidden" name="dari" value="{{ $dari }}">
                <input type="hidden" name="sampai" value="{{ $sampai }}">
                <input type="hidden" name="exportButton" value="1">
                <button type="submit" class="btn-action btn-excel">
                    <i class="ti ti-file-spreadsheet"></i>
                    Export Excel
                </button>
            </form>
            <button onclick="window.print()" class="btn-action btn-print">
                <i class="ti ti-printer"></i>
                Cetak Laporan
            </button>
        </div>
    </div>

    <!-- Company & Report Header -->
    <div class="report-header">
        <div class="company-info">
            @if (!empty($generalsetting->logo) && Storage::disk('public')->exists('/logo/' . $generalsetting->logo))
                <img src="{{ asset('storage/logo/' . $generalsetting->logo) }}" alt="Logo" class="company-logo">
            @elseif (!empty($generalsetting->logo) && file_exists(public_path('storage/logo/' . $generalsetting->logo)))
                <img src="{{ asset('storage/logo/' . $generalsetting->logo) }}" alt="Logo" class="company-logo">
            @endif
            <div class="company-details">
                <h2>{{ $generalsetting->nama_perusahaan }}</h2>
                <p>{{ $generalsetting->alamat ?? 'Human Resource & Payroll Management' }} | Telp: {{ $generalsetting->telepon ?? '-' }}</p>
            </div>
        </div>
        <div class="report-title-badge">
            <h3>Laporan Reimbursement</h3>
            <span class="badge-periode">
                <i class="ti ti-calendar"></i>
                Periode: {{ date('d/m/Y', strtotime($dari)) }} - {{ date('d/m/Y', strtotime($sampai)) }}
            </span>
        </div>
    </div>

    <!-- Parameter Filter Summary Bar -->
    <div class="meta-param-bar">
        <div class="meta-item">
            <span class="meta-label">Cabang:</span>
            <span class="meta-value">{{ textUpperCase($namacabang) }}</span>
        </div>
        <span class="text-slate-300">|</span>
        <div class="meta-item">
            <span class="meta-label">Departemen:</span>
            <span class="meta-value">{{ textUpperCase($namadept) }}</span>
        </div>
        <span class="text-slate-300">|</span>
        <div class="meta-item">
            <span class="meta-label">Status:</span>
            <span class="meta-value">
                @if(request('status') == 'P') Pending (Menunggu)
                @elseif(request('status') == 'A') Approved (Disetujui)
                @elseif(request('status') == 'R') Rejected (Ditolak)
                @elseif(request('status') == 'D') Dibayar (Selesai)
                @elseif(request('status') == 'B') Batal
                @else Semua Status
                @endif
            </span>
        </div>
    </div>

    <!-- Main Table Card Container -->
    <div class="table-card">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 32px;">No</th>
                        <th style="width: 110px;">No Pengajuan</th>
                        <th style="width: 75px;">NIK</th>
                        <th style="width: 140px; text-align: left; padding-left: 10px;">Nama Karyawan</th>
                        <th style="width: 100px;">Departemen</th>
                        <th style="width: 90px;">Cabang</th>
                        <th style="width: 80px;">Tgl Pengajuan</th>
                        <th style="width: 95px; text-align: right; padding-right: 10px;">Total Ajuan</th>
                        <th style="width: 95px; text-align: right; padding-right: 10px;">Total Disetujui</th>
                        <th style="width: 75px;">Status</th>
                        <th style="text-align: left; padding-left: 10px;">Rincian Item &amp; Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reimbursement as $r)
                        <tr>
                            <td style="text-align: center;">{{ $loop->iteration }}</td>
                            <td style="text-align: center; font-weight: 700; color: var(--slate-900);">{{ $r->no_reimbursement }}</td>
                            <td style="text-align: center;">{{ $r->nik_show ?? $r->nik }}</td>
                            <td style="font-weight: 700; color: var(--slate-900); padding-left: 10px;">{{ $r->nama_karyawan }}</td>
                            <td style="text-align: center;">{{ $r->nama_dept }}</td>
                            <td style="text-align: center;">{{ $r->nama_cabang }}</td>
                            <td style="text-align: center;">{{ date('d-m-Y', strtotime($r->tanggal_pengajuan)) }}</td>
                            <td style="text-align: right; font-weight: 600; padding-right: 10px;">Rp {{ number_format($r->total_nominal, 0, ',', '.') }}</td>
                            <td style="text-align: right; font-weight: 700; color: var(--primary-dark); padding-right: 10px;">Rp {{ number_format($r->total_disetujui, 0, ',', '.') }}</td>
                            <td style="text-align: center;">
                                @if ($r->status == 'P')
                                    <span class="status-badge badge-pending">Pending</span>
                                @elseif ($r->status == 'A')
                                    <span class="status-badge badge-approved">Approved</span>
                                @elseif ($r->status == 'R')
                                    <span class="status-badge badge-rejected">Rejected</span>
                                @elseif ($r->status == 'D')
                                    <span class="status-badge badge-paid">Dibayar</span>
                                @elseif ($r->status == 'B')
                                    <span class="status-badge badge-cancel">Batal</span>
                                @endif
                            </td>
                            <td style="padding-left: 10px;">
                                @if (isset($details_by_id[$r->id]))
                                    <ul class="item-list">
                                        @foreach ($details_by_id[$r->id] as $det)
                                            <li>
                                                <span class="item-cat">[{{ $det->nama_jenis }}]</span> 
                                                <span>{{ $det->keterangan }}</span>
                                                — <strong>Rp {{ number_format($det->nominal, 0, ',', '.') }}</strong>
                                                @if($det->nominal_disetujui !== null) 
                                                    <span class="item-approved-note">(Disetujui: Rp {{ number_format($det->nominal_disetujui, 0, ',', '.') }})</span> 
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" style="text-align: center; padding: 20px; color: var(--slate-400);">
                                Tidak ada data pengajuan reimbursement pada periode yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="7" style="text-align: right; font-weight: 800;">TOTAL KESELURUHAN</td>
                        <td style="text-align: right; font-weight: 800; padding-right: 10px;">Rp {{ number_format($reimbursement->sum('total_nominal'), 0, ',', '.') }}</td>
                        <td style="text-align: right; font-weight: 800; color: var(--primary-dark); padding-right: 10px;">Rp {{ number_format($reimbursement->sum('total_disetujui'), 0, ',', '.') }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Summary Breakdown Matrix (Rekap Per Kategori) -->
    <div class="summary-container">
        <div class="summary-card">
            <div class="summary-card-header">
                <i class="ti ti-chart-pie"></i>
                <span>REKAPITULASI PER JENIS / KATEGORI REIMBURSEMENT</span>
            </div>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 36px;">No</th>
                            <th style="text-align: left; padding-left: 10px;">Kategori Reimbursement</th>
                            <th style="width: 130px; text-align: right; padding-right: 10px;">Total Nominal Ajuan</th>
                            <th style="width: 130px; text-align: right; padding-right: 10px;">Total Disetujui</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $rekap_no = 1;
                            $rekap_total_nominal = 0;
                            $rekap_total_disetujui = 0;
                        @endphp
                        @forelse ($rekap as $rk)
                            <tr>
                                <td style="text-align: center;">{{ $rekap_no++ }}</td>
                                <td style="font-weight: 700; color: var(--slate-900); padding-left: 10px;">{{ $rk['nama_jenis'] }}</td>
                                <td style="text-align: right; padding-right: 10px;">Rp {{ number_format($rk['total_nominal'], 0, ',', '.') }}</td>
                                <td style="text-align: right; font-weight: 700; color: var(--primary-dark); padding-right: 10px;">Rp {{ number_format($rk['total_disetujui'], 0, ',', '.') }}</td>
                            </tr>
                            @php
                                $rekap_total_nominal += $rk['total_nominal'];
                                $rekap_total_disetujui += $rk['total_disetujui'];
                            @endphp
                        @empty
                            <tr>
                                <td colspan="4" style="text-align: center; padding: 12px; color: var(--slate-400);">
                                    Tidak ada data rekapitulasi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if(count($rekap) > 0)
                        <tfoot>
                            <tr>
                                <td colspan="2" style="text-align: right; font-weight: 800;">TOTAL REKAPITULASI</td>
                                <td style="text-align: right; font-weight: 800; padding-right: 10px;">Rp {{ number_format($rekap_total_nominal, 0, ',', '.') }}</td>
                                <td style="text-align: right; font-weight: 800; color: var(--primary-dark); padding-right: 10px;">Rp {{ number_format($rekap_total_disetujui, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

</body>

</html>
