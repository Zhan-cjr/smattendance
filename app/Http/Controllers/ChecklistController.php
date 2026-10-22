<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\ChecklistCheckpoint;
use App\Models\ChecklistLaporan;
use App\Models\ChecklistLaporanCollage;
use App\Models\ChecklistLaporanDetail;
use App\Models\Karyawan;
use App\Models\Userkaryawan;
use App\Services\ChecklistImageService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ChecklistController extends Controller
{
    protected ChecklistImageService $imageService;

    public function __construct(ChecklistImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Get logged-in employee record
     */
    protected function getKaryawan()
    {
        $user = Auth::user();
        if (!$user) return null;

        $userKaryawan = Userkaryawan::where('id_user', $user->id)->first();
        if ($userKaryawan) {
            return Karyawan::where('nik', $userKaryawan->nik)
                ->leftJoin('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
                ->leftJoin('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
                ->select('karyawan.*', 'cabang.nama_cabang', 'cabang.lokasi_cabang', 'cabang.alamat_cabang', 'jabatan.nama_jabatan')
                ->first();
        }

        // Fallback for admin or testing: pick first employee or create a mock
        $karyawan = Karyawan::leftJoin('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->leftJoin('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
            ->select('karyawan.*', 'cabang.nama_cabang', 'cabang.lokasi_cabang', 'cabang.alamat_cabang', 'jabatan.nama_jabatan')
            ->first();

        return $karyawan;
    }

    /**
     * Main Landing Page for Opening & Closing Checklist
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if ($user && !$user->hasRole('karyawan')) {
            return redirect()->route('admin.checklist.monitoring.index');
        }

        $karyawan = $this->getKaryawan();
        if (!$karyawan) {
            return redirect()->route('dashboard.index')->with('error', 'Data karyawan tidak ditemukan.');
        }

        $today = date('Y-m-d');
        $kodeCabang = $karyawan->kode_cabang;

        // Today's status for this branch
        $openingToday = ChecklistLaporan::where('kode_cabang', $kodeCabang)
            ->where('tipe', 'opening')
            ->where('tanggal', $today)
            ->with(['karyawan', 'collages'])
            ->latest()
            ->first();

        $closingToday = ChecklistLaporan::where('kode_cabang', $kodeCabang)
            ->where('tipe', 'closing')
            ->where('tanggal', $today)
            ->with(['karyawan', 'collages'])
            ->latest()
            ->first();

        // History of reports
        $recentReports = ChecklistLaporan::where('kode_cabang', $kodeCabang)
            ->with(['karyawan', 'collages'])
            ->latest('tanggal')
            ->latest('jam')
            ->paginate(10);

        return view('checklist.index', compact(
            'karyawan',
            'openingToday',
            'closingToday',
            'recentReports',
            'today'
        ));
    }

    /**
     * Interactive Form to Fill Opening / Closing Checklist
     */
    public function create(string $tipe)
    {
        if (!in_array($tipe, ['opening', 'closing'])) {
            return redirect()->route('checklist.index')->with('error', 'Tipe checklist tidak valid.');
        }

        $karyawan = $this->getKaryawan();
        if (!$karyawan) {
            return redirect()->route('checklist.index')->with('error', 'Data karyawan tidak ditemukan.');
        }

        $kodeCabang = $karyawan->kode_cabang;

        // Fetch checkpoints dynamic per branch and type
        $checkpoints = ChecklistCheckpoint::forBranchAndType($kodeCabang, $tipe)->get();

        if ($checkpoints->isEmpty()) {
            // Fallback: get all active for this type
            $checkpoints = ChecklistCheckpoint::where('is_active', true)
                ->where(function ($q) use ($tipe) {
                    $q->where('tipe', $tipe)->orWhere('tipe', 'both');
                })
                ->orderBy('urutan', 'asc')
                ->get();
        }

        return view('checklist.create', compact('tipe', 'karyawan', 'checkpoints'));
    }

    /**
     * Store submitted checklist with live camera photos, watermark & collages
     */
    public function store(Request $request)
    {
        $request->validate([
            'tipe' => 'required|in:opening,closing',
            'disclaimer_agreed' => 'required',
            'photos' => 'required|array|min:1',
            'latitude' => 'nullable|string',
            'longitude' => 'nullable|string',
            'lokasi_nama' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        $karyawan = $this->getKaryawan();
        if (!$karyawan) {
            return response()->json([
                'success' => false,
                'message' => 'Data karyawan tidak ditemukan.'
            ], 422);
        }

        $tipe = $request->input('tipe');
        $kodeCabang = $karyawan->kode_cabang;
        $now = Carbon::now();
        $dateStr = $now->format('Y-m-d');
        $timeStr = $now->format('H:i:s');

        // Checkpoints applicable
        $checkpoints = ChecklistCheckpoint::forBranchAndType($kodeCabang, $tipe)->get();
        if ($checkpoints->isEmpty()) {
            $checkpoints = ChecklistCheckpoint::where('is_active', true)
                ->where(function ($q) use ($tipe) {
                    $q->where('tipe', $tipe)->orWhere('tipe', 'both');
                })
                ->orderBy('urutan', 'asc')
                ->get();
        }

        $photosInput = $request->file('photos') ?: $request->input('photos'); // Array indexed by checkpoint_id => UploadedFile or base64
        $notesInput = $request->input('notes', []);

        // Verify each checkpoint has photo
        foreach ($checkpoints as $cp) {
            if (empty($photosInput[$cp->id])) {
                return response()->json([
                    'success' => false,
                    'message' => "Foto untuk checkpoint '{$cp->nama_checkpoint}' belum diambil!"
                ], 422);
            }
        }

        // Generate unique code: CHK-OPN-20261022-XXXX
        $prefix = ($tipe === 'opening') ? 'CHK-OPN' : 'CHK-CLS';
        $kodeLaporan = $prefix . '-' . $now->format('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));

        DB::beginTransaction();
        try {
            // 1. Create Header
            $laporan = ChecklistLaporan::create([
                'kode_laporan' => $kodeLaporan,
                'nik' => $karyawan->nik,
                'kode_cabang' => $kodeCabang,
                'tipe' => $tipe,
                'tanggal' => $dateStr,
                'jam' => $timeStr,
                'latitude' => $request->input('latitude'),
                'longitude' => $request->input('longitude'),
                'lokasi_nama' => $request->input('lokasi_nama') ?: ($karyawan->nama_cabang ?? 'Outlet'),
                'disclaimer_agreed' => true,
                'catatan' => $request->input('catatan'),
                'total_checkpoint' => $checkpoints->count(),
            ]);

            // 2. Process Individual Photos & Details
            $savedPhotoPaths = [];
            foreach ($checkpoints as $cp) {
                $rawImage = $photosInput[$cp->id];
                $note = $notesInput[$cp->id] ?? null;

                $meta = [
                    'nama_karyawan' => $karyawan->nama_karyawan,
                    'waktu' => $now->format('H:i'),
                    'tanggal' => $now->translatedFormat('d M Y'),
                    'lokasi' => $request->input('lokasi_nama') ?: ($karyawan->nama_cabang ?? 'Cabang'),
                    'nama_checkpoint' => $cp->nama_checkpoint,
                ];

                $photoRelPath = $this->imageService->saveCheckpointPhoto($rawImage, $meta);
                $savedPhotoPaths[] = $photoRelPath;

                ChecklistLaporanDetail::create([
                    'laporan_id' => $laporan->id,
                    'checkpoint_id' => $cp->id,
                    'nama_checkpoint' => $cp->nama_checkpoint,
                    'deskripsi' => $cp->deskripsi,
                    'foto' => $photoRelPath,
                    'catatan' => $note,
                ]);
            }

            // 3. Generate 4-in-1 Collages (Grid 2x2)
            $collageResults = $this->imageService->createCollages($savedPhotoPaths, $laporan->id);
            foreach ($collageResults as $col) {
                ChecklistLaporanCollage::create([
                    'laporan_id' => $laporan->id,
                    'batch_no' => $col['batch_no'],
                    'foto_collage' => $col['path'],
                    'total_items' => $col['count'],
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Laporan ' . ucfirst($tipe) . ' berhasil dikirim!',
                'redirect_url' => route('checklist.show', $laporan->id)
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses laporan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show Checklist Details & WhatsApp Report Sharing
     */
    public function show($id)
    {
        $laporan = ChecklistLaporan::with(['karyawan', 'cabang', 'details.checkpoint', 'collages'])
            ->findOrFail($id);

        $karyawan = $this->getKaryawan();

        // Format pre-filled WhatsApp text
        $tipeLabel = strtoupper($laporan->tipe);
        $tglStr = Carbon::parse($laporan->tanggal)->translatedFormat('l, d F Y');
        $jamStr = date('H:i', strtotime($laporan->jam)) . ' WIB';
        $cabangNama = $laporan->cabang->nama_cabang ?? $laporan->kode_cabang;
        $petugasNama = $laporan->karyawan->nama_karyawan ?? $laporan->nik;

        $waLines = [];
        $waLines[] = "*LAPORAN {$tipeLabel} TOKO - {$cabangNama}*";
        $waLines[] = "-----------------------------------------";
        $waLines[] = "Petugas : {$petugasNama}";
        $waLines[] = "Waktu   : {$tglStr} - {$jamStr}";
        $waLines[] = "Lokasi  : " . ($laporan->lokasi_nama ?: $cabangNama);
        $waLines[] = "Status  : {$laporan->total_checkpoint}/{$laporan->total_checkpoint} Checkpoint Terverifikasi Aman";
        if (!empty($laporan->catatan)) {
            $waLines[] = "Catatan : {$laporan->catatan}";
        }
        $waLines[] = "-----------------------------------------";
        $waLines[] = "*Daftar Checkpoint Diperiksa:*";

        foreach ($laporan->details as $idx => $d) {
            $num = $idx + 1;
            $waLines[] = "{$num}. [OK] {$d->nama_checkpoint}";
        }
        $waLines[] = "-----------------------------------------";
        $waLines[] = "Pernyataan: Petugas telah memverifikasi seluruh area secara langsung dan bertanggung jawab penuh atas kebenaran laporan.";
        $waLines[] = "(Foto grid 4-in-1 terlampir di bawah)";

        $waText = implode("\n", $waLines);
        $waUrl = "https://api.whatsapp.com/send?text=" . urlencode($waText);

        return view('checklist.show', compact('laporan', 'karyawan', 'waText', 'waUrl'));
    }
}
