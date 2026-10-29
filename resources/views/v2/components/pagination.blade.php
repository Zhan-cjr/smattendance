@if ($paginator->hasPages() || $paginator->total() > 0)
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-4 border-t border-slate-200/80">
        <!-- Info Ringkasan Data -->
        <div class="text-xs text-slate-500 font-medium flex items-center gap-1.5 flex-wrap">
            <span>Menampilkan</span>
            <span class="font-bold text-slate-800">{{ $paginator->firstItem() ?? 0 }}</span>
            <span>sampai</span>
            <span class="font-bold text-slate-800">{{ $paginator->lastItem() ?? 0 }}</span>
            <span>dari total</span>
            <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-bold text-xs">{{ $paginator->total() }}</span>
            <span>data</span>
        </div>

        @if ($paginator->hasPages())
            <!-- Tombol Navigasi Halaman -->
            <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center gap-1 select-none">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <span aria-disabled="true" aria-label="@lang('pagination.previous')" class="inline-flex items-center justify-center h-8 px-3 rounded-lg border border-slate-200/60 bg-slate-50/70 text-slate-300 text-xs font-semibold cursor-not-allowed">
                        <i class="ti ti-chevron-left text-sm mr-1"></i>
                        <span>Sebelumnya</span>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')" class="inline-flex items-center justify-center h-8 px-3 rounded-lg border border-slate-200 bg-white text-slate-700 hover:text-slate-900 hover:bg-slate-50 hover:border-slate-300 text-xs font-semibold shadow-2xs transition">
                        <i class="ti ti-chevron-left text-sm mr-1"></i>
                        <span>Sebelumnya</span>
                    </a>
                @endif

                {{-- Pagination Elements (Desktop) --}}
                <div class="hidden sm:flex items-center gap-1">
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span class="inline-flex items-center justify-center w-8 h-8 text-slate-400 text-xs font-bold tracking-widest select-none">…</span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-white font-bold text-xs shadow-xs" style="background-color: var(--theme-color-1, #0a5c41);">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-slate-200/80 bg-white text-slate-600 hover:text-slate-900 hover:bg-slate-50 hover:border-slate-300 text-xs font-semibold transition">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                </div>

                {{-- Mobile Simple Page Indicator (e.g. 1 / 3) --}}
                <div class="sm:hidden px-2 text-xs font-bold text-slate-700">
                    {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}
                </div>

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')" class="inline-flex items-center justify-center h-8 px-3 rounded-lg border border-slate-200 bg-white text-slate-700 hover:text-slate-900 hover:bg-slate-50 hover:border-slate-300 text-xs font-semibold shadow-2xs transition">
                        <span>Berikutnya</span>
                        <i class="ti ti-chevron-right text-sm ml-1"></i>
                    </a>
                @else
                    <span aria-disabled="true" aria-label="@lang('pagination.next')" class="inline-flex items-center justify-center h-8 px-3 rounded-lg border border-slate-200/60 bg-slate-50/70 text-slate-300 text-xs font-semibold cursor-not-allowed">
                        <span>Berikutnya</span>
                        <i class="ti ti-chevron-right text-sm ml-1"></i>
                    </span>
                @endif
            </nav>
        @endif
    </div>
@endif
