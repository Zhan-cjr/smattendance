<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\PembayaranPinjaman;
use App\Models\Pinjaman;
use App\Models\PinjamanGenerateHistory;
use App\Models\RencanaCicilan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class PinjamanController extends Controller
{
    public function index(Request $request)
    {
        $query = Pinjaman::query()->with('karyawan');
        
        if (!empty($request->nama_karyawan)) {
            $query->whereHas('karyawan', function($q) use ($request) {
                $q->where('nama_karyawan', 'like', '%' . $request->nama_karyawan . '%');
            });
        }

        if ($request->status != "") {
            $query->where('status', $request->status);
        }

        $data['pinjaman'] = $query->orderBy('created_at', 'desc')->paginate(10);
        $data['pinjaman']->appends($request->all());
        
        // Filter untuk Histori Generate
        $history_query = PinjamanGenerateHistory::with('user');
        if (!empty($request->bulan_search)) {
            $history_query->where('bulan', $request->bulan_search);
        }
        if (!empty($request->tahun_search)) {
            $history_query->where('tahun', $request->tahun_search);
        }

        $data['history_generate'] = $history_query
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->get();

        $data['list_bulan'] = config('global.list_bulan');
        $data['start_year'] = config('global.start_year');

        return view('payroll.pinjaman.index', $data);
    }

    public function create()
    {
        $data['karyawan'] = Karyawan::orderBy('nama_karyawan')->get();
        return view('payroll.pinjaman.create', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nik' => 'required',
            'tanggal_pinjaman' => 'required|date',
            'jumlah_pinjaman' => 'required',
            'jumlah_cicilan' => 'required|numeric|min:1',
            'bulan_mulai_cicilan' => 'required',
            'tahun_mulai_cicilan' => 'required',
        ]);

        $jumlah_pinjaman = toNumber($request->jumlah_pinjaman);
        $jumlah_cicilan = $request->jumlah_cicilan;
        $jumlah_per_cicilan = floor($jumlah_pinjaman / $jumlah_cicilan);
        
        // Handle remainder in the last installment
        // Note: In the view, the user can edit this, so we should actually receive the array from the view if we follow the plan strictly.
        // For now, if no array sent (backup), we calc it.
        
        DB::beginTransaction();
        try {
            // Generate No Pinjaman
            $lastPinjaman = Pinjaman::whereRaw('DATE(created_at) = ?', [date('Y-m-d')])->orderBy('id', 'desc')->first();
            $lastNo = $lastPinjaman ? substr($lastPinjaman->no_pinjaman, -3) : 0;
            $no_pinjaman = "PNJ" . date('ymd') . str_pad($lastNo + 1, 3, '0', STR_PAD_LEFT);

            $pinjaman = Pinjaman::create([
                'nik' => $request->nik,
                'no_pinjaman' => $no_pinjaman,
                'tanggal_pinjaman' => $request->tanggal_pinjaman,
                'jumlah_pinjaman' => $jumlah_pinjaman,
                'jumlah_cicilan' => $jumlah_cicilan,
                'jumlah_per_cicilan' => $jumlah_per_cicilan,
                'total_dibayar' => 0,
                'sisa_pinjaman' => $jumlah_pinjaman,
                'keterangan' => $request->keterangan,
                'bulan_mulai_cicilan' => $request->bulan_mulai_cicilan,
                'tahun_mulai_cicilan' => $request->tahun_mulai_cicilan,
            ]);

            // Create Rencana Cicilan
            if ($request->has('rencana_nominal')) {
                // If sent from the editable preview
                foreach($request->rencana_nominal as $key => $nominal) {
                    RencanaCicilan::create([
                        'pinjaman_id' => $pinjaman->id,
                        'cicilan_ke' => $key + 1,
                        'bulan' => $request->rencana_bulan[$key],
                        'tahun' => $request->rencana_tahun[$key],
                        'jumlah_cicilan' => toNumber($nominal),
                        'status' => 'B'
                    ]);
                }
            } else {
                // Default calculation
                $current_bulan = $request->bulan_mulai_cicilan;
                $current_tahun = $request->tahun_mulai_cicilan;
                $total_rencana = 0;

                for ($i = 1; $i <= $jumlah_cicilan; $i++) {
                    $nominal = ($i == $jumlah_cicilan) ? ($jumlah_pinjaman - $total_rencana) : $jumlah_per_cicilan;
                    
                    RencanaCicilan::create([
                        'pinjaman_id' => $pinjaman->id,
                        'cicilan_ke' => $i,
                        'bulan' => $current_bulan,
                        'tahun' => $current_tahun,
                        'jumlah_cicilan' => $nominal,
                        'status' => 'B'
                    ]);

                    $total_rencana += $nominal;
                    
                    if ($current_bulan == 12) {
                        $current_bulan = 1;
                        $current_tahun++;
                    } else {
                        $current_bulan++;
                    }
                }
            }

            DB::commit();
            return Redirect::route('pinjaman.index')->with(messageSuccess('Pinjaman Berhasil Disimpan'));
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    public function show($id)
    {
        $id = Crypt::decrypt($id);
        $data['pinjaman'] = Pinjaman::with(['karyawan', 'rencana_cicilan', 'pembayaran_pinjaman'])->find($id);
        $data['generated_periods'] = PinjamanGenerateHistory::select('bulan', 'tahun')->get()
            ->map(function($h) {
                return $h->bulan . '-' . $h->tahun;
            })->toArray();
            
        return view('payroll.pinjaman.show', $data);
    }

    public function edit($id)
    {
        $id = Crypt::decrypt($id);
        $data['pinjaman'] = Pinjaman::find($id);
        
        // Only allow edit if no payments yet
        if ($data['pinjaman']->total_dibayar > 0) {
            return Redirect::back()->with(messageError('Pinjaman tidak dapat diedit karena sudah ada pembayaran'));
        }
        
        return view('payroll.pinjaman.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $id = Crypt::decrypt($id);
        $pinjaman = Pinjaman::find($id);

        if ($pinjaman->total_dibayar > 0) {
            return Redirect::back()->with(messageError('Pinjaman tidak dapat diupdate karena sudah ada pembayaran'));
        }

        $request->validate([
            'tanggal_pinjaman' => 'required|date',
            'jumlah_pinjaman' => 'required',
            'jumlah_cicilan' => 'required|numeric|min:1',
            'bulan_mulai_cicilan' => 'required',
            'tahun_mulai_cicilan' => 'required',
        ]);

        $jumlah_pinjaman = toNumber($request->jumlah_pinjaman);
        $jumlah_cicilan = $request->jumlah_cicilan;
        $jumlah_per_cicilan = floor($jumlah_pinjaman / $jumlah_cicilan);

        DB::beginTransaction();
        try {
            $pinjaman->update([
                'tanggal_pinjaman' => $request->tanggal_pinjaman,
                'jumlah_pinjaman' => $jumlah_pinjaman,
                'jumlah_cicilan' => $jumlah_cicilan,
                'jumlah_per_cicilan' => $jumlah_per_cicilan,
                'sisa_pinjaman' => $jumlah_pinjaman,
                'keterangan' => $request->keterangan,
                'bulan_mulai_cicilan' => $request->bulan_mulai_cicilan,
                'tahun_mulai_cicilan' => $request->tahun_mulai_cicilan,
            ]);

            // Regerenate Rencana Cicilan
            RencanaCicilan::where('pinjaman_id', $pinjaman->id)->delete();
            
            if ($request->has('rencana_nominal')) {
                foreach($request->rencana_nominal as $key => $nominal) {
                    RencanaCicilan::create([
                        'pinjaman_id' => $pinjaman->id,
                        'cicilan_ke' => $key + 1,
                        'bulan' => $request->rencana_bulan[$key],
                        'tahun' => $request->rencana_tahun[$key],
                        'jumlah_cicilan' => toNumber($nominal),
                        'status' => 'B'
                    ]);
                }
            } else {
                $current_bulan = $request->bulan_mulai_cicilan;
                $current_tahun = $request->tahun_mulai_cicilan;
                $total_rencana = 0;

                for ($i = 1; $i <= $jumlah_cicilan; $i++) {
                    $nominal = ($i == $jumlah_cicilan) ? ($jumlah_pinjaman - $total_rencana) : $jumlah_per_cicilan;
                    RencanaCicilan::create([
                        'pinjaman_id' => $pinjaman->id,
                        'cicilan_ke' => $i,
                        'bulan' => $current_bulan,
                        'tahun' => $current_tahun,
                        'jumlah_cicilan' => $nominal,
                        'status' => 'B'
                    ]);
                    $total_rencana += $nominal;
                    if ($current_bulan == 12) { $current_bulan = 1; $current_tahun++; } else { $current_bulan++; }
                }
            }

            DB::commit();
            return Redirect::route('pinjaman.index')->with(messageSuccess('Pinjaman Berhasil Diupdate'));
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    public function destroy($id)
    {
        $id = Crypt::decrypt($id);
        $pinjaman = Pinjaman::find($id);

        if ($pinjaman->total_dibayar > 0) {
            return Redirect::back()->with(messageError('Pinjaman tidak dapat dihapus karena sudah ada pembayaran'));
        }

        try {
            $pinjaman->delete();
            return Redirect::route('pinjaman.index')->with(messageSuccess('Pinjaman Berhasil Dihapus'));
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    public function generatePembayaran()
    {
        $data['list_bulan'] = config('global.list_bulan');
        $data['start_year'] = config('global.start_year');
        return view('payroll.pinjaman.generate', $data);
    }

    public function prosesGeneratePembayaran(Request $request)
    {
        $request->validate([
            'bulan' => 'required',
            'tahun' => 'required'
        ]);

        // Cek apakah sudah pernah digenerate
        $cek = PinjamanGenerateHistory::where('bulan', $request->bulan)->where('tahun', $request->tahun)->first();
        if ($cek) {
            return Redirect::back()->with(messageError('Pembayaran untuk periode ' . $request->bulan . '-' . $request->tahun . ' sudah pernah digenerate.'));
        }

        $rencana = RencanaCicilan::where('bulan', $request->bulan)
            ->where('tahun', $request->tahun)
            ->where('status', 'B')
            ->get();

        if ($rencana->isEmpty()) {
            return Redirect::back()->with(messageError('Tidak ada rencana cicilan untuk periode ini'));
        }

        DB::beginTransaction();
        try {
            // Generate Kode Generate
            $tgl = date('Ymd');
            $last_history = PinjamanGenerateHistory::where('kode_generate', 'like', 'GEN-' . $tgl . '-%')
                ->orderBy('kode_generate', 'desc')
                ->first();
            $last_no = $last_history ? (int)explode('-', $last_history->kode_generate)[2] : 0;
            $new_no = str_pad($last_no + 1, 4, '0', STR_PAD_LEFT);
            $kode_generate = 'GEN-' . $tgl . '-' . $new_no;

            // Create History
            $history = PinjamanGenerateHistory::create([
                'kode_generate' => $kode_generate,
                'bulan' => $request->bulan,
                'tahun' => $request->tahun,
                'tanggal_generate' => date('Y-m-d'),
                'user_id' => Auth::id()
            ]);

            $count = 0;
            foreach ($rencana as $r) {
                // Hitung sisa yang belum dibayar untuk cicilan ini
                $terbayar = PembayaranPinjaman::where('rencana_cicilan_id', $r->id)->sum('jumlah_bayar');
                $sisa_akan_dibayar = $r->jumlah_cicilan - $terbayar;

                if ($sisa_akan_dibayar <= 0) {
                    $r->update(['status' => 'S']);
                    continue;
                }

                // Create Payment
                PembayaranPinjaman::create([
                    'no_bukti' => $kode_generate,
                    'pinjaman_id' => $r->pinjaman_id,
                    'rencana_cicilan_id' => $r->id,
                    'history_generate_id' => $history->id,
                    'tanggal_bayar' => date('Y-m-d'),
                    'bulan_gaji' => $request->bulan,
                    'tahun_gaji' => $request->tahun,
                    'jumlah_bayar' => $sisa_akan_dibayar,
                    'jenis_pembayaran' => 'C'
                ]);

                // Update Rencana
                $r->update(['status' => 'S']);

                // Update Pinjaman
                $pinjaman = Pinjaman::find($r->pinjaman_id);
                $total_dibayar = $pinjaman->total_dibayar + $sisa_akan_dibayar;
                $sisa = $pinjaman->jumlah_pinjaman - $total_dibayar;
                
                $pinjaman->update([
                    'total_dibayar' => $total_dibayar,
                    'sisa_pinjaman' => $sisa,
                    'status' => $sisa <= 0 ? 'L' : 'A'
                ]);

                $count++;
            }

            DB::commit();
            return Redirect::route('pinjaman.index')->with(messageSuccess($count . ' Pembayaran Berhasil Digenerate'));
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    public function createPembayaranManual(Request $request, $id)
    {
        $id = Crypt::decrypt($id);
        $data['pinjaman'] = Pinjaman::with(['karyawan', 'rencana_cicilan'])->find($id);
        $data['rencana_cicilan'] = $data['pinjaman']->rencana_cicilan()->where('status', 'B')->orderBy('tahun', 'asc')->orderBy('bulan', 'asc')->get();

        if ($request->ajax()) {
            return view('payroll.pinjaman.pembayaran_manual', $data)->with('is_ajax', true);
        }

        return view('payroll.pinjaman.pembayaran_manual', $data);
    }

    public function storePembayaranManual(Request $request, $id)
    {
        $id = Crypt::decrypt($id);
        $pinjaman = Pinjaman::with('karyawan')->find($id);

        $request->validate([
            'tanggal_bayar' => 'required|date',
            'jumlah_bayar' => 'required',
            'jenis_pembayaran' => 'required',
        ]);

        $jumlah_bayar = toNumber($request->jumlah_bayar);
        
        if ($jumlah_bayar > $pinjaman->sisa_pinjaman) {
            return Redirect::back()->with(messageError('Jumlah bayar melebihi sisa pinjaman'));
        }

        DB::beginTransaction();
        try {
            $remaining_to_pay = $jumlah_bayar;

            // Generate No Bukti
            $tgl = date('Ymd', strtotime($request->tanggal_bayar));
            $last_pembayaran = PembayaranPinjaman::where('no_bukti', 'like', 'PYM-' . $tgl . '-%')
                ->orderBy('no_bukti', 'desc')
                ->first();
            $last_no = $last_pembayaran ? (int)explode('-', $last_pembayaran->no_bukti)[2] : 0;
            $new_no = str_pad($last_no + 1, 4, '0', STR_PAD_LEFT);
            $no_bukti = 'PYM-' . $tgl . '-' . $new_no;

            // Fetch unpaid installments in FIFO order
            $rencana_cicilan = RencanaCicilan::where('pinjaman_id', $pinjaman->id)
                ->where('status', 'B')
                ->orderBy('tahun', 'asc')
                ->orderBy('bulan', 'asc')
                ->get();

            if ($request->jenis_pembayaran == 'P') {
                // Pelunasan Full
                foreach ($rencana_cicilan as $rc) {
                    $cekHistory = PinjamanGenerateHistory::where('bulan', $rc->bulan)->where('tahun', $rc->tahun)->first();
                    if ($cekHistory) continue; 

                    $rc->update(['status' => 'S']);
                }
                
                PembayaranPinjaman::create([
                    'no_bukti' => $no_bukti,
                    'pinjaman_id' => $pinjaman->id,
                    'tanggal_bayar' => $request->tanggal_bayar,
                    'bulan_gaji' => date('m', strtotime($request->tanggal_bayar)),
                    'tahun_gaji' => date('Y', strtotime($request->tanggal_bayar)),
                    'jumlah_bayar' => $jumlah_bayar,
                    'jenis_pembayaran' => 'P',
                    'keterangan' => $request->keterangan ?? 'Pelunasan Full'
                ]);
            } else {
                // FIFO Allocation for Manual Payment
                foreach ($rencana_cicilan as $rc) {
                    if ($remaining_to_pay <= 0) break;

                    $cekHistory = PinjamanGenerateHistory::where('bulan', $rc->bulan)->where('tahun', $rc->tahun)->first();
                    if ($cekHistory) continue;

                    $paid_so_far = PembayaranPinjaman::where('rencana_cicilan_id', $rc->id)->sum('jumlah_bayar');
                    $needed = $rc->jumlah_cicilan - $paid_so_far;

                    if ($needed <= 0) {
                        $rc->update(['status' => 'S']);
                        continue;
                    }

                    $pay_now = min($remaining_to_pay, $needed);

                    PembayaranPinjaman::create([
                        'no_bukti' => $no_bukti,
                        'pinjaman_id' => $pinjaman->id,
                        'rencana_cicilan_id' => $rc->id,
                        'tanggal_bayar' => $request->tanggal_bayar,
                        'bulan_gaji' => $rc->bulan,
                        'tahun_gaji' => $rc->tahun,
                        'jumlah_bayar' => $pay_now,
                        'jenis_pembayaran' => 'M',
                        'keterangan' => $request->keterangan
                    ]);

                    $remaining_to_pay -= $pay_now;
                    
                    if ($paid_so_far + $pay_now >= $rc->jumlah_cicilan) {
                        $rc->update(['status' => 'S']);
                    }
                }

                if ($remaining_to_pay > 0) {
                    PembayaranPinjaman::create([
                        'no_bukti' => $no_bukti,
                        'pinjaman_id' => $pinjaman->id,
                        'tanggal_bayar' => $request->tanggal_bayar,
                        'bulan_gaji' => date('m', strtotime($request->tanggal_bayar)),
                        'tahun_gaji' => date('Y', strtotime($request->tanggal_bayar)),
                        'jumlah_bayar' => $remaining_to_pay,
                        'jenis_pembayaran' => 'M',
                        'keterangan' => $request->keterangan ?? 'Sisa Alokasi Manual'
                    ]);
                }
            }

            // Global totals update
            $total_dibayar = $pinjaman->total_dibayar + $jumlah_bayar;
            $sisa = $pinjaman->jumlah_pinjaman - $total_dibayar;

            $pinjaman->update([
                'total_dibayar' => $total_dibayar,
                'sisa_pinjaman' => $sisa,
                'status' => $sisa <= 0 ? 'L' : 'A'
            ]);

            DB::commit();
            return Redirect::route('pinjaman.show', Crypt::encrypt($pinjaman->id))->with(messageSuccess('Pembayaran Berhasil Disimpan (No Bukti: ' . $no_bukti . ')'));
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    public function destroyPembayaran($id)
    {
        $id = Crypt::decrypt($id);
        $pembayaran = PembayaranPinjaman::find($id);
        $pinjaman = Pinjaman::find($pembayaran->pinjaman_id);

        // Validasi 1: Jika merupakan hasil generate, tidak boleh dihapus satu per satu
        if ($pembayaran->history_generate_id != null) {
            return Redirect::back()->with(messageError('Pembayaran hasil generate tidak dapat dihapus secara manual. Silahkan hapus melalui Histori Generate.'));
        }

        // Validasi 2: Jika periode tersebut sudah pernah digenerate, manual payment di periode itu tidak boleh dihapus
        $cekHistory = PinjamanGenerateHistory::where('bulan', $pembayaran->bulan_gaji)
            ->where('tahun', $pembayaran->tahun_gaji)
            ->first();
        if ($cekHistory) {
            return Redirect::back()->with(messageError('Pembayaran tidak dapat dihapus karena periode ' . getNamabulan($pembayaran->bulan_gaji) . ' ' . $pembayaran->tahun_gaji . ' sudah dilakukan generate pembayaran masal.'));
        }

        DB::beginTransaction();
        try {
            if ($pembayaran->no_bukti) {
                $all_payments = PembayaranPinjaman::where('no_bukti', $pembayaran->no_bukti)
                    ->where('pinjaman_id', $pinjaman->id)
                    ->get();
            } else {
                $all_payments = collect([$pembayaran]);
            }

            $total_refund = $all_payments->sum('jumlah_bayar');

            // Rollback pinjaman
            $total_dibayar = $pinjaman->total_dibayar - $total_refund;
            $sisa = $pinjaman->jumlah_pinjaman - $total_dibayar;
            
            $pinjaman->update([
                'total_dibayar' => $total_dibayar,
                'sisa_pinjaman' => $sisa,
                'status' => 'A'
            ]);

            foreach ($all_payments as $item) {
                // Rollback rencana cicilan if it was linked
                if ($item->rencana_cicilan_id) {
                    RencanaCicilan::where('id', $item->rencana_cicilan_id)->update(['status' => 'B']);
                }
                $item->delete();
            }

            DB::commit();
            return Redirect::back()->with(messageSuccess('Pembayaran Berhasil Dibatalkan'));
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    public function preview(Request $request)
    {
        $jumlah_pinjaman = toNumber($request->jumlah_pinjaman);
        $tenor = $request->tenor;
        $tanggal = $request->tanggal;

        // Determine start month and year
        if ($request->has('bulan_mulai_cicilan') && $request->has('tahun_mulai_cicilan')) {
            $current_bulan = (int)$request->bulan_mulai_cicilan;
            $current_tahun = (int)$request->tahun_mulai_cicilan;
        } else {
            // Default: 1 month after loan date
            $start_date = date('Y-m-d', strtotime('+1 month', strtotime($tanggal)));
            $current_bulan = (int)date('m', strtotime($start_date));
            $current_tahun = (int)date('Y', strtotime($start_date));
        }

        $preview = [];
        $total_rencana = 0;
        $jml_per_cicilan = $tenor > 0 ? floor($jumlah_pinjaman / $tenor) : 0;
        for ($i = 1; $i <= $tenor; $i++) {
            $nominal = ($i == $tenor) ? ($jumlah_pinjaman - $total_rencana) : $jml_per_cicilan;
            $preview[] = [
                'cicilan_ke' => $i,
                'bulan' => $current_bulan,
                'tahun' => $current_tahun,
                'nominal' => $nominal
            ];
            $total_rencana += $nominal;

            if ($current_bulan == 12) {
                $current_bulan = 1;
                $current_tahun++;
            } else {
                $current_bulan++;
            }
        }

        return view('payroll.pinjaman.preview', compact('preview'));
    }

    public function showHistoryGenerate($id)
    {
        $id = Crypt::decrypt($id);
        $data['history'] = PinjamanGenerateHistory::with(['pembayaran_pinjaman' => function($q) {
            $q->with(['pinjaman.karyawan', 'rencana_cicilan']);
        }])->find($id);

        return view('payroll.pinjaman.history_detail', $data);
    }

    public function destroyHistoryGenerate($id)
    {
        $id = Crypt::decrypt($id);
        $history = PinjamanGenerateHistory::with('pembayaran_pinjaman')->find($id);

        // Check if there is a newer generate history
        $newerHistory = PinjamanGenerateHistory::where('tahun', '>=', $history->tahun)
            ->where(function($q) use ($history) {
                $q->where('bulan', '>', $history->bulan)
                  ->orWhere('tahun', '>', $history->tahun);
            })
            ->first();

        if ($newerHistory) {
            return Redirect::back()->with(messageError('Tidak dapat menghapus history ini karena sudah ada generate pembayaran bulan berikutnya.'));
        }

        DB::beginTransaction();
        try {
            foreach ($history->pembayaran_pinjaman as $pay) {
                $pinjaman = Pinjaman::find($pay->pinjaman_id);
                
                // Rollback Pinjaman Balance
                $total_dibayar = $pinjaman->total_dibayar - $pay->jumlah_bayar;
                $sisa = $pinjaman->jumlah_pinjaman - $total_dibayar;
                
                $pinjaman->update([
                    'total_dibayar' => $total_dibayar,
                    'sisa_pinjaman' => $sisa,
                    'status' => 'A'
                ]);

                // Rollback Rencana Status
                if ($pay->rencana_cicilan_id) {
                    RencanaCicilan::where('id', $pay->rencana_cicilan_id)->update(['status' => 'B']);
                }

                $pay->delete();
            }

            $history->delete();

            DB::commit();
            return Redirect::back()->with(messageSuccess('History Generate Berhasil Dihapus'));
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }
}
