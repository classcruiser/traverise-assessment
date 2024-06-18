@if ($paginator->hasPages())
  <ul class="pagination align-self-center" role="navigation">
    {{-- Previous Page Link --}}
    @if ($paginator->onFirstPage())
      <li class="page-item disabled"><a href="#" rel="prev" aria-label="@lang('pagination.previous')" class="page-link">←<span class="d-none d-md-inline-block"> &nbsp; Prev</span></a></li>
    @else
      <li class="page-item">
        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')" class="page-link">←<span class="d-none d-md-inline-block"> &nbsp; Prev</a>
      </li>
    @endif

    {{-- Pagination Elements --}}
    @foreach ($elements as $element)
      {{-- "Three Dots" Separator --}}
      @if (is_string($element))
        <li class="page-item disabled" aria-disabled="true"><a class="page-link">{{ $element }}</a></li>
      @endif

      {{-- Array Of Links --}}
      @if (is_array($element))
        @foreach ($element as $page => $url)
          @if ($page == $paginator->currentPage())
            <li class="page-item active" aria-current="page"><a class="page-link">{{ $page }}</a></li>
          @else
            <li class="page-item"><a href="{{ $url }}" class="page-link">{{ $page }}</a></li>
          @endif
        @endforeach
      @endif
    @endforeach

    {{-- Next Page Link --}}
    @if ($paginator->hasMorePages())
      <li class="page-item">
        <a href="{{ $paginator->nextPageUrl() }}" class="page-link" rel="next" aria-label="@lang('pagination.next')">
          <span class="d-none d-md-inline-block">Next &nbsp; </span>→
        </a>
      </li>
    @else
      <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
        <a class="page-link"><span class="d-none d-md-inline-block">Next &nbsp; </span>→</a>
      </li>
    @endif
  </ul>
@endif
