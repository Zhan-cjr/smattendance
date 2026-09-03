<div class="row">
    <div class="col-12">
        <table class="table">
            <thead class="table-dark">
                <tr>
                    <th colspan="4">Mesin 1</th>
                </tr>
                <tr>
                    <th>PIN</th>
                    <th>Status Scan</th>
                    <th>Scan Date</th>
                    <th>#</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($filtered_array as $d)
                    <tr>
                        <td>{{ $d->pin }}</td>
                        <td>{{ $d->status_scan % 2 == 0 ? 'IN' : 'OUT' }} ({{ $d->status_scan }})</td>
                        <td>{{ date('d-m-Y H:i:s', strtotime($d->scan_date)) }}</td>
                        <td>
                            <div class="d-flex">
                                @if(isset($is_locked) && $is_locked)
                                    <span class="text-danger"><i class="ti ti-lock"></i> Terkunci</span>
                                @else
                                    <form method="POST" name="updatemasuk" class="updatemasuk me-1"
                                        action="{{ route('presensi.updatefrommachine', [Crypt::encrypt($d->pin), 0]) }}">
                                        @csrf
                                        <input type="hidden" name="scan_date" value="{{ date('Y-m-d H:i:s', strtotime($d->scan_date)) }}">
                                        <button href="#" class="btn btn-success btn-sm me-1">
                                            <i class="ti ti-login me-1"></i> Masuk
                                        </button>
                                    </form>
                                    <form method="POST" name="updatepulang" class="updatepulang"
                                        action="{{ route('presensi.updatefrommachine', [Crypt::encrypt($d->pin), 1]) }}">
                                        @csrf
                                        <input type="hidden" name="scan_date" value="{{ date('Y-m-d H:i:s', strtotime($d->scan_date)) }}">
                                        <button href="#" class="btn btn-danger btn-sm me-1">
                                            <i class="ti ti-logout me-1"></i> Pulang
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

</div>

<div class="row mt-3">
    <div class="col-12">
        <table class="table align-middle table-hover">
            <thead class="table-dark">
                <tr>
                    <th colspan="6">
                        <i class="ti ti-server-cog me-2"></i> Log Mesin Presensi (Lokal Server)
                    </th>
                </tr>
                <tr>
                    <th>Identitas Mesin</th>
                    <th>Status Scan</th>
                    <th>Waktu Presensi</th>
                    <th>Status Sinkronisasi</th>
                    <th>Keterangan</th>
                    <th>#</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($log_lokal as $log)
                    <tr>
                        <td>
                            @if($log->nama_mesin)
                                <div class="d-flex align-items-center">
                                    <div class="text-primary bg-primary-subtle rounded p-2 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="ti ti-fingerprint fs-4"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1 fw-bold text-dark">{{ $log->nama_mesin }}</h6>
                                        <div class="text-muted" style="font-size: 0.8rem;">
                                            <span><i class="ti ti-barcode me-1"></i>SN: {{ $log->sn }}</span><br>
                                            <span><i class="ti ti-map-pin me-1"></i>{{ $log->lokasi ?? 'Lokasi tidak diset' }}</span>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <span class="text-muted"><i class="ti ti-help-circle me-1"></i> {{ $log->id_mesin ?? 'Tidak Terdeteksi' }}</span>
                            @endif
                        </td>
                        <td>
                            @if(is_numeric($log->status_scan))
                                @if($log->status_scan % 2 == 0)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="ti ti-login fs-6 me-1"></i> MASUK ({{ $log->status_scan }})</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1"><i class="ti ti-logout fs-6 me-1"></i> PULANG ({{ $log->status_scan }})</span>
                                @endif
                            @else
                                <span class="badge bg-secondary-subtle text-secondary px-2 py-1">{{ $log->status_scan }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="fw-medium text-dark">{{ date('d M Y', strtotime($log->jam_absen)) }}</div>
                            <div class="text-muted" style="font-size: 0.85rem;"><i class="ti ti-clock me-1"></i>{{ date('H:i:s', strtotime($log->jam_absen)) }}</div>
                        </td>
                        <td>
                            @if($log->status == 1)
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="ti ti-check fs-6 me-1"></i> Berhasil</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1"><i class="ti ti-x fs-6 me-1"></i> Ditolak</span>
                            @endif
                        </td>
                        <td>
                            <div style="max-width: 250px; word-wrap: break-word; white-space: normal;" class="text-muted mt-1" style="font-size: 0.85rem;">
                                {{ $log->keterangan }}
                            </div>
                        </td>
                        <td>
                            <div class="d-flex">
                                @if(isset($is_locked) && $is_locked)
                                    <span class="text-danger"><i class="ti ti-lock"></i> Terkunci</span>
                                @else
                                    <form method="POST" class="updatemasuk me-1"
                                        action="{{ route('presensi.updatefrommachine', [Crypt::encrypt($log->pin), 0]) }}">
                                        @csrf
                                        <input type="hidden" name="scan_date" value="{{ date('Y-m-d H:i:s', strtotime($log->jam_absen)) }}">
                                        <button href="#" class="btn btn-success btn-sm me-1">
                                            <i class="ti ti-login me-1"></i> Masuk
                                        </button>
                                    </form>
                                    <form method="POST" class="updatepulang"
                                        action="{{ route('presensi.updatefrommachine', [Crypt::encrypt($log->pin), 1]) }}">
                                        @csrf
                                        <input type="hidden" name="scan_date" value="{{ date('Y-m-d H:i:s', strtotime($log->jam_absen)) }}">
                                        <button href="#" class="btn btn-danger btn-sm me-1">
                                            <i class="ti ti-logout me-1"></i> Pulang
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <div class="d-flex flex-column align-items-center justify-content-center">
                                <i class="ti ti-database-off fs-1 text-light mb-2"></i>
                                <p class="mb-0">Belum ada histori sinkronisasi dari mesin di server lokal.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
