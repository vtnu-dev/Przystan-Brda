{{-- Okruszki. Dane strukturalne BreadcrumbList generuje App\Seo z tej samej ścieżki. --}}
<nav aria-label="{{ __('Jesteś tutaj', 'przystan') }}" class="text-sm">
  <ol class="flex flex-wrap items-center gap-2 text-granat/80">
    <li><a href="{{ $urlGlowna }}">{{ __('Strona główna', 'przystan') }}</a></li>
    @foreach ($sciezka as $element)
      <li aria-hidden="true">/</li>
      <li>
        @if ($element['url'])
          <a href="{{ $element['url'] }}">{{ $element['nazwa'] }}</a>
        @else
          <span aria-current="page" class="font-semibold text-granat">{{ $element['nazwa'] }}</span>
        @endif
      </li>
    @endforeach
  </ol>
</nav>
