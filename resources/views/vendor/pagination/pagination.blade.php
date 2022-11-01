@if ($paginator->hasPages())
    @if (!$paginator->onFirstPage())
    <ul class="pagination">
        <li class="page-item"><a class="page-link page-link-chevron" href="{{ $paginator->previousPageUrl() }}"><i class="bx bx-chevron-left"></i></a>
        </li>
    </ul>
    @endif
    <ul class="pagination">
        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <li class="page-item  d-sm-block page-link-chevron" aria-current="page"><span class="page-link">{{ $element }}</span></li>
            @endif
            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                            <li class="page-item active d-sm-block" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                    @else
                            <li class="page-item  d-sm-block"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                    @endif
                @endforeach
            @endif
        @endforeach
    </ul>
    @if ($paginator->hasMorePages())
        <ul class="pagination">
            <li class="page-item"><a class="page-link page-link-chevron" href="{{ $paginator->nextPageUrl() }}" aria-label="Next"><i class="bx bx-chevron-right"></i></a>
            </li>
        </ul>
    @endif
@endif
