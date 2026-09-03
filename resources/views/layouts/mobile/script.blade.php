<!-- ///////////// Js Files ////////////////////  -->
<!-- Jquery - Required early, tidak bisa defer -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<!-- Bootstrap - Required after jQuery -->
<script src="{{ asset('assets/template/js/lib/popper.min.js') }}"></script>
<script src="{{ asset('assets/template/js/lib/bootstrap.min.js') }}"></script>
<!-- Ionicons - Module/nomodule pattern -->
<script type="module" src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons/ionicons.js" defer></script>
<!-- jQuery Circle Progress - jQuery dependent -->
<script src="{{ asset('assets/template/js/plugins/jquery-circle-progress/circle-progress.min.js') }}"></script>
<!-- Base Js File - Required untuk layout -->
<script src="{{ asset('assets/template/js/base.js') }}?v={{ time() }}"></script>
<!-- Toastr - jQuery dependent -->
<script src="{{ asset('assets/vendor/libs/toastr/toastr.js') }}"></script>

<!-- Non-critical scripts - menggunakan defer untuk non-blocking -->
<!-- AmCharts - hanya digunakan di beberapa halaman -->
<script src="https://cdn.amcharts.com/lib/4/core.js" defer></script>
<script src="https://cdn.amcharts.com/lib/4/charts.js" defer></script>
<script src="https://cdn.amcharts.com/lib/4/themes/animated.js" defer></script>
<!-- Webcam - hanya digunakan di halaman tertentu -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js" defer></script>
<!-- SweetAlert2 - jQuery dependent tapi bisa defer karena tidak critical -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js" defer></script>
<!-- Materialize - hanya digunakan di beberapa halaman -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/js/materialize.min.js" defer></script>
<!-- MaskMoney - jQuery dependent -->
<script src="{{ asset('assets/template/js/maskMoney.js') }}" defer></script>
<!-- Rolldate - date picker -->
<script src="https://cdn.jsdelivr.net/npm/rolldate@3.1.3/dist/rolldate.min.js" defer></script>
{{-- <script src="{{ asset('assets/vendor/face-api.min.js') }}"></script> --}}
<style>
    .toast-bottom-full-width {
        bottom: 5rem
    }
</style>
{{-- <script>
    toastr.options.showEasing = 'swing';
    toastr.options.hideEasing = 'linear';
    toastr.options.progressBar = true;
    toastr.options.positionClass = 'toast-bottom-full-width';
    toastr.success("Berhasil", "Data Berhasil Disimpan", {
        timeOut: 3000
    });
</script> --}}
@if ($message = Session::get('success'))
    <script>
        toastr.options.showEasing = 'swing';
        toastr.options.hideEasing = 'linear';
        toastr.options.progressBar = true;
        toastr.options.positionClass = 'toast-bottom-full-width';
        toastr.success("Berhasil", "{{ $message }}", {
            timeOut: 3000
        });
    </script>
@endif

@if ($message = Session::get('error'))
    <script>
        toastr.options.showEasing = 'swing';
        toastr.options.hideEasing = 'linear';
        toastr.options.progressBar = true;
        toastr.options.positionClass = 'toast-bottom-full-width';
        toastr.error("Gagal", "{{ $message }}", {
            timeOut: 3000
        });
    </script>
@endif

@if ($message = Session::get('warning'))
    <script>
        toastr.options.showEasing = 'swing';
        toastr.options.hideEasing = 'linear';
        toastr.options.progressBar = true;
        toastr.warning("Warning", "{{ $message }}", {
            timeOut: 3000
        });
    </script>
@endif

@if ($errors->any())
    @php
        $err = '';
    @endphp
    @foreach ($errors->all() as $error)
        @php
            $err .= $error;
        @endphp
    @endforeach
    <script>
        toastr.options.showEasing = 'swing';
        toastr.options.hideEasing = 'linear';
        toastr.options.progressBar = true;
        // toastr.options.positionClass = 'toast-top-center';
        toastr.error("Gagal", "{{ $err }}", {
            timeOut: 3000
        });
    </script>
