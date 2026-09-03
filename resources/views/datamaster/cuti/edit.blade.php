<form action="{{ route('cuti.update', Crypt::encrypt($cuti->kode_cuti)) }}" id="formCuti" method="POST">
    @csrf
    @method('PUT')
    <x-input-with-icon label="Kode Cuti" name="kode_cuti" icon="ti ti-barcode" value="{{ $cuti->kode_cuti }}" readonly />
    <x-input-with-icon label="Jenis Cuti" name="jenis_cuti" icon="ti ti-file-description" value="{{ $cuti->jenis_cuti }}" maxlength="50" placeholder="Contoh: Tahunan (Maksimal 50 karakter)" required />
    <x-input-with-icon label="Jumlah Hari" name="jumlah_hari" icon="ti ti-calendar" value="{{ $cuti->jumlah_hari }}" type="number" placeholder="Contoh: 12 (Minimal 1, Maksimal 365 hari)" min="1" max="365" required />
    <div class="form-group mb-3">
        <button type="submit" class="btn btn-primary w-100"><i class="ti ti-send"></i>Submit</button>
    </div>
</form>
<script src="{{ asset('/assets/vendor/libs/@form-validation/umd/bundle/popular.min.js') }}"></script>
<script src="{{ asset('/assets/vendor/libs/@form-validation/umd/plugin-bootstrap5/index.min.js') }}"></script>
<script src="{{ asset('/assets/vendor/libs/@form-validation/umd/plugin-auto-focus/index.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/cuti.js') }}"></script>
