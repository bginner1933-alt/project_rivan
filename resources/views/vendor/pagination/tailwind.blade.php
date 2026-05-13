@if ($paginator->hasPages())
    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; width: 100%; margin: 20px 0; font-family: sans-serif;">
        
        {{-- INFO TEKS --}}
        <div style="margin-bottom: 15px; color: #374151; font-size: 14px;">
            Menampilkan <strong>{{ $paginator->firstItem() }}</strong> hingga <strong>{{ $paginator->lastItem() }}</strong> dari <strong>{{ $paginator->total() }}</strong> hasil
        </div>

        {{-- CONTAINER NAVIGASI --}}
        <div style="display: flex; align-items: center; justify-content: center; gap: 8px;">
            
            {{-- Tombol Previous --}}
            @if ($paginator->onFirstPage())
                <span style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border: 1px solid #e5e7eb; border-radius: 12px; color: #d1d5db; background: #ffffff;">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M15 19l-7-7 7-7"/></svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border: 1px solid #e5e7eb; border-radius: 12px; color: #374151; background: #ffffff; text-decoration: none;">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M15 19l-7-7 7-7"/></svg>
                </a>
            @endif

            {{-- Angka-Angka --}}
            @foreach ($elements as $element)
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; background-color: #5D5CDE; color: white; border-radius: 12px; font-weight: bold; font-size: 14px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border: 1px solid #e5e7eb; border-radius: 12px; color: #5D5CDE; background: #ffffff; text-decoration: none; font-size: 14px;">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Tombol Next --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border: 1px solid #e5e7eb; border-radius: 12px; color: #5D5CDE; background: #ffffff; text-decoration: none;">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M9 5l7 7-7 7"/></svg>
                </a>
            @else
                <span style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border: 1px solid #e5e7eb; border-radius: 12px; color: #d1d5db; background: #ffffff;">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M9 5l7 7-7 7"/></svg>
                </span>
            @endif
        </div>
    </div>
@endif