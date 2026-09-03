<form action="{{ route('cuti.store') }}" id="formCuti" method="POST">
    @csrf
    <x-input-with-icon label="Kode Cuti" name="kode_cuti" icon="ti ti-barcode" maxlength="3" placeholder="Contoh: C01 (Maksimal 3 karakter)" required />
    <x-input-with-icon label="Jenis Cuti" name="jenis_cuti" icon="ti ti-file-description" maxlength="50" placeholder="Contoh: Tahunan (Maksimal 50 karakter)" required />
    <x-input-with-icon label="Jumlah Hari" name="jumlah_hari" icon="ti ti-calendar" type="number" placeholder="Contoh: 12 (Minimal 1, Maksimal 365 hari)" min="1" max="365" required />
    <div class="form-group mb-3">
        <button type="submit" class="btn btn-primary w-100"><i class="ti ti-send"></i>Submit</button>
    </div>
</form>
<script src="{{ asset('/assets/vendor/libs/@form-validation/umd/bundle/popular.min.js') }}"></script>
<script src="{{ asset('/assets/vendor/libs/@form-validation/umd/plugin-bootstrap5/index.min.js') }}"></script>
<script src="{{ asset('/assets/vendor/libs/@form-validation/umd/plugin-auto-focus/index.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/cuti.js') }}"></script>
