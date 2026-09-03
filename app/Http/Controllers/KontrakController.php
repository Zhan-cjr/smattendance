<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Departemen;
use App\Models\Detailtunjangan;
use App\Models\Gajipokok;
use App\Models\Jabatan;
use App\Models\Jenistunjangan;
use App\Models\Karyawan;
use App\Models\Kontrak;
use App\Models\Tunjangan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class KontrakController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        // Check if user is Karyawan
        if ($user->hasRole('karyawan')) {
            $userkaryawan = \App\Models\Userkaryawan::where('id_user', $user->id)->first();
            $kontraks = Kontrak::where('nik', $userkaryawan->nik)
                ->orderByDesc('tanggal')
                ->get();
            
            return view('datamaster.kontrak.index_mobile', compact('kontraks'));
        }

        $query = Kontrak::select(
            'kontrak.*',
            'karyawan.nama_karyawan',
            'karyawan.nik_show',
            'jabatan.nama_jabatan',
            'cabang.nama_cabang',
            'departemen.nama_dept'
        )
            ->leftJoin('karyawan', 'kontrak.nik', '=', 'karyawan.nik')
            ->leftJoin('jabatan', 'kontrak.kode_jabatan', '=', 'jabatan.kode_jabatan')
            ->leftJoin('cabang', 'kontrak.kode_cabang', '=', 'cabang.kode_cabang')
            ->leftJoin('departemen', 'kontrak.kode_dept', '=', 'departemen.kode_dept');

        // Filter berdasarkan akses cabang dan departemen jika bukan super admin
        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();

            if (!empty($userCabangs)) {
                $query->whereIn('kontrak.kode_cabang', $userCabangs);
            } else {
                $query->whereRaw('1 = 0');
            }

            if (!empty($userDepartemens)) {
                $query->whereIn('kontrak.kode_dept', $userDepartemens);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        if ($request->nama_karyawan) {
            $query->where('karyawan.nama_karyawan', 'like', '%' . $request->nama_karyawan . '%');
        }

        if ($request->kode_cabang) {
            $query->where('kontrak.kode_cabang', $request->kode_cabang);
        }

        if ($request->kode_dept) {
            $query->where('kontrak.kode_dept', $request->kode_dept);
        }

        $kontraks = $query->orderByDesc('kontrak.tanggal')
            ->orderByDesc('kontrak.created_at')
            ->paginate(15)
            ->withQueryString();

        return view('datamaster.kontrak.index', [
            'kontraks' => $kontraks,
            'filterNama' => $request->nama_karyawan,
            'filterCabang' => $request->kode_cabang,
            'filterDept' => $request->kode_dept,
            'cabangs' => $user->getCabang(),
            'departemens' => $user->getDepartemen(),
        ]);
    }

    public function create()
    {
        return view('datamaster.kontrak.create', $this->formDependencies());
    }

    public function store(Request $request)
    {
        $data = $this->validateRequest($request);
        $data['no_kontrak'] = $this->generateNomorKontrak();
        $data['status_kontrak'] = '1';

        DB::beginTransaction();
        try {
            // Buat gaji pokok baru jika ada nominal_gaji
            if (!empty($request->nominal_gaji)) {
                $jumlah_gaji = toNumber($request->nominal_gaji);
                if ($jumlah_gaji > 0) {
                    $kode_gaji = $this->generateKodeGaji($data['tanggal']);
                    Gajipokok::create([
                        'kode_gaji' => $kode_gaji,
                        'nik' => $data['nik'],
                        'jumlah' => $jumlah_gaji,
                        'tanggal_berlaku' => $data['tanggal']
                    ]);
                    $data['kode_gaji'] = $kode_gaji;
                }
            }

            // Buat tunjangan baru jika ada nominal_tunjangan_detail
            $hasTunjangan = false;
            if (!empty($request->nominal_tunjangan_detail) && !empty($request->kode_jenis_tunjangan)) {
                foreach ($request->nominal_tunjangan_detail as $index => $nominal) {
                    if (!empty($nominal) && toNumber($nominal) > 0) {
                        $hasTunjangan = true;
                        break;
                    }
                }

                if ($hasTunjangan) {
                    $kode_tunjangan = $this->generateKodeTunjangan($data['tanggal']);
                    Tunjangan::create([
                        'kode_tunjangan' => $kode_tunjangan,
                        'nik' => $data['nik'],
                        'tanggal_berlaku' => $data['tanggal']
                    ]);

                    // Simpan detail tunjangan
                    foreach ($request->kode_jenis_tunjangan as $index => $kode_jenis) {
                        $nominal = $request->nominal_tunjangan_detail[$index] ?? 0;
                        if (!empty($nominal) && toNumber($nominal) > 0) {
                            Detailtunjangan::create([
                                'kode_tunjangan' => $kode_tunjangan,
                                'kode_jenis_tunjangan' => $kode_jenis,
                                'jumlah' => toNumber($nominal),
                            ]);
                        }
                    }
                    $data['kode_tunjangan'] = $kode_tunjangan;
                }
            }

            // Nonaktifkan kontrak sebelumnya untuk karyawan yang sama
            Kontrak::where('nik', $data['nik'])
                ->where('status_kontrak', '1')
                ->update(['status_kontrak' => '0']);

            Kontrak::create($data);
            DB::commit();
            return Redirect::back()->with(messageSuccess('Kontrak berhasil disimpan.'));
        } catch (\Throwable $th) {
            DB::rollBack();
            return Redirect::back()->with(messageError($th->getMessage()));
        }
    }

    public function edit(string $encryptedId)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $id = Crypt::decrypt($encryptedId);
        $kontrak = Kontrak::findOrFail($id);

        // Cek jika kontrak sudah tidak aktif
        if ($kontrak->status_kontrak == '0') {
            return Redirect::back()->with(messageError('Kontrak sudah nonaktif tidak dapat diedit.'));
        }

        // Cek akses jika bukan super admin
        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();

            if (!in_array($kontrak->kode_cabang, $userCabangs) || !in_array($kontrak->kode_dept, $userDepartemens)) {
                abort(403, 'Anda tidak memiliki akses ke kontrak ini.');
            }
        }

        $tunjanganValues = [];
        if (!empty($kontrak->kode_tunjangan)) {
            $tunjanganValues = Detailtunjangan::where('kode_tunjangan', $kontrak->kode_tunjangan)
                ->pluck('jumlah', 'kode_jenis_tunjangan')
                ->toArray();
        }

        return view('datamaster.kontrak.edit', array_merge(
            [
                'kontrak' => $kontrak,
                'tunjanganValues' => $tunjanganValues,
            ],
            $this->formDependencies()
        ));
    }

    public function update(Request $request, string $encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        $kontrak = Kontrak::findOrFail($id);

        if ($kontrak->status_kontrak == '0') {
            return Redirect::back()->with(messageError('Kontrak sudah nonaktif tidak dapat diedit.'));
        }

        $data = $this->validateRequest($request, $kontrak->id);

        try {
            $kontrak->update($data);
            return Redirect::back()->with(messageSuccess('Kontrak berhasil diperbarui.'));
        } catch (\Throwable $th) {
            return Redirect::back()->with(messageError($th->getMessage()));
        }
    }

    public function destroy(string $encryptedId)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $id = Crypt::decrypt($encryptedId);
        $kontrak = Kontrak::findOrFail($id);

        if ($kontrak->status_kontrak == '0') {
            return Redirect::back()->with(messageError('Kontrak sudah nonaktif tidak dapat dihapus.'));
        }

        // Cek akses jika bukan super admin
        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();

            if (!in_array($kontrak->kode_cabang, $userCabangs) || !in_array($kontrak->kode_dept, $userDepartemens)) {
                abort(403, 'Anda tidak memiliki akses ke kontrak ini.');
            }
        }

        try {
            $kontrak->delete();
            return Redirect::back()->with(messageSuccess('Kontrak berhasil dihapus.'));
        } catch (\Throwable $th) {
            return Redirect::back()->with(messageError($th->getMessage()));
        }
    }

    public function show(string $encryptedId)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $id = Crypt::decrypt($encryptedId);

        $kontrak = Kontrak::select(
            'kontrak.*',
            'karyawan.nama_karyawan',
            'karyawan.nik_show',
            'karyawan.tempat_lahir',
            'karyawan.tanggal_lahir',
            'karyawan.jenis_kelamin',
            'karyawan.alamat',
            'karyawan.no_ktp',
            'jabatan.nama_jabatan',
            'cabang.nama_cabang',
            'departemen.nama_dept',
            'gaji.jumlah as jumlah_gaji'
        )
            ->leftJoin('karyawan', 'kontrak.nik', '=', 'karyawan.nik')
            ->leftJoin('jabatan', 'kontrak.kode_jabatan', '=', 'jabatan.kode_jabatan')
            ->leftJoin('cabang', 'kontrak.kode_cabang', '=', 'cabang.kode_cabang')
            ->leftJoin('departemen', 'kontrak.kode_dept', '=', 'departemen.kode_dept')
            ->leftJoin('karyawan_gaji_pokok as gaji', 'kontrak.kode_gaji', '=', 'gaji.kode_gaji')
            ->where('kontrak.id', $id)
            ->firstOrFail();

        // Cek akses
        if ($user->hasRole('karyawan')) {
            $userkaryawan = \App\Models\Userkaryawan::where('id_user', $user->id)->first();
            if (!$userkaryawan || $kontrak->nik !== $userkaryawan->nik) {
                abort(403, 'Anda tidak memiliki akses ke kontrak ini.');
            }
        } elseif (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();

            if (!in_array($kontrak->kode_cabang, $userCabangs) || !in_array($kontrak->kode_dept, $userDepartemens)) {
                abort(403, 'Anda tidak memiliki akses ke kontrak ini.');
            }
        }

        $tunjanganItems = Detailtunjangan::select(
            'jenis_tunjangan.jenis_tunjangan as jenis',
            'karyawan_tunjangan_detail.jumlah'
        )
            ->join('jenis_tunjangan', 'karyawan_tunjangan_detail.kode_jenis_tunjangan', '=', 'jenis_tunjangan.kode_jenis_tunjangan')
            ->where('karyawan_tunjangan_detail.kode_tunjangan', $kontrak->kode_tunjangan)
            ->get();
            
        $pengaturan = \App\Models\Pengaturanumum::first();

        if ($user->hasRole('karyawan')) {
            return view('datamaster.kontrak.show_mobile', compact('kontrak', 'tunjanganItems', 'pengaturan'));
        }
        
        // For now, admin also uses the same view or we can create a desktop view later
        // return view('datamaster.kontrak.show', compact('kontrak', 'tunjanganItems'));
        return view('datamaster.kontrak.show_mobile', compact('kontrak', 'tunjanganItems', 'pengaturan'));
    }

    public function template()
    {
        $template = \App\Models\KonfigurasiDokumen::where('kode_dokumen', 'PKWT')->first();
        if (!$template) {
            // Default template if not exists
            $konten = view('datamaster.kontrak.default_template')->render();
            // Create initial record
            $template = \App\Models\KonfigurasiDokumen::create([
                'kode_dokumen' => 'PKWT',
                'nama_dokumen' => 'Perjanjian Kerja Waktu Tertentu',
                'konten' => $konten
            ]);
        }
        return view('datamaster.kontrak.template', compact('template'));
    }

    public function updateTemplate(Request $request)
    {
        // Handle Reset
        if ($request->has('reset') && $request->reset == 'true') {
            $konten = view('datamaster.kontrak.default_template')->render();
            \App\Models\KonfigurasiDokumen::updateOrCreate(
                ['kode_dokumen' => 'PKWT'],
                [
                    'nama_dokumen' => 'Perjanjian Kerja Waktu Tertentu',
                    'konten' => $konten
                ]
            );
            return Redirect::back()->with(messageSuccess('Template kontrak berhasil direset ke default.'));
        }

        $request->validate([
            'konten' => 'required'
        ]);

        \App\Models\KonfigurasiDokumen::updateOrCreate(
            ['kode_dokumen' => 'PKWT'],
            [
                'nama_dokumen' => 'Perjanjian Kerja Waktu Tertentu',
                'konten' => $request->konten
            ]
        );

        return Redirect::back()->with(messageSuccess('Template kontrak berhasil diperbarui.'));
    }

    public function print(string $encryptedId)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $id = Crypt::decrypt($encryptedId);

        $kontrak = Kontrak::select(
            'kontrak.*',
            'karyawan.nama_karyawan',
            'karyawan.nik_show',
            'karyawan.tempat_lahir',
            'karyawan.tanggal_lahir',
            'karyawan.jenis_kelamin',
            'karyawan.alamat',
            'karyawan.no_ktp',
            'jabatan.nama_jabatan',
            'cabang.nama_cabang',
            'departemen.nama_dept',
            'gaji.jumlah as jumlah_gaji'
        )
            ->leftJoin('karyawan', 'kontrak.nik', '=', 'karyawan.nik')
            ->leftJoin('jabatan', 'kontrak.kode_jabatan', '=', 'jabatan.kode_jabatan')
            ->leftJoin('cabang', 'kontrak.kode_cabang', '=', 'cabang.kode_cabang')
            ->leftJoin('departemen', 'kontrak.kode_dept', '=', 'departemen.kode_dept')
            ->leftJoin('karyawan_gaji_pokok as gaji', 'kontrak.kode_gaji', '=', 'gaji.kode_gaji')
            ->where('kontrak.id', $id)
            ->firstOrFail();

        // Cek akses jika bukan super admin
        if (!$user->isSuperAdmin()) {
            if (!$user->hasRole('karyawan')) {
                $userCabangs = $user->getCabangCodes();
                $userDepartemens = $user->getDepartemenCodes();

                if (!in_array($kontrak->kode_cabang, $userCabangs) || !in_array($kontrak->kode_dept, $userDepartemens)) {
                    abort(403, 'Anda tidak memiliki akses ke kontrak ini.');
                }
            }
        }

        $tunjanganItems = Detailtunjangan::select(
            'jenis_tunjangan.jenis_tunjangan as jenis',
            'karyawan_tunjangan_detail.jumlah'
        )
            ->join('jenis_tunjangan', 'karyawan_tunjangan_detail.kode_jenis_tunjangan', '=', 'jenis_tunjangan.kode_jenis_tunjangan')
            ->where('karyawan_tunjangan_detail.kode_tunjangan', $kontrak->kode_tunjangan)
            ->get();

        $setting = \App\Models\Pengaturanumum::first();
        
        // Get Template
        $template = \App\Models\KonfigurasiDokumen::where('kode_dokumen', 'PKWT')->first();
        if (!$template) {
             // Fallback to default if not configured
             $konten = view('datamaster.kontrak.default_template')->render();
        } else {
            $konten = $template->konten;
        }


        // Prepare Placeholders
        $placeholders = [
            '{{no_kontrak}}' => $kontrak->no_kontrak,
            '{{hari_ini}}' => now()->isoFormat('dddd'),
            '{{tanggal_hari_ini}}' => now()->isoFormat('D MMMM Y'),
            '{{nama_hrd}}' => $setting->nama_hrd ?? 'Pihak Pertama',
            '{{nama_perusahaan}}' => $setting->nama_perusahaan ?? 'Perusahaan',
            '{{alamat_perusahaan}}' => $setting->alamat ?? 'Lokasi Perusahaan',
            '{{nama_karyawan}}' => $kontrak->nama_karyawan,
            '{{tempat_lahir}}' => $kontrak->tempat_lahir ?? '-',
            '{{tanggal_lahir}}' => $kontrak->tanggal_lahir ? Carbon::parse($kontrak->tanggal_lahir)->format('d-m-Y') : '-',
            '{{jenis_kelamin}}' => $kontrak->jenis_kelamin,
            '{{alamat_karyawan}}' => $kontrak->alamat ?? '-',
            '{{no_ktp}}' => $kontrak->no_ktp ?? '-',
            '{{jabatan}}' => $kontrak->nama_jabatan,
            '{{cabang}}' => $kontrak->nama_cabang,
            '{{tanggal_mulai}}' => $kontrak->dari ? Carbon::parse($kontrak->dari)->isoFormat('D MMMM Y') : '-',
            '{{tanggal_selesai}}' => $kontrak->sampai ? Carbon::parse($kontrak->sampai)->isoFormat('D MMMM Y') : '-',
            '{{gaji_pokok}}' => 'Rp ' . number_format($kontrak->jumlah_gaji ?? 0, 0, ',', '.'),
        ];
        
        // Replace Tunjangan Loop
        $tunjanganHtml = '<table width="100%" style="border-collapse:collapse; margin:0; padding:0;">';
        if ($tunjanganItems->isNotEmpty()) {
            foreach ($tunjanganItems as $item) {
                $tunjanganHtml .= '<tr><td class="label" style="width:55%; padding: 6px 10px; border:none;">'.$item->jenis.'</td><td class="value" style="text-align:right; padding: 6px 10px; border:none;">Rp '.number_format($item->jumlah ?? 0, 0, ',', '.').'</td></tr>';
            }
        } else {
             $tunjanganHtml .= '<tr><td class="label" style="width:55%; padding: 6px 10px; border:none;">Transport</td><td class="value" style="text-align:right; padding: 6px 10px; border:none;">Rp 0</td></tr>';
             $tunjanganHtml .= '<tr><td class="label" style="width:55%; padding: 6px 10px; border:none;">Tunjangan Shift Malam</td><td class="value" style="text-align:right; padding: 6px 10px; border:none;">Rp 0</td></tr>';
             $tunjanganHtml .= '<tr><td class="label" style="width:55%; padding: 6px 10px; border:none;">Uang Makan</td><td class="value" style="text-align:right; padding: 6px 10px; border:none;">Rp 0</td></tr>';
        }
        $tunjanganHtml .= '</table>';
        $placeholders['{{tabel_tunjangan}}'] = $tunjanganHtml;


        // Perform Replacement
        foreach ($placeholders as $key => $value) {
            $konten = str_replace($key, $value, $konten);
        }

        $pdf = Pdf::loadView('datamaster.kontrak.print_dynamic', [
            'konten' => $konten,
            'kontrak' => $kontrak // Pass for title etc
        ])->setPaper('legal', 'portrait');

        $filename = 'kontrak-' . $kontrak->nik . '-' . $kontrak->no_kontrak . '.pdf';

        return $pdf->stream($filename);
    }

    protected function formDependencies(): array
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $karyawanQuery = Karyawan::select('nik', 'nama_karyawan', 'kode_dept', 'kode_cabang', 'kode_jabatan');

        // Filter karyawan berdasarkan akses jika bukan super admin
        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();

            if (!empty($userCabangs)) {
                $karyawanQuery->whereIn('kode_cabang', $userCabangs);
            } else {
                $karyawanQuery->whereRaw('1 = 0');
            }

            if (!empty($userDepartemens)) {
                $karyawanQuery->whereIn('kode_dept', $userDepartemens);
            } else {
                $karyawanQuery->whereRaw('1 = 0');
            }
        }

        return [
            'karyawans' => $karyawanQuery->orderBy('nama_karyawan')->get(),
            'jabatans' => Jabatan::select('kode_jabatan', 'nama_jabatan')->orderBy('nama_jabatan')->get(),
            'cabangs' => $user->getCabang(),
            'departemens' => $user->getDepartemen(),
            'gajis' => Gajipokok::select('kode_gaji', 'nik', 'jumlah', 'tanggal_berlaku')
                ->orderByDesc('tanggal_berlaku')
                ->limit(100)
                ->get(),
            'tunjangans' => Tunjangan::select('kode_tunjangan', 'nik', 'tanggal_berlaku')
                ->orderByDesc('tanggal_berlaku')
                ->limit(100)
                ->get(),
            'jenisTunjangans' => Jenistunjangan::orderBy('kode_jenis_tunjangan')->get(),
        ];
    }

    protected function validateRequest(Request $request, ?int $ignoreId = null): array
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $rules = [
            'nik' => ['required', 'exists:karyawan,nik'],
            'tanggal' => ['required', 'date'],
            'jenis_kontrak' => ['required', 'string', 'in:K,T'],
            'dari' => ['nullable', 'required_if:jenis_kontrak,K', 'date'],
            'sampai' => ['nullable', 'required_if:jenis_kontrak,K', 'date', 'after_or_equal:dari'],
            'kode_jabatan' => ['required', 'exists:jabatan,kode_jabatan'],
            'kode_cabang' => ['required', 'exists:cabang,kode_cabang'],
            'kode_dept' => ['required', 'exists:departemen,kode_dept'],
            'status_kontrak' => ['required', 'string', 'max:20'],
            'nominal_gaji' => ['nullable', 'string'],
            'nominal_tunjangan_detail' => ['nullable', 'array'],
            'kode_jenis_tunjangan' => ['nullable', 'array'],
        ];

        // Validasi akses cabang dan departemen jika bukan super admin
        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();

            $rules['kode_cabang'] = array_merge($rules['kode_cabang'], [Rule::in($userCabangs)]);
            $rules['kode_dept'] = array_merge($rules['kode_dept'], [Rule::in($userDepartemens)]);
        }

        // Validasi kode_gaji dan kode_tunjangan hanya jika ada nilainya (untuk update)
        if ($ignoreId) {
            $uniqueRule = 'unique:kontrak,no_kontrak,' . $ignoreId;
            $rules['no_kontrak'] = ['required', 'string', 'max:100', $uniqueRule];
            $rules['kode_gaji'] = ['nullable', 'exists:karyawan_gaji_pokok,kode_gaji'];
            $rules['kode_tunjangan'] = ['nullable', 'exists:karyawan_tunjangan,kode_tunjangan'];
        }

        $messages = [
            'kode_cabang.in' => 'Anda tidak memiliki akses ke cabang yang dipilih.',
            'kode_dept.in' => 'Anda tidak memiliki akses ke departemen yang dipilih.',
        ];

        $validated = $request->validate($rules, $messages);

        // Hapus field yang tidak ada di tabel kontrak
        // Field ini hanya digunakan untuk membuat gaji pokok dan tunjangan baru
        unset($validated['nominal_gaji']);
        unset($validated['nominal_tunjangan_detail']);
        unset($validated['kode_jenis_tunjangan']);

        return $validated;
    }

    protected function generateNomorKontrak(): string
    {
        $prefix = 'K' . now()->format('my');
        $sequenceLength = 8 - strlen($prefix);

        $lastNumber = Kontrak::where('no_kontrak', 'like', $prefix . '%')
            ->orderByDesc('no_kontrak')
            ->value('no_kontrak');

        $lastSequence = 0;
        if ($lastNumber) {
            $lastSequence = (int) substr($lastNumber, -$sequenceLength);
        }

        $nextSequence = str_pad((string) ($lastSequence + 1), $sequenceLength, '0', STR_PAD_LEFT);

        return $prefix . $nextSequence;
    }

    protected function generateKodeGaji(string $tanggal): string
    {
        $tahun_gaji = date('Y', strtotime($tanggal));
        $last_gaji = Gajipokok::orderBy('kode_gaji', 'desc')
            ->whereRaw('YEAR(tanggal_berlaku) = ' . $tahun_gaji)
            ->first();
        $last_kode_gaji = $last_gaji != null ? $last_gaji->kode_gaji : '';
        return buatkode($last_kode_gaji, "G" . substr($tahun_gaji, 2, 2), 4);
    }

    protected function generateKodeTunjangan(string $tanggal): string
    {
        $tahun_gaji = date('Y', strtotime($tanggal));
        $last_tunjangan = Tunjangan::orderBy('kode_tunjangan', 'desc')
            ->whereRaw('YEAR(tanggal_berlaku) = ' . $tahun_gaji)
            ->first();
        $last_kode_tunjangan = $last_tunjangan != null ? $last_tunjangan->kode_tunjangan : '';
        return buatkode($last_kode_tunjangan, "T" . substr($tahun_gaji, 2, 2), 4);
    }

    public function getEmployeeLatest(string $nik)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $karyawan = Karyawan::where('nik', $nik)->first();
        if (!$karyawan) {
            return response()->json(['message' => 'Karyawan tidak ditemukan'], 404);
        }

        // Cek akses jika bukan super admin
        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();

            if (!in_array($karyawan->kode_cabang, $userCabangs) || !in_array($karyawan->kode_dept, $userDepartemens)) {
                return response()->json(['message' => 'Anda tidak memiliki akses ke karyawan ini'], 403);
            }
        }

        // Ambil gaji terakhir berdasarkan tanggal berlaku (terbaru)
        $latestSalary = Gajipokok::where('nik', $nik)
            ->orderByDesc('tanggal_berlaku')
            ->orderByDesc('created_at')
            ->first();
        // Ambil tunjangan terakhir berdasarkan tanggal berlaku (terbaru)
        $latestAllowance = Tunjangan::where('nik', $nik)
            ->orderByDesc('tanggal_berlaku')
            ->orderByDesc('created_at')
            ->first();
        $allowanceTotal = null;
        $allowanceDetails = [];

        if ($latestAllowance) {
            // Ambil total tunjangan
            $allowanceTotal = Detailtunjangan::where('kode_tunjangan', $latestAllowance->kode_tunjangan)->sum('jumlah');

            // Ambil detail tunjangan per jenis
            $details = Detailtunjangan::where('kode_tunjangan', $latestAllowance->kode_tunjangan)
                ->get();

            foreach ($details as $detail) {
                $allowanceDetails[] = [
                    'kode_jenis_tunjangan' => $detail->kode_jenis_tunjangan,
                    'jumlah' => (int) $detail->jumlah,
                ];
            }
        }

        $formatDate = function ($date) {
            if (!$date) {
                return null;
            }
            return Carbon::parse($date)->format('d/m/Y');
        };

        return response()->json([
            'salary' => $latestSalary ? [
                'kode' => $latestSalary->kode_gaji,
                'jumlah' => (int) $latestSalary->jumlah,
                'tanggal' => $formatDate($latestSalary->tanggal_berlaku),
            ] : null,
            'allowance' => $latestAllowance ? [
                'kode' => $latestAllowance->kode_tunjangan,
                'total' => (int) $allowanceTotal,
                'tanggal' => $formatDate($latestAllowance->tanggal_berlaku),
                'details' => $allowanceDetails,
            ] : null,
        ]);
    }
}
