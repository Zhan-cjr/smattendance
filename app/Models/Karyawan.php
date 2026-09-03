<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Karyawan extends Model
{
    use HasFactory;
    protected $table = "karyawan";
    protected $primaryKey = "nik";
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];
    protected $casts = [
        'kode_cabang_array' => 'array',
    ];

    function getRekapstatuskaryawan($request = null)
    {
        // Get Total Active Employee Count
        $queryAktif = Karyawan::query();
        if (!empty($request->kode_cabang)) {
            $queryAktif->where('karyawan.kode_cabang', $request->kode_cabang);
        }
        if (!empty($request->kode_dept)) {
            $queryAktif->where('karyawan.kode_dept', $request->kode_dept);
        }
        $jml_aktif = $queryAktif->where('status_aktif_karyawan', '1')->count();

        // Get Dynamic Status Recapitulation
        $queryRekap = DB::table('status_karyawan')
            ->leftJoin('karyawan', function($join) use ($request) {
                $join->on('status_karyawan.kode_status_karyawan', '=', 'karyawan.status_karyawan');
                if (!empty($request->kode_cabang)) {
                    $join->where('karyawan.kode_cabang', '=', $request->kode_cabang);
                }
                if (!empty($request->kode_dept)) {
                    $join->where('karyawan.kode_dept', '=', $request->kode_dept);
                }
            })
            ->select('status_karyawan.nama_status_karyawan', DB::raw('count(karyawan.nik) as total'))
            ->groupBy('status_karyawan.nama_status_karyawan', 'status_karyawan.kode_status_karyawan')
            ->orderBy('status_karyawan.kode_status_karyawan')
            ->get();

        return (object) [
            'jml_aktif' => $jml_aktif,
            'rekap_status' => $queryRekap
        ];
    }

    // Relasi dengan Facerecognition
    public function facerecognition()
    {
        return $this->hasMany(Facerecognition::class, 'nik', 'nik');
    }

    // Relasi dengan LiveLocation untuk tracking lokasi real-time
    public function liveLocations()
    {
        return $this->hasMany(LiveLocation::class, 'nik', 'nik');
    }

    // Get latest live location
    public function latestLiveLocation()
    {
        return $this->hasOne(LiveLocation::class, 'nik', 'nik')->latest('tracked_at');
    }



    public function getRekapkontrak($kategori, $userCabangs = null, $userDepartemens = null)
    {
        $bulanini = date("m");
        $tahunini = date("Y");
        $start_date_bulanini = $tahunini . "-" . $bulanini . "-01";
        $end_date_bulanini = date("Y-m-t", strtotime($start_date_bulanini));
        //Jika Bulan + 1 Lebih dari 12 Maka Bulan + 1 - 12 dan Tahun + 1 Jika Tidak Maka Bulan Depan = Bulan + 1
        $bulandepan = date("m") + 1 > 12 ? (date("m") + 1) - 12 : date("m") + 1;
        $tahunbulandepan = date("m") + 1 > 12 ? $tahunini + 1 : $tahunini;
        $start_date_bulandepan = $tahunbulandepan . "-" . $bulandepan . "-01";
        $end_date_bulandepan = date("Y-m-t", strtotime($start_date_bulandepan));

        //Jika Bulan + 2 Lebih dari 12 Maka Bulan + 2 - 12 dan Tahun + 1 Jika Tidak Maka Bulan Depan = Bulan + 2
        //Sampel Jika Bulan = Desember (12) Maka Dua bulan adalah Februari (2) (12+2-12);
        $duabulan = date("m") + 2 > 12 ? (date("m") + 2) - 12 : date("m") + 2;
        $tahunduabulan = date("m") + 2 > 12 ? $tahunini + 1 : $tahunini;
        $start_date_duabulan = $tahunduabulan . "-" . $duabulan . "-01";
        $end_date_duabulan = date("Y-m-t", strtotime($start_date_duabulan));
        $query = Kontrak::query();
        $query->select('kontrak.no_kontrak', 'kontrak.nik', 'kontrak.sampai', 'karyawan.nama_karyawan', 'nama_jabatan', 'karyawan.kode_dept', 'karyawan.kode_cabang', 'nama_cabang');
        $query->join('karyawan', 'kontrak.nik', '=', 'karyawan.nik');
        $query->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang');
        $query->join('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan');
        
        // Filter berdasarkan akses cabang dan departemen jika diberikan
        if (!empty($userCabangs) && is_array($userCabangs)) {
            $query->whereIn('karyawan.kode_cabang', $userCabangs);
        }
        
        if (!empty($userDepartemens) && is_array($userDepartemens)) {
            $query->whereIn('karyawan.kode_dept', $userDepartemens);
        }
        
        if ($kategori == 0) { // Lewat Jatuh Tempo
            $query->where('sampai', '<', $start_date_bulanini);
        } else if ($kategori == 1) { // Jatuh Tempo Bulan Ini
            $query->whereBetween('sampai', [$start_date_bulanini, $end_date_bulanini]);
        } else if ($kategori == 2) { // Jatuh Tempo Bulan Depan
            $query->whereBetween('sampai', [$start_date_bulandepan, $end_date_bulandepan]);
        } else if ($kategori == 3) { // Jatuh Tempo Dua Bulan
            $query->whereBetween('sampai', [$start_date_duabulan, $end_date_duabulan]);
        }
        $query->where('status_aktif_karyawan', 1);
        $query->where('status_karyawan', 'K');
        $query->where('status_kontrak', 1);
        $query->orderBy('kontrak.sampai');
        $query->orderBy('karyawan.nama_karyawan');
        return $query->get();
    }

    // Relasi dengan GrupDetail
    // public function grupDetail()
    // {
    //     return $this->hasMany(GrupDetail::class, 'nik', 'nik');
    // }

    // Relasi ke Grup melalui GrupDetail
    // public function grup()
    // {
    //     return $this->hasManyThrough(Grup::class, GrupDetail::class, 'nik', 'kode_grup', 'nik', 'kode_grup');
    // }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'kode_jabatan', 'kode_jabatan');
    }

    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'kode_dept', 'kode_dept');
    }

    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'kode_cabang', 'kode_cabang');
    }
}
