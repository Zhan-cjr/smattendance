<form action="{{ route('lemburaturan.store') }}" method="POST" id="formAturanLembur">
    @csrf
    <input type="hidden" name="tipe_hari" value="{{ $tipe_hari }}">

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Mulai Jam</label>
            <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="ti ti-clock-play"></i></span>
                <input type="number" step="0.5" name="jam_dari" id="jam_dari" class="form-control" placeholder="Mulai Jam (Contoh: 0)" required>
            </div>
            <small class="text-muted" style="font-size: 0.7rem;">Mulai Jam (0 untuk jam pertama)</small>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Sampai Jam</label>
            <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="ti ti-clock-stop"></i></span>
                <input type="number" step="0.5" name="jam_sampai" id="jam_sampai" class="form-control" placeholder="Sampai Jam (Contoh: 1)">
            </div>
            <small class="text-muted" style="font-size: 0.7rem;">Sampai Jam (1 untuk 1 jam pertama)</small>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Faktor Pengali (Multipliers)</label>
        <div class="input-group input-group-merge">
            <span class="input-group-text"><i class="ti ti-percentage"></i></span>
            <input type="number" step="0.05" name="faktor" id="faktor" class="form-control" placeholder="Contoh: 1.5 atau 2.0" required>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12 text-end">
            <button class="btn btn-primary w-100" id="btnSimpanAturan" type="submit">
                <i class="ti ti-send me-1"></i> Simpan Aturan
            </button>
        </div>
    </div>
</form>

<script>
    $(function() {
        $("#formAturanLembur").submit(function() {
            const jam_dari = $("#jam_dari").val();
            const faktor = $("#faktor").val();

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

            $("#btnSimpanAturan").prop('disabled', true).text("Loading...");
        });
    });
</script>
