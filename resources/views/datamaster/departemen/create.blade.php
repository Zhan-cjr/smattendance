<form action="{{ route('departemen.store') }}" method="POST" id="formDepartemen">
   @csrf
   <x-input-with-icon label="Kode Departemen" name="kode_dept" icon="ti ti-barcode" maxlength="3" placeholder="Maksimal 3 karakter" required />
   <x-input-with-icon label="Nama Departemen" name="nama_dept" icon="ti ti-building" maxlength="30" placeholder="Maksimal 30 karakter" required />
   <div class="form-group mb-3">
      <button type="submit" class="btn btn-primary w-100"><i class="ti ti-send me-1"></i> Submit</button>
   </div>
</form>
<script src="{{ asset('assets/js/pages/departemen.js') }}"></script>
