@extends('layouts.app')
@section('titlepage', 'Setting KPI Jabatan')

@section('content')
@section('navigasi')
    <span>Setting KPI Jabatan</span>
@endsection
<div class="row">
    <div class="col-lg-8 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Konfigurasi KPI: {{ $jabatan->nama_jabatan }}</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('kpi.jabatan.store', $jabatan->kode_jabatan) }}" method="POST" id="formSetting">
                    @csrf
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="tableIndicators">
                            <thead class="table-dark">
                                <tr>
                                    <th style="width: 40%">Indikator</th>
                                    <th style="width: 20%">Bobot</th>
                                    <th style="width: 30%">Target</th>
                                    <th style="width: 10%">#</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($existing_indicators as $index => $item)
                                    <tr id="row{{ $index }}">
                                        <td>
                                            <select name="kpi_indicator_id[]" class="form-select select2">
                                                <option value="">Pilih Indikator</option>
                                                @foreach ($master_indicators as $mi)
                                                    <option value="{{ $mi->id }}" {{ $item->kpi_indicator_id == $mi->id ? 'selected' : '' }}>
                                                        {{ $mi->nama_indikator }} ({{ $mi->satuan }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" name="bobot[]" class="form-control" placeholder="Bobot" value="{{ $item->bobot }}" required>
                                        </td>
                                        <td>
                                            <input type="text" name="target[]" class="form-control" placeholder="Target" value="{{ $item->target }}" required>
                                        </td>
                                        <td>
                                            <a href="#" class="btn btn-danger btn-sm delete-row"><i class="ti ti-trash"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-2">
                        <button type="button" class="btn btn-success btn-sm" id="addRow"><i class="ti ti-plus me-1"></i> Tambah Indikator</button>
                    </div>
                    <div class="mt-4">
                        <button class="btn btn-primary w-100" type="submit">
                            <i class="ti ti-send me-1"></i> Simpan Konfigurasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('myscript')
<script>
    $(function() {
        var rowCount = {{ count($existing_indicators) }};

        $("#addRow").click(function() {
            rowCount++;
            var html = `
                <tr id="row${rowCount}">
                    <td>
                        <select name="kpi_indicator_id[]" class="form-select select2">
                            <option value="">Pilih Indikator</option>
                            @foreach ($master_indicators as $mi)
                                <option value="{{ $mi->id }}">{{ $mi->nama_indikator }} ({{ $mi->satuan }})</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="number" name="bobot[]" class="form-control" placeholder="Bobot" required>
                    </td>
                    <td>
                        <input type="text" name="target[]" class="form-control" placeholder="Target" required>
                    </td>
                    <td>
                        <a href="#" class="btn btn-danger btn-sm delete-row"><i class="ti ti-trash"></i></a>
                    </td>
                </tr>
            `;
            $("#tableIndicators tbody").append(html);
        });

        $(document).on('click', '.delete-row', function(e) {
            e.preventDefault();
            $(this).closest('tr').remove();
        });
    });
</script>
@endpush
