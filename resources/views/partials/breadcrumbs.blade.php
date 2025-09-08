@if (!empty($breadcrumbs) && is_array($breadcrumbs))
  <nav aria-label="Breadcrumb" class="container mt-2" role="navigation">
    <ol class="flex items-center gap-2 list-reset">
      @foreach ($breadcrumbs as $i => $bc)
        @php($isLast = $i === array_key_last($breadcrumbs))
        <li>
          @if (!empty($bc['url']) && !$isLast)
            <a href="{{ $bc['url'] }}" class="link focus-ring">{{ $bc['label'] ?? '' }}</a>
          @else
            <span aria-current="page" class="muted">{{ $bc['label'] ?? '' }}</span>
          @endif
        </li>
      @endforeach
    </ol>
  </nav>
@endif
