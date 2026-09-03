<form action="{{ route('grup.store') }}" method="POST" id="formGrup">
    @csrf
    <x-input-with-icon label="Kode Grup" name="kode_grup" icon="ti ti-barcode" maxlength="3" placeholder="Contoh: GR1 (Maksimal 3 karakter)" required />
    <x-input-with-icon label="Nama Grup" name="nama_grup" icon="ti ti-users" maxlength="50" placeholder="Contoh: Grup Produksi (Maksimal 50 karakter)" required />
    <div class="form-group mb-3">
        <button type="submit" class="btn btn-primary w-100"><i class="ti ti-send me-1"></i> Submit</button>
    </div>
</form>
<script src="{{ asset('assets/js/pages/grup.js') }}"></script>



































