<form action="{{ route('lemburaturan.update', $aturan->id) }}" method="POST" id="formEditAturanLembur">
    @csrf
    @method('PUT')
    <input type="hidden" name="tipe_hari" value="{{ $aturan->tipe_hari }}">

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Mulai Jam</label>
            <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="ti ti-clock"></i></span>
                <input type="number" step="1" name="jam_dari" id="jam_dari_edit" class="form-control" value="{{ $aturan->jam_dari }}" placeholder="1" required>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Sampai Jam</label>
            <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="ti ti-clock"></i></span>
                <input type="number" step="1" name="jam_sampai" id="jam_sampai_edit" class="form-control" value="{{ $aturan->jam_sampai }}" placeholder="Kosongkan jika seterusnya">
            </div>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Faktor Pengali (Multipliers)</label>
        <div class="input-group input-group-merge">
            <span class="input-group-text"><i class="ti ti-percentage"></i></span>
            <input type="number" step="0.05" name="faktor" id="faktor_edit" class="form-control" value="{{ $aturan->faktor }}" placeholder="Contoh: 1.5 atau 2.0" required>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12 text-end">
            <button class="btn btn-primary w-100" id="btnUpdateAturan" type="submit">
                <i class="ti ti-send me-1"></i> Update Aturan
            </button>
        </div>
    </div>
</form>

<script>
    $(function() {
        $("#formEditAturanLembur").submit(function() {
            const jam_dari = $("#jam_dari_edit").val();
            const faktor = $("#faktor_edit").val();

            if (jam_dari === "") {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Jam Dari harus diisi',
                    confirmButtonColor: '#3085d6',
                });
                return false;
            } else if (faktor === "") {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Faktor Pengali harus diisi',
                    confirmButtonColor: '#3085d6',
                });
                return false;
            }

            $("#btnUpdateAturan").prop('disabled', true).text("Updating...");
        });
    });
</script>
