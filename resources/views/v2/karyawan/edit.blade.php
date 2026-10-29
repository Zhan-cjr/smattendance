<form action="{{ route('karyawan.update', Crypt::encrypt($karyawan->nik)) }}" id="formEditKaryawan" method="POST" enctype="multipart/form-data" class="space-y-4" novalidate>
    @csrf
    @method('PUT')

    <!-- ================= 1. DATA PRIBADI & IDENTITAS ================= -->
    <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px;">
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 14px; padding-bottom: 10px; border-bottom: 1px solid #e2e8f0;">
            <div style="width: 28px; height: 28px; border-radius: 8px; background-color: #d1fae5; color: #047857; display: flex; align-items: center; justify-content: center; font-size: 14px;">
                <i class="ti ti-user-check"></i>
            </div>
            <div>
                <h4 style="font-size: 13px; font-weight: 700; color: #0f172a; margin: 0; line-height: 1.2;">1. Identitas & Data Pribadi</h4>
                <p style="font-size: 11px; color: #64748b; margin: 0;">Informasi identitas kependudukan dan biodata karyawan</p>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px;">
            <!-- NIK Karyawan -->
            <div class="space-y-1">
                <label for="nik_show_edit" style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 4px;">
                    NIK Karyawan <span style="color: #ef4444; font-weight: bold;">*</span>
                </label>
                <div style="position: relative; display: flex; align-items: center;">
                    <span style="position: absolute; left: 10px; pointer-events: none; color: #94a3b8; display: flex; align-items: center;">
                        <i class="ti ti-barcode" style="font-size: 16px;"></i>
                    </span>
                    <input type="text" 
                           id="nik_show_edit" 
                           name="nik_show" 
                           value="{{ $karyawan->nik_show ?? $karyawan->nik }}"
                           placeholder="Contoh: 12.34.567" 
                           style="width: 100%; height: 38px; padding-left: 36px; padding-right: 12px; font-size: 13px; font-weight: 500; color: #1e293b; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; transition: border-color 0.15s ease;">
                </div>
            </div>

            <!-- Nama Karyawan -->
            <div class="space-y-1">
                <label for="nama_karyawan_edit" style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 4px;">
                    Nama Lengkap <span style="color: #ef4444; font-weight: bold;">*</span>
                </label>
                <div style="position: relative; display: flex; align-items: center;">
                    <span style="position: absolute; left: 10px; pointer-events: none; color: #94a3b8; display: flex; align-items: center;">
                        <i class="ti ti-user" style="font-size: 16px;"></i>
                    </span>
                    <input type="text" 
                           id="nama_karyawan_edit" 
                           name="nama_karyawan" 
                           value="{{ $karyawan->nama_karyawan }}"
                           placeholder="Masukkan nama lengkap" 
                           style="width: 100%; height: 38px; padding-left: 36px; padding-right: 12px; font-size: 13px; font-weight: 500; color: #1e293b; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; transition: border-color 0.15s ease;">
                </div>
            </div>

            <!-- No KTP -->
            <div class="space-y-1">
                <label for="no_ktp_edit" style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 4px;">
                    No. KTP / NIK KTP <span style="color: #ef4444; font-weight: bold;">*</span>
                </label>
                <div style="position: relative; display: flex; align-items: center;">
                    <span style="position: absolute; left: 10px; pointer-events: none; color: #94a3b8; display: flex; align-items: center;">
                        <i class="ti ti-credit-card" style="font-size: 16px;"></i>
                    </span>
                    <input type="text" 
                           id="no_ktp_edit" 
                           name="no_ktp" 
                           value="{{ $karyawan->no_ktp }}"
                           maxlength="16"
                           placeholder="16 digit nomor KTP" 
                           style="width: 100%; height: 38px; padding-left: 36px; padding-right: 12px; font-size: 13px; font-weight: 500; color: #1e293b; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; transition: border-color 0.15s ease;">
                </div>
            </div>

            <!-- NPWP -->
            <div class="space-y-1">
                <label for="npwp_edit" style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 4px;">
                    NPWP
                </label>
                <div style="position: relative; display: flex; align-items: center;">
                    <span style="position: absolute; left: 10px; pointer-events: none; color: #94a3b8; display: flex; align-items: center;">
                        <i class="ti ti-credit-card-pay" style="font-size: 16px;"></i>
                    </span>
                    <input type="text" 
                           id="npwp_edit" 
                           name="npwp" 
                           value="{{ $karyawan->npwp }}"
                           placeholder="Nomor NPWP (Opsional)" 
                           style="width: 100%; height: 38px; padding-left: 36px; padding-right: 12px; font-size: 13px; font-weight: 500; color: #1e293b; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; transition: border-color 0.15s ease;">
                </div>
            </div>

            <!-- Tempat Lahir -->
            <div class="space-y-1">
                <label for="tempat_lahir_edit" style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 4px;">
                    Tempat Lahir <span style="color: #ef4444; font-weight: bold;">*</span>
                </label>
                <div style="position: relative; display: flex; align-items: center;">
                    <span style="position: absolute; left: 10px; pointer-events: none; color: #94a3b8; display: flex; align-items: center;">
                        <i class="ti ti-map-pin" style="font-size: 16px;"></i>
                    </span>
                    <input type="text" 
                           id="tempat_lahir_edit" 
                           name="tempat_lahir" 
                           value="{{ $karyawan->tempat_lahir }}"
                           placeholder="Kota kelahiran" 
                           style="width: 100%; height: 38px; padding-left: 36px; padding-right: 12px; font-size: 13px; font-weight: 500; color: #1e293b; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; transition: border-color 0.15s ease;">
                </div>
            </div>

            <!-- Tanggal Lahir -->
            <div class="space-y-1">
                <label for="tanggal_lahir_edit" style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 4px;">
                    Tanggal Lahir <span style="color: #ef4444; font-weight: bold;">*</span>
                </label>
                <div style="position: relative; display: flex; align-items: center;">
                    <span style="position: absolute; left: 10px; pointer-events: none; color: #94a3b8; display: flex; align-items: center;">
                        <i class="ti ti-calendar" style="font-size: 16px;"></i>
                    </span>
                    <input type="text" 
                           id="tanggal_lahir_edit" 
                           name="tanggal_lahir" 
                           value="{{ $karyawan->tanggal_lahir }}"
                           placeholder="YYYY-MM-DD"
                           autocomplete="off"
                           class="flatpickr-date"
                           style="width: 100%; height: 38px; padding-left: 36px; padding-right: 36px; font-size: 13px; font-weight: 500; color: #1e293b; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; cursor: pointer; transition: border-color 0.15s ease;">
                    <span style="position: absolute; right: 10px; pointer-events: none; color: #94a3b8; display: flex; align-items: center;">
                        <i class="ti ti-calendar-event" style="font-size: 16px;"></i>
                    </span>
                </div>
            </div>

            <!-- Jenis Kelamin -->
            <div class="space-y-1">
                <label for="jenis_kelamin_edit" style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 4px;">
                    Jenis Kelamin <span style="color: #ef4444; font-weight: bold;">*</span>
                </label>
                <div style="position: relative; display: flex; align-items: center;">
                    <span style="position: absolute; left: 10px; pointer-events: none; color: #94a3b8; display: flex; align-items: center;">
                        <i class="ti ti-gender-intergender" style="font-size: 16px;"></i>
                    </span>
                    <select name="jenis_kelamin" 
                            id="jenis_kelamin_edit" 
                            style="width: 100%; height: 38px; padding-left: 36px; padding-right: 32px; font-size: 13px; font-weight: 500; color: #1e293b; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; cursor: pointer;">
                        <option value="">-- Pilih Jenis Kelamin --</option>
                        <option value="L" {{ $karyawan->jenis_kelamin == 'L' ? 'selected' : '' }}>Laki - Laki</option>
                        <option value="P" {{ $karyawan->jenis_kelamin == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
            </div>

            <!-- Status Perkawinan -->
            <div class="space-y-1">
                <label for="kode_status_kawin_edit" style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 4px;">
                    Status Perkawinan <span style="color: #ef4444; font-weight: bold;">*</span>
                </label>
                <div style="position: relative; display: flex; align-items: center;">
                    <span style="position: absolute; left: 10px; pointer-events: none; color: #94a3b8; display: flex; align-items: center;">
                        <i class="ti ti-heart-handshake" style="font-size: 16px;"></i>
                    </span>
                    <select name="kode_status_kawin" 
                            id="kode_status_kawin_edit" 
                            style="width: 100%; height: 38px; padding-left: 36px; padding-right: 32px; font-size: 13px; font-weight: 500; color: #1e293b; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; cursor: pointer;">
                        <option value="">-- Pilih Status --</option>
                        @foreach ($status_kawin as $sk)
                            <option value="{{ $sk->kode_status_kawin }}" {{ $karyawan->kode_status_kawin == $sk->kode_status_kawin ? 'selected' : '' }}>{{ $sk->kode_status_kawin }} - {{ $sk->status_kawin }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Pendidikan Terakhir -->
            <div class="space-y-1">
                <label for="pendidikan_terakhir_edit" style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 4px;">
                    Pendidikan Terakhir <span style="color: #ef4444; font-weight: bold;">*</span>
                </label>
                <div style="position: relative; display: flex; align-items: center;">
                    <span style="position: absolute; left: 10px; pointer-events: none; color: #94a3b8; display: flex; align-items: center;">
                        <i class="ti ti-school" style="font-size: 16px;"></i>
                    </span>
                    <select name="pendidikan_terakhir" 
                            id="pendidikan_terakhir_edit" 
                            style="width: 100%; height: 38px; padding-left: 36px; padding-right: 32px; font-size: 13px; font-weight: 500; color: #1e293b; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; cursor: pointer;">
                        <option value="">-- Pilih Pendidikan --</option>
                        @foreach (['SD','SMP','SMA','SMK','D1','D2','D3','D4','S1','S2','S3'] as $p)
                            <option value="{{ $p }}" {{ $karyawan->pendidikan_terakhir == $p ? 'selected' : '' }}>{{ $p }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Jurusan -->
            <div class="space-y-1">
                <label for="jurusan_edit" style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 4px;">
                    Jurusan
                </label>
                <div style="position: relative; display: flex; align-items: center;">
                    <span style="position: absolute; left: 10px; pointer-events: none; color: #94a3b8; display: flex; align-items: center;">
                        <i class="ti ti-books" style="font-size: 16px;"></i>
                    </span>
                    <input type="text" 
                           id="jurusan_edit" 
                           name="jurusan" 
                           value="{{ $karyawan->jurusan }}"
                           placeholder="Contoh: Teknik Informatika" 
                           style="width: 100%; height: 38px; padding-left: 36px; padding-right: 12px; font-size: 13px; font-weight: 500; color: #1e293b; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; transition: border-color 0.15s ease;">
                </div>
            </div>
        </div>

        <!-- Alamat Domisili & KTP -->
        <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; margin-top: 12px;">
            <div class="space-y-1">
                <label for="alamat_edit" style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 4px;">
                    Alamat Domisili <span style="color: #ef4444; font-weight: bold;">*</span>
                </label>
                <textarea id="alamat_edit" 
                          name="alamat" 
                          rows="2" 
                          placeholder="Alamat tempat tinggal saat ini" 
                          style="width: 100%; padding: 8px 12px; font-size: 13px; font-weight: 500; color: #1e293b; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; resize: vertical; transition: border-color 0.15s ease;">{{ $karyawan->alamat }}</textarea>
            </div>

            <div class="space-y-1">
                <label for="alamat_sesuai_ktp_edit" style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 4px;">
                    Alamat Sesuai KTP
                </label>
                <textarea id="alamat_sesuai_ktp_edit" 
                          name="alamat_sesuai_ktp" 
                          rows="2" 
                          placeholder="Alamat lengkap sesuai e-KTP" 
                          style="width: 100%; padding: 8px 12px; font-size: 13px; font-weight: 500; color: #1e293b; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; resize: vertical; transition: border-color 0.15s ease;">{{ $karyawan->alamat_sesuai_ktp }}</textarea>
            </div>
        </div>
    </div>

    <!-- ================= 2. KONTAK & REKENING ================= -->
    <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px;">
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 14px; padding-bottom: 10px; border-bottom: 1px solid #e2e8f0;">
            <div style="width: 28px; height: 28px; border-radius: 8px; background-color: #dbeafe; color: #1d4ed8; display: flex; align-items: center; justify-content: center; font-size: 14px;">
                <i class="ti ti-address-book"></i>
            </div>
            <div>
                <h4 style="font-size: 13px; font-weight: 700; color: #0f172a; margin: 0; line-height: 1.2;">2. Kontak & Rekening Bank</h4>
                <p style="font-size: 11px; color: #64748b; margin: 0;">Nomor handphone, email, darurat, dan data payroll</p>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px;">
            <!-- No HP -->
            <div class="space-y-1">
                <label for="no_hp_edit" style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 4px;">
                    No. Handphone / WA <span style="color: #ef4444; font-weight: bold;">*</span>
                </label>
                <div style="position: relative; display: flex; align-items: center;">
                    <span style="position: absolute; left: 10px; pointer-events: none; color: #94a3b8; display: flex; align-items: center;">
                        <i class="ti ti-phone" style="font-size: 16px;"></i>
                    </span>
                    <input type="text" 
                           id="no_hp_edit" 
                           name="no_hp" 
                           value="{{ $karyawan->no_hp }}"
                           maxlength="16"
                           placeholder="08123456789" 
                           style="width: 100%; height: 38px; padding-left: 36px; padding-right: 12px; font-size: 13px; font-weight: 500; color: #1e293b; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; transition: border-color 0.15s ease;">
                </div>
            </div>

            <!-- Email -->
            <div class="space-y-1">
                <label for="email_edit" style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 4px;">
                    Alamat Email
                </label>
                <div style="position: relative; display: flex; align-items: center;">
                    <span style="position: absolute; left: 10px; pointer-events: none; color: #94a3b8; display: flex; align-items: center;">
                        <i class="ti ti-mail" style="font-size: 16px;"></i>
                    </span>
                    <input type="email" 
                           id="email_edit" 
                           name="email" 
                           value="{{ $karyawan->email }}"
                           placeholder="karyawan@email.com" 
                           style="width: 100%; height: 38px; padding-left: 36px; padding-right: 12px; font-size: 13px; font-weight: 500; color: #1e293b; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; transition: border-color 0.15s ease;">
                </div>
            </div>

            <!-- Kontak Darurat -->
            <div class="space-y-1">
                <label for="kontak_darurat_edit" style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 4px;">
                    Kontak Darurat
                </label>
                <div style="position: relative; display: flex; align-items: center;">
                    <span style="position: absolute; left: 10px; pointer-events: none; color: #94a3b8; display: flex; align-items: center;">
                        <i class="ti ti-phone-call" style="font-size: 16px;"></i>
                    </span>
                    <input type="text" 
                           id="kontak_darurat_edit" 
                           name="kontak_darurat" 
                           value="{{ $karyawan->kontak_darurat }}"
                           placeholder="Nomor telepon darurat" 
                           style="width: 100%; height: 38px; padding-left: 36px; padding-right: 12px; font-size: 13px; font-weight: 500; color: #1e293b; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; transition: border-color 0.15s ease;">
                </div>
            </div>

            <!-- Hubungan Kontak Darurat -->
            <div class="space-y-1">
                <label for="hubungan_kontak_darurat_edit" style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 4px;">
                    Hubungan Kontak Darurat
                </label>
                <div style="position: relative; display: flex; align-items: center;">
                    <span style="position: absolute; left: 10px; pointer-events: none; color: #94a3b8; display: flex; align-items: center;">
                        <i class="ti ti-users" style="font-size: 16px;"></i>
                    </span>
                    <input type="text" 
                           id="hubungan_kontak_darurat_edit" 
                           name="hubungan_kontak_darurat" 
                           value="{{ $karyawan->hubungan_kontak_darurat }}"
                           placeholder="Contoh: Orang Tua / Pasangan" 
                           style="width: 100%; height: 38px; padding-left: 36px; padding-right: 12px; font-size: 13px; font-weight: 500; color: #1e293b; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; transition: border-color 0.15s ease;">
                </div>
            </div>

            <!-- Nama Bank -->
            <div class="space-y-1">
                <label for="nama_bank_edit" style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 4px;">
                    Nama Bank
                </label>
                <div style="position: relative; display: flex; align-items: center;">
                    <span style="position: absolute; left: 10px; pointer-events: none; color: #94a3b8; display: flex; align-items: center;">
                        <i class="ti ti-building-bank" style="font-size: 16px;"></i>
                    </span>
                    <input type="text" 
                           id="nama_bank_edit" 
                           name="nama_bank" 
                           value="{{ $karyawan->nama_bank }}"
                           placeholder="Contoh: BCA / Mandiri / BRI" 
                           style="width: 100%; height: 38px; padding-left: 36px; padding-right: 12px; font-size: 13px; font-weight: 500; color: #1e293b; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; transition: border-color 0.15s ease;">
                </div>
            </div>

            <!-- No Rekening -->
            <div class="space-y-1">
                <label for="no_rekening_edit" style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 4px;">
                    No. Rekening
                </label>
                <div style="position: relative; display: flex; align-items: center;">
                    <span style="position: absolute; left: 10px; pointer-events: none; color: #94a3b8; display: flex; align-items: center;">
                        <i class="ti ti-credit-card" style="font-size: 16px;"></i>
                    </span>
                    <input type="text" 
                           id="no_rekening_edit" 
                           name="no_rekening" 
                           value="{{ $karyawan->no_rekening }}"
                           placeholder="Nomor rekening bank" 
                           style="width: 100%; height: 38px; padding-left: 36px; padding-right: 12px; font-size: 13px; font-weight: 500; color: #1e293b; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; transition: border-color 0.15s ease;">
                </div>
            </div>

            <!-- Nama Rekening -->
            <div class="space-y-1" style="grid-column: 1 / -1;">
                <label for="nama_rekening_edit" style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 4px;">
                    Nama Pemilik Rekening
                </label>
                <div style="position: relative; display: flex; align-items: center;">
                    <span style="position: absolute; left: 10px; pointer-events: none; color: #94a3b8; display: flex; align-items: center;">
                        <i class="ti ti-user-circle" style="font-size: 16px;"></i>
                    </span>
                    <input type="text" 
                           id="nama_rekening_edit" 
                           name="nama_rekening" 
                           value="{{ $karyawan->nama_rekening }}"
                           placeholder="Nama sesuai buku tabungan" 
                           style="width: 100%; height: 38px; padding-left: 36px; padding-right: 12px; font-size: 13px; font-weight: 500; color: #1e293b; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; transition: border-color 0.15s ease;">
                </div>
            </div>
        </div>
    </div>

    <!-- ================= 3. PENUGASAN & PEKERJAAN ================= -->
    <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px;">
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 14px; padding-bottom: 10px; border-bottom: 1px solid #e2e8f0;">
            <div style="width: 28px; height: 28px; border-radius: 8px; background-color: #fef3c7; color: #b45309; display: flex; align-items: center; justify-content: center; font-size: 14px;">
                <i class="ti ti-briefcase"></i>
            </div>
            <div>
                <h4 style="font-size: 13px; font-weight: 700; color: #0f172a; margin: 0; line-height: 1.2;">3. Penugasan & Status Kerja</h4>
                <p style="font-size: 11px; color: #64748b; margin: 0;">Penempatan kantor cabang, departemen, jabatan, dan RFID</p>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px;">
            <!-- Kantor Cabang -->
            <div class="space-y-1">
                <label for="kode_cabang_edit" style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 4px;">
                    Kantor Cabang <span style="color: #ef4444; font-weight: bold;">*</span>
                </label>
                <div style="position: relative; display: flex; align-items: center;">
                    <span style="position: absolute; left: 10px; pointer-events: none; color: #94a3b8; display: flex; align-items: center;">
                        <i class="ti ti-building" style="font-size: 16px;"></i>
                    </span>
                    <select name="kode_cabang" 
                            id="kode_cabang_edit" 
                            style="width: 100%; height: 38px; padding-left: 36px; padding-right: 32px; font-size: 13px; font-weight: 500; color: #1e293b; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; cursor: pointer;">
                        <option value="">-- Pilih Cabang --</option>
                        @foreach ($cabang as $c)
                            <option value="{{ $c->kode_cabang }}" {{ $karyawan->kode_cabang == $c->kode_cabang ? 'selected' : '' }}>{{ $c->nama_cabang }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Departemen -->
            <div class="space-y-1">
                <label for="kode_dept_edit" style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 4px;">
                    Departemen <span style="color: #ef4444; font-weight: bold;">*</span>
                </label>
                <div style="position: relative; display: flex; align-items: center;">
                    <span style="position: absolute; left: 10px; pointer-events: none; color: #94a3b8; display: flex; align-items: center;">
                        <i class="ti ti-layout-grid" style="font-size: 16px;"></i>
                    </span>
                    <select name="kode_dept" 
                            id="kode_dept_edit" 
                            style="width: 100%; height: 38px; padding-left: 36px; padding-right: 32px; font-size: 13px; font-weight: 500; color: #1e293b; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; cursor: pointer;">
                        <option value="">-- Pilih Departemen --</option>
                        @foreach ($departemen as $d)
                            <option value="{{ $d->kode_dept }}" {{ $karyawan->kode_dept == $d->kode_dept ? 'selected' : '' }}>{{ strtoupper($d->nama_dept) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Jabatan -->
            <div class="space-y-1">
                <label for="kode_jabatan_edit" style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 4px;">
                    Jabatan <span style="color: #ef4444; font-weight: bold;">*</span>
                </label>
                <div style="position: relative; display: flex; align-items: center;">
                    <span style="position: absolute; left: 10px; pointer-events: none; color: #94a3b8; display: flex; align-items: center;">
                        <i class="ti ti-badge" style="font-size: 16px;"></i>
                    </span>
                    <select name="kode_jabatan" 
                            id="kode_jabatan_edit" 
                            style="width: 100%; height: 38px; padding-left: 36px; padding-right: 32px; font-size: 13px; font-weight: 500; color: #1e293b; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; cursor: pointer;">
                        <option value="">-- Pilih Jabatan --</option>
                        @foreach ($jabatan as $j)
                            <option value="{{ $j->kode_jabatan }}" {{ $karyawan->kode_jabatan == $j->kode_jabatan ? 'selected' : '' }}>{{ strtoupper($j->nama_jabatan) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Status Karyawan -->
            <div class="space-y-1">
                <label for="status_karyawan_edit" style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 4px;">
                    Status Karyawan <span style="color: #ef4444; font-weight: bold;">*</span>
                </label>
                <div style="position: relative; display: flex; align-items: center;">
                    <span style="position: absolute; left: 10px; pointer-events: none; color: #94a3b8; display: flex; align-items: center;">
                        <i class="ti ti-user-star" style="font-size: 16px;"></i>
                    </span>
                    <select name="status_karyawan" 
                            id="status_karyawan_edit" 
                            style="width: 100%; height: 38px; padding-left: 36px; padding-right: 32px; font-size: 13px; font-weight: 500; color: #1e293b; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; cursor: pointer;">
                        <option value="">-- Pilih Status --</option>
                        @foreach ($status_karyawan as $skk)
                            <option value="{{ $skk->kode_status_karyawan }}" {{ $karyawan->status_karyawan == $skk->kode_status_karyawan ? 'selected' : '' }}>{{ $skk->nama_status_karyawan }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Tanggal Masuk -->
            <div class="space-y-1">
                <label for="tanggal_masuk_edit" style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 4px;">
                    Tanggal Masuk <span style="color: #ef4444; font-weight: bold;">*</span>
                </label>
                <div style="position: relative; display: flex; align-items: center;">
                    <span style="position: absolute; left: 10px; pointer-events: none; color: #94a3b8; display: flex; align-items: center;">
                        <i class="ti ti-calendar" style="font-size: 16px;"></i>
                    </span>
                    <input type="text" 
                           id="tanggal_masuk_edit" 
                           name="tanggal_masuk" 
                           value="{{ $karyawan->tanggal_masuk }}"
                           autocomplete="off"
                           class="flatpickr-date"
                           style="width: 100%; height: 38px; padding-left: 36px; padding-right: 36px; font-size: 13px; font-weight: 500; color: #1e293b; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; cursor: pointer; transition: border-color 0.15s ease;">
                    <span style="position: absolute; right: 10px; pointer-events: none; color: #94a3b8; display: flex; align-items: center;">
                        <i class="ti ti-calendar-event" style="font-size: 16px;"></i>
                    </span>
                </div>
            </div>

            <!-- RFID UID -->
            <div class="space-y-1">
                <label for="rfid_uid_edit" style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 4px;">
                    RFID Card UID
                </label>
                <div style="position: relative; display: flex; align-items: center;">
                    <span style="position: absolute; left: 10px; pointer-events: none; color: #94a3b8; display: flex; align-items: center;">
                        <i class="ti ti-id" style="font-size: 16px;"></i>
                    </span>
                    <input type="text" 
                           id="rfid_uid_edit" 
                           name="rfid_uid" 
                           value="{{ $karyawan->rfid_uid }}"
                           placeholder="Tempelkan kartu RFID atau isi UID" 
                           style="width: 100%; height: 38px; padding-left: 36px; padding-right: 12px; font-size: 13px; font-weight: 500; color: #1e293b; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; transition: border-color 0.15s ease;">
                </div>
            </div>

            <!-- PIN Fingerprint -->
            <div class="space-y-1">
                <label for="pin_edit" style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 4px;">
                    PIN Mesin Fingerprint
                </label>
                <div style="position: relative; display: flex; align-items: center;">
                    <span style="position: absolute; left: 10px; pointer-events: none; color: #94a3b8; display: flex; align-items: center;">
                        <i class="ti ti-fingerprint" style="font-size: 16px;"></i>
                    </span>
                    <input type="text" 
                           id="pin_edit" 
                           name="pin" 
                           value="{{ $karyawan->pin }}"
                           placeholder="PIN / User ID Fingerprint" 
                           style="width: 100%; height: 38px; padding-left: 36px; padding-right: 12px; font-size: 13px; font-weight: 500; color: #1e293b; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; transition: border-color 0.15s ease;">
                </div>
            </div>

            <!-- Status Aktif Karyawan -->
            <div class="space-y-1">
                <label for="status_aktif_karyawan_edit" style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 4px;">
                    Status Keaktifan <span style="color: #ef4444; font-weight: bold;">*</span>
                </label>
                <div style="position: relative; display: flex; align-items: center;">
                    <span style="position: absolute; left: 10px; pointer-events: none; color: #94a3b8; display: flex; align-items: center;">
                        <i class="ti ti-user-check" style="font-size: 16px;"></i>
                    </span>
                    <select name="status_aktif_karyawan" 
                            id="status_aktif_karyawan_edit" 
                            style="width: 100%; height: 38px; padding-left: 36px; padding-right: 32px; font-size: 13px; font-weight: 500; color: #1e293b; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; cursor: pointer;">
                        <option value="1" {{ $karyawan->status_aktif_karyawan == '1' ? 'selected' : '' }}>Aktif Bekerja</option>
                        <option value="0" {{ $karyawan->status_aktif_karyawan === '0' ? 'selected' : '' }}>Non-Aktif / Resign</option>
                    </select>
                </div>
            </div>

            <!-- Status Akses Web -->
            <div class="space-y-1" style="grid-column: 1 / -1;">
                <label for="status_akses_web_edit" style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 4px;">
                    Hak Akses Web Login
                </label>
                <div style="position: relative; display: flex; align-items: center;">
                    <span style="position: absolute; left: 10px; pointer-events: none; color: #94a3b8; display: flex; align-items: center;">
                        <i class="ti ti-world" style="font-size: 16px;"></i>
                    </span>
                    <select name="status_akses_web" 
                            id="status_akses_web_edit" 
                            style="width: 100%; height: 38px; padding-left: 36px; padding-right: 32px; font-size: 13px; font-weight: 500; color: #1e293b; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; cursor: pointer;">
                        <option value="1" {{ ($karyawan->status_akses_web ?? '1') != '0' ? 'selected' : '' }}>Aktif (Diizinkan Login Web)</option>
                        <option value="0" {{ ($karyawan->status_akses_web ?? '1') === '0' ? 'selected' : '' }}>Non Aktif (Hanya Aplikasi Mobile)</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= 4. PENGATURAN & FOTO ================= -->
    <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px;">
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 14px; padding-bottom: 10px; border-bottom: 1px solid #e2e8f0;">
            <div style="width: 28px; height: 28px; border-radius: 8px; background-color: #e0e7ff; color: #4338ca; display: flex; align-items: center; justify-content: center; font-size: 14px;">
                <i class="ti ti-settings"></i>
            </div>
            <div>
                <h4 style="font-size: 13px; font-weight: 700; color: #0f172a; margin: 0; line-height: 1.2;">4. Foto Profil & Pajak PPh 21</h4>
                <p style="font-size: 11px; color: #64748b; margin: 0;">Ubah berkas foto profil dan konfigurasi pajak karyawan</p>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; align-items: center;">
            <!-- Foto Upload with Preview -->
            <div class="space-y-2">
                <label style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 4px;">
                    Foto Karyawan
                </label>
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 54px; height: 54px; border-radius: 10px; border: 2px dashed #cbd5e1; background: #ffffff; display: flex; align-items: center; justify-content: center; overflow: hidden; flex-shrink: 0;" id="previewContainerEdit">
                        @if ($karyawan->foto && Storage::disk('public')->exists('/karyawan/' . $karyawan->foto))
                            <img id="previewImageEdit" src="{{ url('storage/karyawan/' . $karyawan->foto) }}" alt="{{ $karyawan->nama_karyawan }}" style="width: 100%; height: 100%; object-fit: cover;">
                            <i class="ti ti-user" style="font-size: 24px; color: #cbd5e1; display: none;" id="previewPlaceholderEdit"></i>
                        @else
                            <i class="ti ti-user" style="font-size: 24px; color: #cbd5e1;" id="previewPlaceholderEdit"></i>
                            <img id="previewImageEdit" src="" alt="Preview" style="width: 100%; height: 100%; object-fit: cover; display: none;">
                        @endif
                    </div>
                    <div style="flex: 1;">
                        <label for="fotoInputEdit" style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; font-size: 12px; font-weight: 600; color: #334155; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; cursor: pointer; transition: all 0.15s ease;">
                            <i class="ti ti-upload" style="font-size: 14px; color: #059669;"></i>
                            <span id="fotoLabelTextEdit">Ubah Berkas Foto</span>
                        </label>
                        <input type="file" name="foto" id="fotoInputEdit" accept="image/*" style="display: none;">
                        <p style="font-size: 11px; color: #94a3b8; margin: 4px 0 0 0;">Format: JPG, PNG, WEBP (Maks. 2MB)</p>
                    </div>
                </div>
            </div>

            <!-- Hitung PPh21 Switch -->
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px;">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <h5 style="font-size: 12px; font-weight: 700; color: #1e293b; margin: 0;">Hitung PPh 21</h5>
                        <p style="font-size: 11px; color: #64748b; margin: 2px 0 0 0;">Aktifkan kalkulasi pajak bulanan</p>
                    </div>
                    <label style="position: relative; display: inline-block; width: 44px; height: 24px; cursor: pointer;">
                        <input type="checkbox" name="hitung_pph21" id="hitung_pph21_edit" value="1" {{ ($karyawan->hitung_pph21 ?? 1) ? 'checked' : '' }} style="opacity: 0; width: 0; height: 0;">
                        <span class="custom-switch-slider"></span>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= SUBMIT ACTION ================= -->
    <div style="padding-top: 10px; display: flex; align-items: center; justify-content: flex-end; gap: 10px; border-top: 1px solid #e2e8f0;">
        <button type="button" onclick="closeModal('modal')" style="padding: 8px 16px; font-size: 12px; font-weight: 600; color: #475569; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; cursor: pointer; transition: background 0.15s ease;">
            Batal
        </button>
        <button type="submit" id="btnUpdateKaryawan" style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 18px; font-size: 12px; font-weight: 700; color: #ffffff; background: #059669; border: none; border-radius: 8px; cursor: pointer; box-shadow: 0 2px 6px rgba(5, 150, 105, 0.3); transition: background 0.15s ease;">
            <i class="ti ti-device-floppy" style="font-size: 16px;"></i>
            <span>Simpan Perubahan</span>
        </button>
    </div>
</form>

<script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
<script>
$(function() {
    // Inisialisasi Flatpickr
    $(".flatpickr-date").flatpickr({
        dateFormat: "Y-m-d",
        allowInput: true
    });

    // Preview Foto Profil
    $("#fotoInputEdit").on("change", function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(evt) {
                $("#previewImageEdit").attr("src", evt.target.result).show();
                $("#previewPlaceholderEdit").hide();
                $("#fotoLabelTextEdit").text(file.name.length > 20 ? file.name.substring(0, 18) + '...' : file.name);
            };
            reader.readAsDataURL(file);
        }
    });

    // Validation Rules like siprenpas
    const validationRulesEdit = {
        'nik_show': { required: true, message: 'NIK Karyawan wajib diisi' },
        'nama_karyawan': { required: true, message: 'Nama lengkap karyawan wajib diisi' },
        'no_ktp': { required: true, message: 'No. KTP wajib diisi', maxLength: 16, maxMessage: 'No. KTP maksimal 16 digit' },
        'tempat_lahir': { required: true, message: 'Tempat lahir wajib diisi' },
        'tanggal_lahir': { required: true, message: 'Tanggal lahir wajib dipilih' },
        'jenis_kelamin': { required: true, message: 'Jenis kelamin wajib dipilih' },
        'kode_status_kawin': { required: true, message: 'Status perkawinan wajib dipilih' },
        'pendidikan_terakhir': { required: true, message: 'Pendidikan terakhir wajib dipilih' },
        'alamat': { required: true, message: 'Alamat domisili wajib diisi' },
        'no_hp': { required: true, message: 'Nomor handphone wajib diisi', maxLength: 16, maxMessage: 'No. HP maksimal 16 karakter' },
        'kode_cabang': { required: true, message: 'Kantor cabang wajib dipilih' },
        'kode_dept': { required: true, message: 'Departemen wajib dipilih' },
        'kode_jabatan': { required: true, message: 'Jabatan wajib dipilih' },
        'status_karyawan': { required: true, message: 'Status karyawan wajib dipilih' },
        'tanggal_masuk': { required: true, message: 'Tanggal masuk wajib dipilih' }
    };

    function showErrorEdit(element, message) {
        const $el = $(element);
        const $container = $el.closest('.space-y-1, .space-y-2');
        
        $el.css({
            'border-color': '#ef4444',
            'background-color': '#fff1f2',
            'box-shadow': '0 0 0 2px rgba(239, 68, 68, 0.2)'
        });
        
        $el.siblings('span').find('i').css('color', '#ef4444');
        
        $container.find('.error-msg').remove();
        $container.append(`
            <p class="error-msg" style="font-size: 11px; font-weight: 600; color: #ef4444; margin: 4px 0 0 0; display: flex; align-items: center; gap: 4px;">
                <i class="ti ti-alert-circle" style="font-size: 13px;"></i>
                <span>${message}</span>
            </p>
        `);
    }

    function clearErrorEdit(element) {
        const $el = $(element);
        const $container = $el.closest('.space-y-1, .space-y-2');
        
        $el.css({
            'border-color': '#cbd5e1',
            'background-color': '#ffffff',
            'box-shadow': 'none'
        });
        
        $el.siblings('span').find('i').css('color', '#94a3b8');
        $container.find('.error-msg').remove();
    }

    function validateSingleFieldEdit(el) {
        const $el = $(el);
        const name = $el.attr('name') || $el.attr('id');
        const val = ($el.val() || '').toString().trim();

        const rule = validationRulesEdit[name];
        if (!rule) {
            clearErrorEdit($el);
            return true;
        }

        if (rule.required && !val) {
            showErrorEdit($el, rule.message);
            return false;
        }

        if (rule.maxLength && val.length > rule.maxLength) {
            showErrorEdit($el, rule.maxMessage || `Maksimal ${rule.maxLength} karakter`);
            return false;
        }

        clearErrorEdit($el);
        return true;
    }

    // Realtime validation trigger
    $("#formEditKaryawan").on('input change blur', 'input, select, textarea', function(e) {
        const $this = $(this);
        const hasError = $this.css('border-color') === 'rgb(239, 68, 68)';
        const val = ($this.val() || '').toString().trim();
        
        if (e.type === 'blur' || val !== '' || hasError) {
            validateSingleFieldEdit(this);
        }
    });

    // Submit validation
    $("#formEditKaryawan").on('submit', function(e) {
        let isValid = true;
        let firstInvalidEl = null;

        Object.keys(validationRulesEdit).forEach(function(fieldName) {
            const $el = $("#formEditKaryawan").find(`[name="${fieldName}"]`);
            if ($el.length > 0 && $el.is(':visible')) {
                const valid = validateSingleFieldEdit($el);
                if (!valid) {
                    isValid = false;
                    if (!firstInvalidEl) {
                        firstInvalidEl = $el;
                    }
                }
            }
        });

        if (!isValid) {
            e.preventDefault();
            if (firstInvalidEl) {
                firstInvalidEl.focus();
                if (firstInvalidEl[0].scrollIntoView) {
                    firstInvalidEl[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Formulir Belum Lengkap',
                    text: 'Silakan periksa kembali field yang bertanda merah.',
                    customClass: {
                        popup: 'rounded-2xl shadow-xl'
                    }
                });
            }
            return false;
        }

        const $btn = $("#btnUpdateKaryawan");
        $btn.prop('disabled', true).html('<div style="width: 16px; height: 16px; border: 2px solid #ffffff; border-top-color: transparent; border-radius: 50%; animation: spin 0.8s linear infinite; display: inline-block;"></div><span>Menyimpan...</span>');
    });
});
</script>
