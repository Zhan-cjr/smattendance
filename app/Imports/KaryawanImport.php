<?php

namespace App\Imports;

use App\Models\Karyawan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class KaryawanImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        // Cek apakah baris memiliki data atau kosong
        if ($this->isRowEmpty($row)) {
            return null; // Skip baris kosong
        }

        // Generate nik otomatis (9 digit)
        $nik = $this->generateNik();

        return new Karyawan([
            'nik' => $nik,
            'nik_show' => $row['nik'], // NIK dari Excel masuk ke nik_show
            'no_ktp' => $row['no_ktp'],
            'nama_karyawan' => $row['nama_karyawan'],
            'tempat_lahir' => $row['tempat_lahir'],
            'tanggal_lahir' => $this->convertDate($row['tanggal_lahir']),
            'alamat' => $row['alamat'],
            'no_hp' => $row['no_hp'],
            'jenis_kelamin' => $row['jenis_kelamin'],
            'kode_status_kawin' => $row['kode_status_kawin'],
            'pendidikan_terakhir' => $row['pendidikan_terakhir'],
            'kode_cabang' => $row['kode_cabang'],
            'kode_dept' => $row['kode_dept'],
            'kode_jabatan' => $row['kode_jabatan'],
            'tanggal_masuk' => $this->convertDate($row['tanggal_masuk']),
            'status_karyawan' => $row['status_karyawan'],
            'kode_jadwal' => null,
            'pin' => null,
            'tanggal_nonaktif' => null,
            'tanggal_off_gaji' => null,
            'lock_location' => 1,
            'lock_jam_kerja' => 1,
            'status_aktif_karyawan' => $row['status_aktif_karyawan'] ?? 1,
            'password' => bcrypt('12345')
        ]);
    }

    /**
     * Konversi format tanggal dari Excel ke format Y-m-d
     * Menangani berbagai format tanggal yang mungkin dari Excel
     */
    private function convertDate($dateValue)
    {
        if (empty($dateValue)) {
            return null;
        }

        // Debug: Log format tanggal yang diterima
        Log::info('Date value received: ' . $dateValue . ' (Type: ' . gettype($dateValue) . ')');

        // Jika sudah dalam format Y-m-d, langsung return
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateValue)) {
            return $dateValue;
        }

        // Coba berbagai format yang mungkin dari Excel
        $formats = [
            'Y-m-d',      // 1993-10-01
            'd/m/Y',      // 01/10/1993
            'd-m-Y',      // 01-10-1993
            'm/d/Y',      // 10/01/1993
            'Y/m/d',      // 1993/10/01
            'd.m.Y',      // 01.10.1993
            'Y.m.d',      // 1993.10.01
        ];

        foreach ($formats as $format) {
            try {
                $carbon = Carbon::createFromFormat($format, $dateValue);
                if ($carbon) {
                    return $carbon->format('Y-m-d');
                }
            } catch (\Exception $e) {
                // Lanjut ke format berikutnya
                continue;
            }
        }

        // Jika semua format gagal, coba parse umum
        try {
            $carbon = Carbon::parse($dateValue);
            return $carbon->format('Y-m-d');
        } catch (\Exception $e) {
            // Coba handle Excel serial number (jika tanggal dikirim sebagai angka)
            if (is_numeric($dateValue)) {
                try {
                    // Excel serial number: 1 = 1900-01-01, 2 = 1900-01-02, dst
                    $excelDate = Carbon::createFromFormat('Y-m-d', '1900-01-01')->addDays($dateValue - 2);
                    return $excelDate->format('Y-m-d');
                } catch (\Exception $e2) {
                    Log::error('Failed to convert Excel serial date: ' . $dateValue);
                }
            }

            // Jika masih gagal, return null untuk menghindari error
            Log::error('Failed to convert date: ' . $dateValue . ' - ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Cek apakah baris kosong atau tidak
     * Baris dianggap kosong jika field wajib tidak ada atau kosong
     */
    private function isRowEmpty(array $row)
    {
        // Field wajib yang harus ada untuk dianggap baris tidak kosong
        $requiredFields = [
            'nik',
            'nama_karyawan',
            'no_ktp',
            'tempat_lahir',
            'tanggal_lahir',
            'alamat',
            'no_hp',
            'jenis_kelamin',
            'kode_status_kawin',
            'pendidikan_terakhir',
            'kode_cabang',
            'kode_dept',
            'kode_jabatan',
            'tanggal_masuk',
            'status_karyawan'
        ];

        // Cek apakah semua field wajib kosong atau tidak ada
        foreach ($requiredFields as $field) {
            if (!empty($row[$field]) && trim($row[$field]) !== '') {
                return false; // Ada data, baris tidak kosong
            }
        }

        return true; // Semua field kosong, baris kosong
    }

    /**
     * Generate NIK otomatis format YYMM + 5 digit urut per bulan
     * Sama dengan format di KaryawanController::store()
     */
    private function generateNik()
    {
        // Generate NIK format YYMM + 5 digit urut per bulan
        $tahun = date('y');
        $bulan = date('m');
        $prefix = $tahun . $bulan; // e.g., 2510

        $last = Karyawan::where('nik', 'like', $prefix . '%')
            ->orderBy('nik', 'desc')
            ->first();

        $lastNumber = 0;
        if ($last) {
            $lastNumber = (int)substr($last->nik, 4, 5);
        }
        $nextNumber = $lastNumber + 1;
        $nikAuto = $prefix . str_pad((string)$nextNumber, 5, '0', STR_PAD_LEFT);

        return $nikAuto;
    }

    public function rules(): array
    {
        return [
            'nik' => ['required', 'unique:karyawan,nik_show'], // Validasi nik_show untuk uniqueness
            'no_ktp' => 'required',
            'nama_karyawan' => 'required',
            'tempat_lahir' => 'required',
            'tanggal_lahir' => 'required', // Hapus validasi date, biarkan convertDate handle
            'alamat' => 'required',
            'no_hp' => 'required',
            'jenis_kelamin' => 'required|in:L,P',
            'kode_status_kawin' => 'required|exists:status_kawin,kode_status_kawin',
            'pendidikan_terakhir' => 'required',
            'kode_cabang' => 'required|exists:cabang,kode_cabang',
            'kode_dept' => 'required|exists:departemen,kode_dept',
            'kode_jabatan' => 'required|exists:jabatan,kode_jabatan',
            'tanggal_masuk' => 'required', // Hapus validasi date, biarkan convertDate handle
            'status_karyawan' => 'required',
            'status_aktif_karyawan' => 'nullable|in:0,1'
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nik.required' => 'NIK harus diisi',
            'nik.unique' => 'NIK sudah terdaftar di sistem',
            'no_ktp.required' => 'No KTP harus diisi',
            'nama_karyawan.required' => 'Nama karyawan harus diisi',
            'tempat_lahir.required' => 'Tempat lahir harus diisi',
            'tanggal_lahir.required' => 'Tanggal lahir harus diisi',
            'alamat.required' => 'Alamat harus diisi',
            'no_hp.required' => 'No HP harus diisi',
            'jenis_kelamin.required' => 'Jenis kelamin harus diisi',
            'jenis_kelamin.in' => 'Jenis kelamin harus L atau P',
            'kode_status_kawin.required' => 'Kode status kawin harus diisi',
            'kode_status_kawin.exists' => 'Kode status kawin tidak valid',
            'pendidikan_terakhir.required' => 'Pendidikan terakhir harus diisi',
            'kode_cabang.required' => 'Kode cabang harus diisi',
            'kode_cabang.exists' => 'Kode cabang tidak valid',
            'kode_dept.required' => 'Kode departemen harus diisi',
            'kode_dept.exists' => 'Kode departemen tidak valid',
            'kode_jabatan.required' => 'Kode jabatan harus diisi',
            'kode_jabatan.exists' => 'Kode jabatan tidak valid',
            'tanggal_masuk.required' => 'Tanggal masuk harus diisi',
            'status_karyawan.required' => 'Status karyawan harus diisi',
            'status_aktif_karyawan.in' => 'Status aktif harus 0 atau 1'
        ];
    }
}
