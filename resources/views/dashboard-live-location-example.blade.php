{{-- Example Dashboard with Live Location Tracking --}}
{{-- Save this as: resources/views/dashboard-example.blade.php --}}
{{-- Or add components to your existing dashboard view --}}

@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    
    {{-- Live Location Tracker (auto-tracking for spy=1 employees) --}}
    <x-live-location-tracker />

    {{-- Location Tracking Status Badge --}}
    <x-location-tracking-badge />

    <!-- Dashboard Content -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Dashboard</h5>
                </div>
                <div class="card-body">
                    <p>Welcome {{ auth()->user()->name }}!</p>
                    
                    @if(auth()->user()->karyawan && auth()->user()->karyawan->spy == 1)
                        <div class="alert alert-info">
                            <strong>ℹ️ Informasi:</strong>
                            Akun Anda memiliki fitur Live Location Tracking aktif. 
                            Lokasi Anda akan dibagikan ke sistem monitoring secara real-time.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Optional: Add location status widget to sidebar or navbar --}}
@push('scripts')
<script>
    // Optional: Monitor tracking status
    document.addEventListener('DOMContentLoaded', () => {
        if (window.LocationTracker && window.LocationTracker.enabled) {
            console.log('✓ Live location tracking is running for this session');
        }
    });
</script>
@endpush

@endsection
