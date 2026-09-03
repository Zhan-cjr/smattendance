<form action="{{ route('grup.update', Crypt::encrypt($grup->kode_grup)) }}" method="POST" id="formGrup">
    @csrf
    @method('PUT')
    <x-input-with-icon label="Kode Grup" name="kode_grup" icon="ti ti-barcode" value="{{ $grup->kode_grup }}" maxlength="3" placeholder="Contoh: GR1 (Maksimal 3 karakter)" />
    <x-input-with-icon label="Nama Grup" name="nama_grup" icon="ti ti-users" value="{{ $grup->nama_grup }}" maxlength="50" placeholder="Contoh: Grup Produksi (Maksimal 50 karakter)" required />
    <div class="form-group mb-3">
        <button type="submit" class="btn btn-primary w-100"><i class="ti ti-send me-1"></i> Submit</button>
    </div>
</form>
<script src="{{ asset('assets/js/pages/grup.js') }}"></script>



































