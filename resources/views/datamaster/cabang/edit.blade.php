<form action="{{ route('cabang.update', Crypt::encrypt($cabang->kode_cabang)) }}" id="formeditCabang" method="POST">
    @csrf
    @method('PUT')
    <x-input-with-icon icon="ti ti-barcode" label="Kode Cabang" name="kode_cabang" value="{{ $cabang->kode_cabang }}" readonly />
    <x-input-with-icon icon="ti ti-file-text" label="Nama Cabang" name="nama_cabang" value="{{ $cabang->nama_cabang }}" maxlength="50" placeholder="Contoh: TASIKMALAYA (Maksimal 50 karakter)" required />
    <x-input-with-icon icon="ti ti-map-pin" label="Alamat Cabang" name="alamat_cabang" value="{{ $cabang->alamat_cabang }}" maxlength="100" placeholder="Contoh: Jln. Perintis Kemerdekaan No. 80 (Maksimal 100 karakter)" required />
    <x-input-with-icon icon="ti ti-phone" label="Telepon Cabang" name="telepon_cabang" value="{{ $cabang->telepon_cabang }}" maxlength="13" placeholder="Contoh: 0265311766 (Maksimal 13 karakter)" required />
    
    <!-- Peta untuk memilih lokasi -->
    <div class="form-group mb-3">
        <label class="form-label">Pilih Lokasi di Peta</label>
        <div class="input-group mb-2">
            <input type="text" id="searchLocation" class="form-control" placeholder="Cari lokasi (contoh: Tasikmalaya, Jawa Barat)" />
            <button type="button" class="btn btn-primary" id="btnSearchLocation">
                <i class="ti ti-search me-1"></i> Cari
            </button>
        </div>
        <div id="map" style="height: 400px; border-radius: 8px; border: 1px solid #ddd;"></div>
        <small class="text-muted mt-2 d-block">
            <i class="ti ti-info-circle me-1"></i>Klik pada peta atau drag marker untuk memilih lokasi
        </small>
    </div>
    
    <x-input-with-icon icon="ti ti-map-pin" label="Lokasi Cabang (Latitude, Longitude)" name="lokasi_cabang" value="{{ $cabang->lokasi_cabang }}" placeholder="Contoh: -7.317623,108.199358 (Koordinat GPS)" readonly required />
    <x-input-with-icon icon="ti ti-access-point" label="Radius Cabang" name="radius_cabang" value="{{ $cabang->radius_cabang }}" type="number" placeholder="Contoh: 30 (dalam meter, minimal 1)" min="1" max="9999" required />
    
    <div class="form-group mb-3">
        <label for="timezone" class="form-label" style="font-weight: 600;">
            Zona Waktu Cabang <span class="text-danger">*</span>
        </label>
        <div class="input-group input-group-merge">
            <span class="input-group-text"><i class="ti ti-clock"></i></span>
            <select class="form-select" name="timezone" id="timezone" required>
                <option value="">Pilih Zona Waktu</option>
                <option value="Asia/Jakarta" @selected(($cabang->timezone ?? $defaultTimezone ?? 'Asia/Jakarta') == 'Asia/Jakarta')>Asia/Jakarta (WIB)</option>
                <option value="Asia/Makassar" @selected(($cabang->timezone ?? $defaultTimezone ?? 'Asia/Jakarta') == 'Asia/Makassar')>Asia/Makassar (WITA)</option>
                <option value="Asia/Jayapura" @selected(($cabang->timezone ?? $defaultTimezone ?? 'Asia/Jakarta') == 'Asia/Jayapura')>Asia/Jayapura (WIT)</option>
                <option value="Asia/Singapore" @selected(($cabang->timezone ?? $defaultTimezone ?? 'Asia/Jakarta') == 'Asia/Singapore')>Asia/Singapore</option>
                <option value="Asia/Kuala_Lumpur" @selected(($cabang->timezone ?? $defaultTimezone ?? 'Asia/Jakarta') == 'Asia/Kuala_Lumpur')>Asia/Kuala_Lumpur</option>
                <option value="Asia/Bangkok" @selected(($cabang->timezone ?? $defaultTimezone ?? 'Asia/Jakarta') == 'Asia/Bangkok')>Asia/Bangkok</option>
                <option value="Asia/Manila" @selected(($cabang->timezone ?? $defaultTimezone ?? 'Asia/Jakarta') == 'Asia/Manila')>Asia/Manila</option>
                <option value="UTC" @selected(($cabang->timezone ?? $defaultTimezone ?? 'Asia/Jakarta') == 'UTC')>UTC</option>
            </select>
        </div>
        <small class="text-muted">Zona waktu untuk cabang ini</small>
    </div>
    
    <div class="form-group">
        <button class="btn btn-primary w-100" type="submit">
            <ion-icon name="send-outline" class="me-1"></ion-icon>
            Update
        </button>
    </div>
</form>

<script src="{{ asset('/assets/vendor/libs/@form-validation/umd/bundle/popular.min.js') }}"></script>
<script src="{{ asset('/assets/vendor/libs/@form-validation/umd/plugin-bootstrap5/index.min.js') }}"></script>
<script src="{{ asset('/assets/vendor/libs/@form-validation/umd/plugin-auto-focus/index.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/cabang/edit.js') }}"></script>
