@extends('layouts.app')

@section('title', 'Riwayat Pesan WhatsApp')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Riwayat Pesan WhatsApp</h5>
                        <div class="card-tools">
                            <a href="{{ route('wagateway.index') }}" class="btn btn-sm btn-primary">
                                <i class="ti ti-arrow-left"></i> Kembali ke WA Gateway
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="messagesTable">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Pengirim</th>
                                        <th>Penerima</th>
                                        <th>Pesan</th>
                                        <th>Status</th>
                                        <th>Message ID</th>
                                        <th>Error</th>
                                        <th>Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($messages as $index => $message)
                                        <tr>
                                            <td>{{ $messages->firstItem() + $index }}</td>
                                            <td>
                                                <span class="badge bg-info">{{ $message->pengirim }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $message->penerima }}</span>
                                            </td>
                                            <td>
                                                <div class="message-content" style="max-width: 200px;">
                                                    @if (strlen($message->pesan) > 50)
                                                        <span class="message-preview">{{ substr($message->pesan, 0, 50) }}...</span>
                                                        <button class="btn btn-sm btn-link p-0 ms-1" onclick="showFullMessage({{ $message->id }})">
                                                            <i class="ti ti-eye"></i>
                                                        </button>
                                                    @else
                                                        {{ $message->pesan }}
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @if ($message->status === 'success')
                                                    <span class="badge bg-success">
                                                        <i class="ti ti-check-circle"></i> Berhasil
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger">
                                                        <i class="ti ti-x-circle"></i> Gagal
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($message->message_id)
                                                    <code class="text-success">{{ $message->message_id }}</code>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($message->error_message)
                                                    <span class="text-danger" style="max-width: 150px; display: inline-block;">
                                                        {{ substr($message->error_message, 0, 30) }}...
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $message->created_at->format('d/m/Y H:i:s') }}
                                                </small>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">Belum ada pesan yang dikirim</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-3">
                            {{ $messages->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk menampilkan pesan lengkap -->
    <div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="messageModalLabel">Detail Pesan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-6">
                            <strong>Pengirim:</strong><br>
                            <span class="badge bg-info" id="modalPengirim"></span>
                        </div>
                        <div class="col-6">
                            <strong>Penerima:</strong><br>
                            <span class="badge bg-secondary" id="modalPenerima"></span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <strong>Pesan:</strong><br>
                        <div class="border p-3 rounded bg-light" id="modalPesan"></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <strong>Status:</strong><br>
                            <span id="modalStatus"></span>
                        </div>
                        <div class="col-6">
                            <strong>Tanggal:</strong><br>
                            <span id="modalTanggal"></span>
                        </div>
                    </div>
                    <div class="mb-3" id="messageIdSection" style="display: none;">
                        <strong>Message ID:</strong><br>
                        <code id="modalMessageId"></code>
                    </div>
                    <div class="mb-3" id="errorSection" style="display: none;">
                        <strong>Error:</strong><br>
                        <div class="border p-3 rounded bg-danger text-white" id="modalError"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('myscript')
    <script>
        function showFullMessage(messageId) {
            // Ambil data pesan dari tabel
            const row = event.target.closest('tr');
            const cells = row.querySelectorAll('td');

            const pengirim = cells[1].querySelector('.badge').textContent.trim();
            const penerima = cells[2].querySelector('.badge').textContent.trim();
            const pesan = cells[3].querySelector('.message-preview').textContent.trim();
            const status = cells[4].querySelector('.badge').textContent.trim();
            const messageIdValue = cells[5].querySelector('code') ? cells[5].querySelector('code').textContent.trim() : '';
            const error = cells[6].textContent.trim();
            const tanggal = cells[7].textContent.trim();

            // Isi modal
            document.getElementById('modalPengirim').textContent = pengirim;
            document.getElementById('modalPenerima').textContent = penerima;
            document.getElementById('modalPesan').textContent = pesan;
            document.getElementById('modalStatus').innerHTML = cells[4].innerHTML;
            document.getElementById('modalTanggal').textContent = tanggal;

            // Tampilkan/sembunyikan section berdasarkan data
            if (messageIdValue && messageIdValue !== '-') {
                document.getElementById('modalMessageId').textContent = messageIdValue;
                document.getElementById('messageIdSection').style.display = 'block';
            } else {
                document.getElementById('messageIdSection').style.display = 'none';
            }

            if (error && error !== '-') {
                document.getElementById('modalError').textContent = error;
                document.getElementById('errorSection').style.display = 'block';
            } else {
                document.getElementById('errorSection').style.display = 'none';
            }

            // Tampilkan modal
            $('#messageModal').modal('show');
        }
    </script>
@endpush
