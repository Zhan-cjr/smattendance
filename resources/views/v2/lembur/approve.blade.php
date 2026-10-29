@php
    $start = strtotime($lembur->lembur_mulai);
    $end = strtotime($lembur->lembur_selesai);
    $lama = round(hitungJam($lembur->lembur_mulai, $lembur->lembur_selesai), 2);
@endphp

<form action="{{ route('lembur.storeapprove', Crypt::encrypt($lembur->id)) }}" method="POST" id="formApproveLemburV2" class="space-y-4">
    @csrf

    <!-- Profil Karyawan Ringkas -->
    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/90 flex items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-800 font-black flex items-center justify-center text-sm shadow-2xs">
                {{ strtoupper(substr($lembur->nama_karyawan, 0, 2)) }}
            </div>
            <div>
                <h4 class="font-extrabold text-slate-900 text-sm mb-0.5">{{ $lembur->nama_karyawan }}</h4>
                <div class="text-xs text-slate-500 font-mono">{{ $lembur->nik }} &bull; {{ $lembur->nama_jabatan ?? '-' }}</div>
            </div>
        </div>
        <div class="text-right text-xs">
            <span class="text-slate-400 block text-[10.5px]">Departemen</span>
            <span class="font-bold text-slate-800">{{ $lembur->nama_dept ?? '-' }}</span>
        </div>
    </div>

    <!-- Detail Permohonan Lembur -->
    <div class="p-4 rounded-xl border border-slate-200/90 bg-white space-y-2 text-xs">
        <div class="flex justify-between py-1 border-b border-slate-100">
            <span class="text-slate-500 font-medium">Tanggal Lembur:</span>
            <span class="font-bold text-slate-800">{{ DateToIndo($lembur->tanggal) }}</span>
        </div>
        <div class="flex justify-between py-1 border-b border-slate-100">
            <span class="text-slate-500 font-medium">Rentang Waktu:</span>
            <span class="font-mono font-bold text-slate-800">{{ date('d-m-Y H:i', $start) }} s/d {{ date('d-m-Y H:i', $end) }}</span>
        </div>
        <div class="flex justify-between py-1 border-b border-slate-100">
            <span class="text-slate-500 font-medium">Durasi Estimasi:</span>
            <span class="font-bold text-emerald-700">{{ $lama }} Jam</span>
        </div>
        <div class="py-1">
            <span class="text-slate-500 font-medium block mb-0.5">Uraian Tugas / Keterangan:</span>
            <p class="text-slate-800 italic leading-relaxed font-medium bg-slate-50 p-2.5 rounded-lg border border-slate-200/80">
                &ldquo;{{ $lembur->keterangan }}&rdquo;
            </p>
        </div>
    </div>

    <!-- Action Buttons (Approve / Reject) -->
    <div class="pt-2 grid grid-cols-1 sm:grid-cols-2 gap-2.5">
        <button type="submit" 
                id="btnApproveAction"
                name="approve" 
                value="approve" 
                class="btnSubmitAction py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md transition active:scale-95 flex items-center justify-center gap-1.5 cursor-pointer">
            <i class="ti ti-check text-base"></i>
            <span>Setujui Lembur (Approve)</span>
        </button>
        <button type="submit" 
                id="btnTolakAction"
                name="tolak" 
                value="tolak" 
                class="btnSubmitAction py-2.5 px-4 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-md transition active:scale-95 flex items-center justify-center gap-1.5 cursor-pointer">
            <i class="ti ti-x text-base"></i>
            <span>Tolak Lembur (Reject)</span>
        </button>
    </div>
</form>

<script>
    (function() {
        let clickedAction = null;

        $('.btnSubmitAction').on('click', function(e) {
            clickedAction = $(this).attr('name');
        });

        $('#formApproveLemburV2').on('submit', function(e) {
            if (!clickedAction) {
                clickedAction = 'approve';
            }

            // Tambahkan hidden input agar value 'approve' atau 'tolak' tetap terkirim ke Controller saat button didisable
            $('#formApproveLemburV2').find('input[name="approve"], input[name="tolak"]').remove();
            $('<input>').attr({
                type: 'hidden',
                name: clickedAction,
                value: clickedAction
            }).appendTo('#formApproveLemburV2');

            if (clickedAction === 'approve') {
                $('#btnApproveAction').html(`
                    <div style="width: 15px; height: 15px; border: 2px solid #ffffff; border-top-color: transparent; border-radius: 50%; animation: spin 0.8s linear infinite; display: inline-block;"></div>
                    <span>Menyetujui...</span>
                `);
            } else {
                $('#btnTolakAction').html(`
                    <div style="width: 15px; height: 15px; border: 2px solid #ffffff; border-top-color: transparent; border-radius: 50%; animation: spin 0.8s linear infinite; display: inline-block;"></div>
                    <span>Menolak...</span>
                `);
            }

            $('.btnSubmitAction').prop('disabled', true).addClass('opacity-75 cursor-not-allowed');
        });
    })();
</script>
