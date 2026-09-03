<?php

namespace App\Exports;

use App\Models\Karyawan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class KaryawanExport implements FromCollection, WithHeadings, WithMapping
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Karyawan::query()
            ->select('karyawan.*', 'departemen.nama_dept', 'jabatan.nama_jabatan', 'cabang.nama_cabang')
            ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->join('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
            ->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->orderBy('nama_karyawan', 'asc');

        if (!empty($this->filters['nama_karyawan'])) {
            $query->where('nama_karyawan', 'like', '%' . $this->filters['nama_karyawan'] . '%');
        }

        if (!empty($this->filters['kode_cabang'])) {
            $query->where('karyawan.kode_cabang', $this->filters['kode_cabang']);
        }

        if (!empty($this->filters['kode_dept'])) {
            $query->where('karyawan.kode_dept', $this->filters['kode_dept']);
        }

        $user = auth()->user();
        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();

            if (!empty($userCabangs)) {
                $query->whereIn('karyawan.kode_cabang', $userCabangs);
            } else {
                $query->whereRaw('1 = 0');
            }

            if (!empty($userDepartemens)) {
                $query->whereIn('karyawan.kode_dept', $userDepartemens);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'NIK',
            'Nama Karyawan',
            'Departemen',
            'Jabatan',
            'Cabang',
            'No. HP',
            'Tanggal Masuk',
            'Status'
        ];
    }

    public function map($karyawan): array
    {
        return [
            "'" . $karyawan->nik,
            $karyawan->nama_karyawan,
            $karyawan->nama_dept,
            $karyawan->nama_jabatan,
            $karyawan->nama_cabang,
            $karyawan->no_hp,
            $karyawan->tanggal_masuk,
            $karyawan->status_aktif_karyawan == 1 ? 'Aktif' : 'Non Aktif'
        ];
    }
}