@endif
<script>
    $('.cancel-confirm').click(function(event) {
        var form = $(this).closest("form");
        var name = $(this).data("name");
        event.preventDefault();
        Swal.fire({
            title: `Apakah Anda Yakin Ingin Membatalkan Data Ini ?`,
            text: "Data ini akan dibatalkan.",
            icon: "warning",
            buttons: true,
            dangerMode: true,
            showCancelButton: true,
            confirmButtonColor: "#554bbb",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, Batalkan Saja Saja!"
        }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
</script>
<script>
    $(document).ready(function() {

        // function adjustZoom() {
        //     var width = $(window).width(); // Ambil lebar layar
        //     //alert(width);
        //     // $('body').css('zoom', '120%');
        //     if (width <= 400) { // Misalnya untuk layar kecil (mobile)
        //         $('body').css('zoom', '88%'); // Zoom out ke 80%
        //     } else if (width <= 768) { // Untuk tablet kecil
        //         $('body').css('zoom', '90%');
        //     } else {
        //         $('body').css('zoom', '100%'); // Normal zoom
        //     }
        // }

        // adjustZoom(); // Panggil saat halaman dimuat

        // $(window).resize(function() {
        //     adjustZoom(); // Panggil lagi saat ukuran layar berubah
        // });
    });
</script>

<!-- ===================================
     PAGE LOADING / PRELOADER SCRIPT
     =================================== -->
<script>
    // Preloader utility functions
    const PreloaderManager = {
        overlay: null,
        timeout: null,
        minDuration: 300, // Minimum show time in ms
        autoHideDelay: 10000, // Auto hide after 10 seconds
        isInitialPageLoad: true, // Track if this is initial page load

        init() {
            this.overlay = document.getElementById('preloaderOverlay');
            if (!this.overlay) return;

            // Show preloader on initial page load (for ALL pages)
            // Check if page took time to load (network delay detected)
            if (window.performance && window.performance.timing) {
                var navigationStart = window.performance.timing.navigationStart;
                var currentTime = Date.now();
                var pageLoadTime = currentTime - navigationStart;

                // If page load time > 100ms, show preloader briefly
                // This gives visual feedback that page was loading
                if (pageLoadTime > 100) {
                    this.show();
                    this.isInitialPageLoad = true;
                }
            }

            // Show preloader on link clicks (for navigation)
            document.addEventListener('click', (e) => {
                const link = e.target.closest('a[href]:not([data-no-preloader])');
                if (link && !link.hasAttribute('data-no-preloader')) {
                    const href = link.getAttribute('href');
                    if (!href || href.startsWith('#') || href.startsWith('javascript:')) return;
                    
                    try {
                        const url = new URL(href, window.location.origin);
                        // Tampilkan preloader jika url menuju internal origin yang sama dan tidak _blank
                        if (url.origin === window.location.origin && link.target !== '_blank') {
                            this.show();
                        }
                    } catch (err) {
                        // Fallback jika new URL gagal
                        if (!href.startsWith('http') && link.target !== '_blank') {
                            this.show();
                        }
                    }
                }
            });

            // Show preloader on form submission
            document.addEventListener('submit', (e) => {
                const form = e.target;
                if (!form.hasAttribute('data-no-preloader')) {
                    this.show();
                }
            });

            // Auto-hide preloader when page is ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => this.scheduleHide());
            } else {
                this.scheduleHide();
            }

            // Hide preloader on window load
            window.addEventListener('load', () => this.scheduleHide());

            // FIX: Handle BFCache (Back-Forward Cache)
            // This ensures preloader is hidden when user navigates using back/forward buttons
            window.addEventListener('pageshow', (event) => {
                if (event.persisted) {
                    console.log('Page restored from BFCache, hiding preloader');
                    this.hide();
                }
            });
        },

        show() {
            if (!this.overlay) return;

            this.overlay.classList.add('active');

            // Clear any existing timeout
            if (this.timeout) {
                clearTimeout(this.timeout);
            }

            // Auto-hide after max duration
            this.timeout = setTimeout(() => {
                this.hide();
            }, this.autoHideDelay);
        },

        hide() {
            if (!this.overlay) return;

            this.overlay.classList.remove('active');

            // Clear timeout
            if (this.timeout) {
                clearTimeout(this.timeout);
                this.timeout = null;
            }
        },

        scheduleHide() {
            // Ensure minimum display time
            if (this.timeout) {
                clearTimeout(this.timeout);
            }

            this.timeout = setTimeout(() => {
                this.hide();
            }, this.minDuration);
        }
    };

    // Initialize preloader manager
    document.addEventListener('DOMContentLoaded', () => {
        PreloaderManager.init();
    });

    // Expose globally for manual control if needed
    window.Preloader = {
        show: () => PreloaderManager.show(),
        hide: () => PreloaderManager.hide()
    };

    // Example: Show preloader on AJAX requests
    if (typeof jQuery !== 'undefined') {
        jQuery(document).ajaxStart(function() {
            PreloaderManager.show();
        }).ajaxStop(function() {
            PreloaderManager.scheduleHide();
        });
    }
</script>

@stack('myscript')
