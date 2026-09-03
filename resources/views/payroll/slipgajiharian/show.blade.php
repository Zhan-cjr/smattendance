<form action="{{ route('slipgajiharian.cetak') }}" method="POST" target="_blank" id="formCetakHarianDetail">
    @csrf
    <input type="hidden" name="dari" value="{{ $slipgaji->dari }}">
    <input type="hidden" name="sampai" value="{{ $slipgaji->sampai }}">
    
    <div class="row mb-3">
        <div class="col-12 text-end">
            <button type="submit" name="cetakButton" class="btn btn-primary">
                <i class="ti ti-printer me-1"></i> Cetak Slip Gaji
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                <table class="table table-bordered table-striped" id="tableDetailHarian">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th width="50" class="text-center">
                                <input type="checkbox" class="form-check-input" id="checkAllHarianDetail">
                            </th>
                            <th>NIK</th>
                            <th>Nama Karyawan</th>
                            <th>Jabatan</th>
                            <th>Departemen</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($detail as $d)
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="nik[]" value="{{ $d->nik }}" class="form-check-input checkItemHarianDetail">
                                </td>
                                <td>{{ $d->nik }}</td>
                                <td>{{ $d->nama_karyawan }}</td>
                                <td>{{ $d->nama_jabatan }}</td>
                                <td>{{ $d->nama_dept }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>

<script>
    $(function() {
        // Check All Handle
        $("#checkAllHarianDetail").click(function() {
            $(".checkItemHarianDetail").prop('checked', $(this).prop('checked'));
        });

        // Toggle checkAll state when single items are clicked
        $(".checkItemHarianDetail").click(function() {
            if (!$(this).prop("checked")) {
                $("#checkAllHarianDetail").prop("checked", false);
            }
            if ($(".checkItemHarianDetail:checked").length == $(".checkItemHarianDetail").length) {
                $("#checkAllHarianDetail").prop("checked", true);
            }
        });

        // Initial state: check all
        $("#checkAllHarianDetail").prop('checked', true);
        $(".checkItemHarianDetail").prop('checked', true);

        // Form Validation before submit
        $("#formCetakHarianDetail").submit(function(e) {
            if ($(".checkItemHarianDetail:checked").length == 0) {
                Swal.fire({
                    title: "Oops!",
                    text: 'Pilih minimal 1 karyawan untuk dicetak !',
                    icon: "warning",
                    showConfirmButton: true,
                });
                return false;
            }
        });
    });
</script>
