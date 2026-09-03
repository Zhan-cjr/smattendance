<?php

namespace Database\Seeders;

use App\Models\Departemen;
use App\Models\Jabatan;
use App\Models\KpiIndicator;
use App\Models\KpiIndicatorDetail;
use App\Models\KpiPeriod;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KpiDummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            // 0. Ensure Dependencies Exist
            $dept = Departemen::firstOrCreate(
                ['kode_dept' => 'IT'],
                ['nama_dept' => 'Information Technology']
            );

            $jabatan = Jabatan::firstOrCreate(
                ['kode_jabatan' => 'J01'],
                ['nama_jabatan' => 'Kepala IT']
            );

            // 1. Create KPI Periods
            $periods = [
                [
                    'nama_periode' => 'Januari 2026',
                    'start_date' => '2026-01-01',
                    'end_date' => '2026-01-31',
                    'is_active' => false,
                ],
                [
                    'nama_periode' => 'Februari 2026',
                    'start_date' => '2026-02-01',
                    'end_date' => '2026-02-28',
                    'is_active' => true,
                ],
            ];

            foreach ($periods as $periodData) {
                KpiPeriod::firstOrCreate(
                    ['start_date' => $periodData['start_date'], 'end_date' => $periodData['end_date']],
                    $periodData
                );
            }

            // 2. Create KPI Indicators Header (Based on Jabatan & Dept)
            $kpiIndicator = KpiIndicator::firstOrCreate(
                [
                    'kode_jabatan' => $jabatan->kode_jabatan,
                    'kode_dept' => $dept->kode_dept
                ]
            );

            // 3. Create KPI Indicator Details
            $indicators = [
                [
                    'nama_indikator' => 'Uptime Server & Network',
                    'deskripsi' => 'Menjaga ketersediaan server dan jaringan minimal 99.9%',
                    'satuan' => 'Persen',
                    'jenis_target' => 'max',
                    'bobot' => 20,
                    'target' => 99.90,
                    'mode' => 'manual',
                ],
                [
                    'nama_indikator' => 'Penyelesaian Tiket Support',
                    'deskripsi' => 'Menyelesaikan tiket bantuan teknis tepat waktu',
                    'satuan' => 'Tiket',
                    'jenis_target' => 'max',
                    'bobot' => 15,
                    'target' => 50,
                    'mode' => 'manual',
                ],
                [
                    'nama_indikator' => 'Pengembangan Fitur Baru',
                    'deskripsi' => 'Jumlah modul atau fitur baru yang dideploy ke production',
                    'satuan' => 'Modul',
                    'jenis_target' => 'max',
                    'bobot' => 15,
                    'target' => 2,
                    'mode' => 'manual',
                ],
                [
                    'nama_indikator' => 'Keamanan Sistem & Audit',
                    'deskripsi' => 'Jumlah kerentanan kritis yang ditemukan dalam audit bulanan',
                    'satuan' => 'Temuan',
                    'jenis_target' => 'min',
                    'bobot' => 20,
                    'target' => 0,
                    'mode' => 'manual',
                ],
                [
                    'nama_indikator' => 'Backup & Recovery Success Rate',
                    'deskripsi' => 'Persentase keberhasilan backup harian dan uji restore',
                    'satuan' => 'Persen',
                    'jenis_target' => 'max',
                    'bobot' => 15,
                    'target' => 100,
                    'mode' => 'manual',
                ],
                [
                    'nama_indikator' => 'Kepuasan User (User Satisfaction)',
                    'deskripsi' => 'Rata-rata nilai kepuasan user terhadap layanan IT (Skala 1-5)',
                    'satuan' => 'Skala',
                    'jenis_target' => 'max',
                    'bobot' => 15,
                    'target' => 4.5,
                    'mode' => 'manual',
                ],
            ];

            $indicatorNames = array_column($indicators, 'nama_indikator');
            KpiIndicatorDetail::where('kpi_indicator_id', $kpiIndicator->id)
                ->whereNotIn('nama_indikator', $indicatorNames)
                ->delete();

            foreach ($indicators as $detail) {
                // Update or create based on name and kpi_indicator_id
                KpiIndicatorDetail::updateOrCreate(
                    [
                        'kpi_indicator_id' => $kpiIndicator->id,
                        'nama_indikator' => $detail['nama_indikator']
                    ],
                    $detail
                );
            }
            
            DB::commit();
            $this->command->info('KPI Dummy Data seeded successfully!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Error seeding KPI Dummy Data: ' . $e->getMessage());
        }
    }
}
