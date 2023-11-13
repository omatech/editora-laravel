@if ($paginator->hasPages())
    <nav>
        <ul class="pagination">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="paginate_button page-item previous disabled" aria-disabled="true" aria-label="{{ getMessage('paginacion_anteriores') }}">
                    <span class="page-link" aria-hidden="true">{{ getMessage('paginacion_anteriores') }}</span>
                </li>
            @else
                <li class="paginate_button page-item previous">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev"
                       aria-label="{{ getMessage('paginacion_anteriores') }}">{{ getMessage('paginacion_anteriores') }}</a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="disabled paginate_button page-item" aria-disabled="true"><span class="page-link">{{ $element }}</span></li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="paginate_button page-item  active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                        @else
                            <li class="paginate_button page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="paginate_button page-item next">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next"
                       aria-label="{{ getMessage('paginacion_siguientes') }}">{{ getMessage('paginacion_siguientes') }}</a>
                </li>
            @else
                <li class="paginate_button page-item next disabled" aria-disabled="true"
                    aria-label="{{ getMessage('paginacion_siguientes') }}">
                    <span class="page-link" aria-hidden="true">{{ getMessage('paginacion_siguientes') }}</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
