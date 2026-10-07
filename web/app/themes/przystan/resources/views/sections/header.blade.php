<header class="sticky top-0 z-40 bg-granat text-tlo" data-naglowek>
  <div class="kontener flex min-h-18 items-center justify-between gap-6 py-3">
    <a href="{{ $urlGlowna }}" class="flex items-center gap-3 text-tlo no-underline hover:text-piasek" @if (is_front_page()) aria-current="page" @endif>
      @include('partials.znak', ['klasa' => 'h-8 w-8 text-morze-jasne'])
      <span class="font-serif text-2xl leading-none tracking-tight">{!! $siteName !!}</span>
    </a>

    <button type="button"
            class="inline-flex min-h-11 min-w-11 items-center justify-center gap-2 rounded-full border border-tlo/40 px-4 text-sm font-semibold lg:hidden"
            aria-expanded="false" aria-controls="menu-glowne" data-menu-przycisk>
      <svg class="size-5" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 7h18M3 12h18M3 17h18" stroke="currentColor" stroke-width="2" stroke-linecap="round" fill="none"/></svg>
      <span>{{ __('Menu', 'przystan') }}</span>
    </button>

    <nav id="menu-glowne" aria-label="{{ __('Menu główne', 'przystan') }}"
         class="absolute inset-x-0 top-full hidden border-t border-tlo/15 bg-granat pb-6 lg:static lg:block lg:border-0 lg:pb-0" data-menu>
      <div class="kontener flex flex-col gap-1 lg:flex-row lg:items-center lg:gap-2 lg:px-0">
        @if (has_nav_menu('glowne'))
          {!! wp_nav_menu([
            'theme_location' => 'glowne',
            'container' => false,
            'menu_class' => 'menu-glowne flex flex-col lg:flex-row lg:items-center lg:gap-1',
            'echo' => false,
            'depth' => 1,
          ]) !!}
        @endif

        @if (count($jezyki) > 1)
          <ul class="mt-3 flex gap-1 lg:ml-3 lg:mt-0" aria-label="{{ __('Język', 'przystan') }}">
            @foreach ($jezyki as $j)
              <li>
                <a href="{{ $j['url'] }}" lang="{{ $j['locale'] }}" hreflang="{{ $j['locale'] }}"
                   class="inline-flex min-h-11 min-w-11 items-center justify-center rounded-full px-3 text-sm font-semibold no-underline {{ $j['aktywny'] ? 'bg-tlo text-granat hover:text-granat' : 'text-tlo hover:bg-granat-2 hover:text-tlo' }}"
                   @if ($j['aktywny']) aria-current="true" @endif
                   aria-label="{{ $j['nazwa'] }}">{{ $j['kod'] }}</a>
              </li>
            @endforeach
          </ul>
        @endif

        <a href="{{ $urlUlubione }}" class="relative inline-flex min-h-11 items-center gap-2 rounded-full px-4 font-semibold text-tlo no-underline hover:bg-granat-2 hover:text-tlo lg:ml-1" data-ulubione-link>
          <svg class="size-5" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 20s-7-4.4-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 10c0 5.6-7 10-7 10Z" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>
          <span>{{ __('Ulubione', 'przystan') }}</span>
          <span class="hidden min-w-6 rounded-full bg-morze-jasne px-1.5 text-center text-sm text-granat" data-ulubione-licznik></span>
        </a>

        <a href="{{ $urlMieszkania }}" class="przycisk przycisk--jasny mt-4 lg:ml-3 lg:mt-0 lg:min-h-11 lg:px-5">{{ __('Znajdź mieszkanie', 'przystan') }}</a>
      </div>
    </nav>
  </div>
</header>
