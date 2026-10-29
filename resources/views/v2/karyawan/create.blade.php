<form action="{{ route('karyawan.store') }}" id="formcreateKaryawan" method="POST" enctype="multipart/form-data" class="space-y-4" novalidate>
    @csrf

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
                <label for="nik_show" style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 4px;">
                    NIK Karyawan <span style="color: #ef4444; font-weight: bold;">*</span>
                </label>
                <div style="position: relative; display: flex; align-items: center;">
                    <span style="position: absolute; left: 10px; pointer-events: none; color: #94a3b8; display: flex; align-items: center;">
                        <i class="ti ti-barcode" style="font-size: 16px;"></i>
                    </span>
                    <input type="text" 
                           id="nik_show" 
                           name="nik_show" 
                           placeholder="Contoh: 12.34.567" 
                           style="width: 100%; height: 38px; padding-left: 36px; padding-right: 12px; font-size: 13px; font-weight: 500; color: #1e293b; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; transition: border-color 0.15s ease;">
                </div>
            </div>

            <!-- Nama Karyawan -->
            <div class="space-y-1">
                <label for="nama_karyawan" style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 4px;">
                    Nama Lengkap <span style="color: #ef4444; font-weight: bold;">*</span>
                </label>
                <div style="position: relative; display: flex; align-items: center;">
                    <span style="position: absolute; left: 10px; pointer-events: none; color: #94a3b8; display: flex; align-items: center;">
                        <i class="ti ti-user" style="font-size: 16px;"></i>
                    </span>
                    <input type="text" 
                           id="nama_karyawan" 
                           name="nama_karyawan" 
                           placeholder="Masukkan nama lengkap" 
                           style="width: 100%; height: 38px; padding-left: 36px; padding-right: 12px; font-size: 13px; font-weight: 500; color: #1e293b; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; transition: border-color 0.15s ease;">
                </div>
            </div>

            <!-- No KTP -->
            <div class="space-y-1">
                <label for="no_ktp" style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 4px;">
                    No. KTP / NIK KTP <span style="color: #ef4444; font-weight: bold;">*</span>
                </label>
                <div style="position: relative; display: flex; align-items: center;">
                    <span style="position: absolute; left: 10px; pointer-events: none; color: #94a3b8; display: flex; align-items: center;">
                        <i class="ti ti-credit-card" style="font-size: 16px;"></i>
                    </span>
                    <input type="text" 
                           id="no_ktp" 
                           name="no_ktp" 
                           maxlength="16"
                           placeholder="16 digit nomor KTP" 
                           style="width: 100%; height: 38px; padding-left: 36px; padding-right: 12px; font-size: 13px; font-weight: 500; color: #1e293b; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; transition: border-color 0.15s ease;">
                </div>
            </div>

            <!-- NPWP -->
            <div class="space-y-1">
                <label for="npwp" style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 4px;">
                    NPWP
                </label>
                <div style="position: relative; display: flex; align-items: center;">
                    <span style="position: absolute; left: 10px; pointer-events: none; color: #94a3b8; display: flex; align-items: center;">
                        <i class="ti ti-credit-card-pay" style="font-size: 16px;"></i>
                    </span>
                    <input type="text" 
                           id="npwp" 
                           name="npwp" 
                           placeholder="Nomor NPWP (Opsional)" 
                           style="width: 100%; height: 38px; padding-left: 36px; padding-right: 12px; font-size: 13px; font-weight: 500; color: #1e293b; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; transition: border-color 0.15s ease;">
                </div>
            </div>

            <!-- Tempat Lahir -->
            <div class="space-y-1">
                <label for="tempat_lahir" style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 4px;">
                    Tempat Lahir <span style="color: #ef4444; font-weight: bold;">*</span>
                </label>
                <div style="position: relative; display: flex; align-items: center;">
                    <span style="position: absolute; left: 10px; pointer-events: none; color: #94a3b8; display: flex; align-items: center;">
                        <i class="ti ti-map-pin" style="font-size: 16px;"></i>
                    </span>
                    <input type="text" 
                           id="tempat_lahir" 
                           name="tempat_lahir" 
                           placeholder="Kota kelahiran" 
                           style="width: 100%; height: 38px; padding-left: 36px; padding-right: 12px; font-size: 13px; font-weight: 500; color: #1e293b; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; transition: border-color 0.15s ease;">
                </div>
            </div>

            <!-- Tanggal Lahir -->
            <div class="space-y-1">
                <label for="tanggal_lahir" style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 4px;">
                    Tanggal Lahir <span style="color: #ef4444; font-weight: bold;">*</span>
                </label>
                <div style="position: relative; display: flex; align-items: center;">
                    <span style="position: absolute; left: 10px; pointer-events: none; color: #94a3b8; display: flex; align-items: center;">
                        <i class="ti ti-calendar" style="font-size: 16px;"></i>
                    </span>
                    <input type="text" 
                           id="tanggal_lahir" 
                           name="tanggal_lahir" 
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
                <label for="jenis_kelamin" style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 4px;">
                    Jenis Kelamin <span style="color: #ef4444; font-weight: bold;">*</span>
                </label>
                <div style="position: relative; display: flex; align-items: center;">
                    <span style="position: absolute; left: 10px; pointer-events: none; color: #94a3b8; display: flex; align-items: center;">
                        <i class="ti ti-gender-intergender" style="font-size: 16px;"></i>
                    </span>
                    <select name="jenis_kelamin" 
                            id="jenis_kelamin" 
                            style="width: 100%; height: 38px; padding-left: 36px; padding-right: 32px; font-size: 13px; font-weight: 500; color: #1e293b; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; cursor: pointer;">
                        <option value="">-- Pilih Jenis Kelamin --</option>
                        <option value="L">Laki - Laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
            </div>

            <!-- Status Perkawinan -->
            <div class="space-y-1">
                <label for="kode_status_kawin" style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 4px;">
                    Status Perkawinan <span style="color: #ef4444; font-weight: bold;">*</span>
                </label>
                <div style="position: relative; display: flex; align-items: center;">
                    <span style="position: absolute; left: 10px; pointer-events: none; color: #94a3b8; display: flex; align-items: center;">
                        <i class="ti ti-heart-handshake" style="font-size: 16px;"></i>
                    </span>
                    <select name="kode_status_kawin" 
                            id="kode_status_kawin" 
                            style="width: 100%; height: 38px; padding-left: 36px; padding-right: 32px; font-size: 13px; font-weight: 500; color: #1e293b; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; cursor: pointer;">
                        <option value="">-- Pilih Status --</option>
                        @foreach ($status_kawin as $sk)
                            <option value="{{ $sk->kode_status_kawin }}">{{ $sk->kode_status_kawin }} - {{ $sk->status_kawin }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Pendidikan Terakhir -->
            <div class="space-y-1">
                <label for="pendidikan_terakhir" style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 4px;">
                    Pendidikan Terakhir <span style="color: #ef4444; font-weight: bold;">*</span>
                </label>
                <div style="position: relative; display: flex; align-items: center;">
                    <span style="position: absolute; left: 10px; pointer-events: none; color: #94a3b8; display: flex; align-items: center;">
                        <i class="ti ti-school" style="font-size: 16px;"></i>
                    </span>
                    <select name="pendidikan_terakhir" 
                            id="pendidikan_terakhir" 
                            style="width: 100%; height: 38px; padding-left: 36px; padding-right: 32px; font-size: 13px; font-weight: 500; color: #1e293b; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; cursor: pointer;">
                        <option value="">-- Pilih Pendidikan --</option>
                        <option value="SD">SD</option>
                        <option value="SMP">SMP</option>
                        <option value="SMA">SMA</option>
                        <option value="SMK">SMK</option>
                        <option value="D1">D1</option>
                        <option value="D2">D2</option>
                        <option value="D3">D3</option>
                        <option value="D4">D4</option>
                        <option value="S1">S1</option>
                        <option value="S2">S2</option>
                        <option value="S3">S3</option>
                    </select>
                </div>
            </div>

            <!-- Jurusan -->
            <div class="space-y-1">
                <label for="jurusan" style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 4px;">
                    Jurusan
                </label>
                <div style="position: relative; display: flex; align-items: center;">
                    <span style="position: absolute; left: 10px; pointer-events: none; color: #94a3b8; display: flex; align-items: center;">
                        <i class="ti ti-books" style="font-size: 16px;"></i>
                    </span>
                    <input type="text" 
                           id="jurusan" 
                           name="jurusan" 
                           placeholder="Contoh: Teknik Informatika" 
                           style="width: 100%; height: 38px; padding-left: 36px; padding-right: 12px; font-size: 13px; font-weight: 500; color: #1e293b; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; transition: border-color 0.15s ease;">
                </div>
            </div>
        </div>

        <!-- Alamat Domisili & KTP -->
        <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; margin-top: 12px;">
            <div class="space-y-1">
                <label for="alamat" style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 4px;">
                    Alamat Domisili <span style="color: #ef4444; font-weight: bold;">*</span>
                </label>
                <textarea id="alamat" 
                          name="alamat" 
                          rows="2" 
                          placeholder="Alamat tempat tinggal saat ini" 
                          style="width: 100%; padding: 8px 12px; font-size: 13px; font-weight: 500; color: #1e293b; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; resize: vertical; transition: border-color 0.15s ease;"></textarea>
            </div>

            <div class="space-y-1">
                <label for="alamat_sesuai_ktp" style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 4px;">
                    Alamat Sesuai KTP
                </label>
                <textarea id="alamat_sesuai_ktp" 
                          name="alamat_sesuai_ktp" 
                          rows="2" 
                          placeholder="Alamat lengkap sesuai e-KTP" 
                          style="width: 100%; padding: 8px 12px; font-size: 13px; font-weight: 500; color: #1e293b; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; resize: vertical; transition: border-color 0.15s ease;"></textarea>
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
                <label for="no_hp" style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 4px;">
                    No. Handphone / WA <span style="color: #ef4444; font-weight: bold;">*</span>
                </label>
                <div style="position: relative; display: flex; align-items: center;">
                    <span style="position: absolute; left: 10px; pointer-events: none; color: #94a3b8; display: flex; align-items: center;">
                        <i class="ti ti-phone" style="font-size: 16px;"></i>
                    </span>
                    <input type="text" 
                           id="no_hp" 
                           name="no_hp" 
                           maxlength="16"
                           placeholder="08123456789" 
                           style="width: 100%; height: 38px; padding-left: 36px; padding-right: 12px; font-size: 13px; font-weight: 500; color: #1e293b; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; transition: border-color 0.15s ease;">
                </div>
            </div>

            <!-- Email -->
            <div class="space-y-1">
                <label for="email" style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 4px;">
                    Alamat Email
                </label>
                <div style="position: relative; display: flex; align-items: center;">
                    <span style="position: absolute; left: 10px; pointer-events: none; color: #94a3b8; display: flex; align-items: center;">
                        <i class="ti ti-mail" style="font-size: 16px;"></i>
                    </span>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           placeholder="karyawan@email.com" 
                           style="width: 100%; height: 38px; padding-left: 36px; padding-right: 12px; font-size: 13px; font-weight: 500; color: #1e293b; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; transition: border-color 0.15s ease;">
                </div>
            </div>

            <!-- Kontak Darurat -->
            <div class="space-y-1">
                <label for="kontak_darurat" style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 4px;">
                    Kontak Darurat
                </label>
                <div style="position: relative; display: flex; align-items: center;">
                    <span style="position: absolute; left: 10px; pointer-events: none; color: #94a3b8; display: flex; align-items: center;">
                        <i class="ti ti-phone-call" style="font-size: 16px;"></i>
                    </span>
                    <input type="text" 
                           id="kontak_darurat" 
                           name="kontak_darurat" 
                           placeholder="Nomor telepon darurat" 
                           style="width: 100%; height: 38px; padding-left: 36px; padding-right: 12px; font-size: 13px; font-weight: 500; color: #1e293b; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; transition: border-color 0.15s ease;">
                </div>
            </div>

            <!-- Hubungan Kontak Darurat -->
            <div class="space-y-1">
                <label for="hubungan_kontak_darurat" style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 4px;">
                    Hubungan Kontak Darurat
                </label>
                <div style="position: relative; display: flex; align-items: center;">
                    <span style="position: absolute; left: 10px; pointer-events: none; color: #94a3b8; display: flex; align-items: center;">
                        <i class="ti ti-users" style="font-size: 16px;"></i>
                    </span>
                    <input type="text" 
                           id="hubungan_kontak_darurat" 
                           name="hubungan_kontak_darurat" 
                           placeholder="Contoh: Orang Tua / Pasangan" 
                           style="width: 100%; height: 38px; padding-left: 36px; padding-right: 12px; font-size: 13px; font-weight: 500; color: #1e293b; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; transition: border-color 0.15s ease;">
                </div>
            </div>

            <!-- Nama Bank -->
            <div class="space-y-1">
                <label for="nama_bank" style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 4px;">
                    Nama Bank
                </label>
                <div style="position: relative; display: flex; align-items: center;">
                    <span style="position: absolute; left: 10px; pointer-events: none; color: #94a3b8; display: flex; align-items: center;">
                        <i class="ti ti-building-bank" style="font-size: 16px;"></i>
                    </span>
                    <input type="text" 
                           id="nama_bank" 
                           name="nama_bank" 
                           placeholder="Contoh: BCA / Mandiri / BRI" 
                           style="width: 100%; height: 38px; padding-left: 36px; padding-right: 12px; font-size: 13px; font-weight: 500; color: #1e293b; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; transition: border-color 0.15s ease;">
                </div>
            </div>

            <!-- No Rekening -->
            <div class="space-y-1">
                <label for="no_rekening" style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 4px;">
                    No. Rekening
                </label>
                <div style="position: relative; display: flex; align-items: center;">
                    <span style="position: absolute; left: 10px; pointer-events: none; color: #94a3b8; display: flex; align-items: center;">
                        <i class="ti ti-credit-card" style="font-size: 16px;"></i>
                    </span>
                    <input type="text" 
                           id="no_rekening" 
                           name="no_rekening" 
                           placeholder="Nomor rekening bank" 
                           style="width: 100%; height: 38px; padding-left: 36px; padding-right: 12px; font-size: 13px; font-weight: 500; color: #1e293b; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; transition: border-color 0.15s ease;">
                </div>
            </div>

            <!-- Nama Rekening -->
            <div class="space-y-1" style="grid-column: 1 / -1;">
                <label for="nama_rekening" style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 4px;">
                    Nama Pemilik Rekening
                </label>
                <div style="position: relative; display: flex; align-items: center;">
                    <span style="position: absolute; left: 10px; pointer-events: none; color: #94a3b8; display: flex; align-items: center;">
                        <i class="ti ti-user-circle" style="font-size: 16px;"></i>
                    </span>
                    <input type="text" 
                           id="nama_rekening" 
                           name="nama_rekening" 
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
                <label for="kode_cabang" style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 4px;">
                    Kantor Cabang <span style="color: #ef4444; font-weight: bold;">*</span>
                </label>
                <div style="position: relative; display: flex; align-items: center;">
                    <span style="position: absolute; left: 10px; pointer-events: none; color: #94a3b8; display: flex; align-items: center;">
                        <i class="ti ti-building" style="font-size: 16px;"></i>
                    </span>
                    <select name="kode_cabang" 
                            id="kode_cabang" 
                            style="width: 100%; height: 38px; padding-left: 36px; padding-right: 32px; font-size: 13px; font-weight: 500; color: #1e293b; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; cursor: pointer;">
                        <option value="">-- Pilih Cabang --</option>
                        @foreach ($cabang as $c)
                            <option value="{{ $c->kode_cabang }}">{{ $c->nama_cabang }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Departemen -->
            <div class="space-y-1">
                <label for="kode_dept" style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 4px;">
                    Departemen <span style="color: #ef4444; font-weight: bold;">*</span>
                </label>
                <div style="position: relative; display: flex; align-items: center;">
                    <span style="position: absolute; left: 10px; pointer-events: none; color: #94a3b8; display: flex; align-items: center;">
                        <i class="ti ti-layout-grid" style="font-size: 16px;"></i>
                    </span>
                    <select name="kode_dept" 
                            id="kode_dept" 
                            style="width: 100%; height: 38px; padding-left: 36px; padding-right: 32px; font-size: 13px; font-weight: 500; color: #1e293b; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; cursor: pointer;">
                        <option value="">-- Pilih Departemen --</option>
                        @foreach ($departemen as $d)
                            <option value="{{ $d->kode_dept }}">{{ strtoupper($d->nama_dept) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Jabatan -->
            <div class="space-y-1">
                <label for="kode_jabatan" style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 4px;">
                    Jabatan <span style="color: #ef4444; font-weight: bold;">*</span>
                </label>
                <div style="position: relative; display: flex; align-items: center;">
                    <span style="position: absolute; left: 10px; pointer-events: none; color: #94a3b8; display: flex; align-items: center;">
                        <i class="ti ti-badge" style="font-size: 16px;"></i>
                    </span>
                    <select name="kode_jabatan" 
                            id="kode_jabatan" 
                            style="width: 100%; height: 38px; padding-left: 36px; padding-right: 32px; font-size: 13px; font-weight: 500; color: #1e293b; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; cursor: pointer;">
                        <option value="">-- Pilih Jabatan --</option>
                        @foreach ($jabatan as $j)
                            <option value="{{ $j->kode_jabatan }}">{{ strtoupper($j->nama_jabatan) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Status Karyawan -->
            <div class="space-y-1">
                <label for="status_karyawan" style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 4px;">
                    Status Karyawan <span style="color: #ef4444; font-weight: bold;">*</span>
                </label>
                <div style="position: relative; display: flex; align-items: center;">
                    <span style="position: absolute; left: 10px; pointer-events: none; color: #94a3b8; display: flex; align-items: center;">
                        <i class="ti ti-user-star" style="font-size: 16px;"></i>
                    </span>
                    <select name="status_karyawan" 
                            id="status_karyawan" 
                            style="width: 100%; height: 38px; padding-left: 36px; padding-right: 32px; font-size: 13px; font-weight: 500; color: #1e293b; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; cursor: pointer;">
                        <option value="">-- Pilih Status --</option>
                        @foreach ($status_karyawan as $skk)
                            <option value="{{ $skk->kode_status_karyawan }}">{{ $skk->nama_status_karyawan }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Tanggal Masuk -->
            <div class="space-y-1">
                <label for="tanggal_masuk" style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 4px;">
                    Tanggal Masuk <span style="color: #ef4444; font-weight: bold;">*</span>
                </label>
                <div style="position: relative; display: flex; align-items: center;">
                    <span style="position: absolute; left: 10px; pointer-events: none; color: #94a3b8; display: flex; align-items: center;">
                        <i class="ti ti-calendar" style="font-size: 16px;"></i>
                    </span>
                    <input type="text" 
                           id="tanggal_masuk" 
                           name="tanggal_masuk" 
                           value="{{ date('Y-m-d') }}"
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
                <label for="rfid_uid" style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 4px;">
                    RFID Card UID
                </label>
                <div style="position: relative; display: flex; align-items: center;">
                    <span style="position: absolute; left: 10px; pointer-events: none; color: #94a3b8; display: flex; align-items: center;">
                        <i class="ti ti-id" style="font-size: 16px;"></i>
                    </span>
                    <input type="text" 
                           id="rfid_uid" 
                           name="rfid_uid" 
                           placeholder="Tempelkan kartu RFID atau isi UID" 
                           style="width: 100%; height: 38px; padding-left: 36px; padding-right: 12px; font-size: 13px; font-weight: 500; color: #1e293b; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; transition: border-color 0.15s ease;">
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
                <p style="font-size: 11px; color: #64748b; margin: 0;">Unggah berkas foto profil dan konfigurasi pajak karyawan</p>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; align-items: center;">
            <!-- Foto Upload with Preview -->
            <div class="space-y-2">
                <label style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 4px;">
                    Foto Karyawan
                </label>
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 54px; height: 54px; border-radius: 10px; border: 2px dashed #cbd5e1; background: #ffffff; display: flex; align-items: center; justify-content: center; overflow: hidden; flex-shrink: 0;" id="previewContainer">
                        <i class="ti ti-user" style="font-size: 24px; color: #cbd5e1;" id="previewPlaceholder"></i>
                        <img id="previewImage" src="" alt="Preview" style="width: 100%; height: 100%; object-fit: cover; display: none;">
                    </div>
                    <div style="flex: 1;">
                        <label for="fotoInput" style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; font-size: 12px; font-weight: 600; color: #334155; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; cursor: pointer; transition: all 0.15s ease;">
                            <i class="ti ti-upload" style="font-size: 14px; color: #059669;"></i>
                            <span id="fotoLabelText">Pilih Berkas Foto</span>
                        </label>
                        <input type="file" name="foto" id="fotoInput" accept="image/*" style="display: none;">
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
                        <input type="checkbox" name="hitung_pph21" id="hitung_pph21" value="1" checked style="opacity: 0; width: 0; height: 0;">
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
        <button type="submit" id="btnSimpanKaryawan" style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 18px; font-size: 12px; font-weight: 700; color: #ffffff; background: #059669; border: none; border-radius: 8px; cursor: pointer; box-shadow: 0 2px 6px rgba(5, 150, 105, 0.3); transition: background 0.15s ease;">
            <i class="ti ti-device-floppy" style="font-size: 16px;"></i>
            <span>Simpan Data Karyawan</span>
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
    $("#fotoInput").on("change", function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(evt) {
                $("#previewImage").attr("src", evt.target.result).show();
                $("#previewPlaceholder").hide();
                $("#fotoLabelText").text(file.name.length > 20 ? file.name.substring(0, 18) + '...' : file.name);
            };
            reader.readAsDataURL(file);
        } else {
            $("#previewImage").hide().attr("src", "");
            $("#previewPlaceholder").show();
            $("#fotoLabelText").text("Pilih Berkas Foto");
        }
    });

    // Validation Rules like siprenpas
    const validationRules = {
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

    function showError(element, message) {
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

    function clearError(element) {
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

    function validateSingleField(el) {
        const $el = $(el);
        const name = $el.attr('name') || $el.attr('id');
        const val = ($el.val() || '').toString().trim();

        const rule = validationRules[name];
        if (!rule) {
            clearError($el);
            return true;
        }

        if (rule.required && !val) {
            showError($el, rule.message);
            return false;
        }

        if (rule.maxLength && val.length > rule.maxLength) {
            showError($el, rule.maxMessage || `Maksimal ${rule.maxLength} karakter`);
            return false;
        }

        clearError($el);
        return true;
    }

    // Realtime validation trigger
    $("#formcreateKaryawan").on('input change blur', 'input, select, textarea', function(e) {
        const $this = $(this);
        const hasError = $this.css('border-color') === 'rgb(239, 68, 68)';
        const val = ($this.val() || '').toString().trim();
        
        if (e.type === 'blur' || val !== '' || hasError) {
            validateSingleField(this);
        }
    });

    // Submit validation
    $("#formcreateKaryawan").on('submit', function(e) {
        let isValid = true;
        let firstInvalidEl = null;

        Object.keys(validationRules).forEach(function(fieldName) {
            const $el = $("#formcreateKaryawan").find(`[name="${fieldName}"]`);
            if ($el.length > 0 && $el.is(':visible')) {
                const valid = validateSingleField($el);
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

        const $btn = $("#btnSimpanKaryawan");
        $btn.prop('disabled', true).html('<div style="width: 16px; height: 16px; border: 2px solid #ffffff; border-top-color: transparent; border-radius: 50%; animation: spin 0.8s linear infinite; display: inline-block;"></div><span>Menyimpan...</span>');
    });
});
</script>
