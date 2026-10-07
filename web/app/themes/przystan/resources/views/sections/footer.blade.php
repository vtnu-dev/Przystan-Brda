<footer class="mt-auto bg-granat text-tlo">
  @include('partials.linie-wody', ['klasa' => 'h-12 text-morze-jasne -translate-y-px'])

  <div class="kontener grid gap-10 pb-10 pt-8 md:grid-cols-[1.4fr_1fr_1fr]">
    <div>
      <a href="{{ $urlGlowna }}" class="flex items-center gap-3 text-tlo no-underline hover:text-piasek">
        @include('partials.znak', ['klasa' => 'h-9 w-9 text-morze-jasne'])
        <span class="font-serif text-3xl leading-none">{!! $siteName !!}</span>
      </a>
      @if ($kontakt['termin'])
        <p class="mt-4 text-tlo/85">{{ __('Termin oddania:', 'przystan') }} <strong class="text-tlo">{{ $kontakt['termin'] }}</strong></p>
      @endif
    </div>

    <div>
      <h2 class="mb-3 font-sans text-sm font-semibold uppercase tracking-[0.14em] text-morze-jasne">{{ __('Biuro sprzedaży', 'przystan') }}</h2>
      <address class="not-italic leading-relaxed text-tlo/90">
        {!! nl2br(e($kontakt['adres'])) !!}<br>
        @if ($kontakt['telefon'])
          <a class="text-tlo hover:text-piasek" href="{{ $kontakt['telefon_href'] }}">{{ $kontakt['telefon'] }}</a><br>
        @endif
        @if ($kontakt['email'])
          <a class="text-tlo hover:text-piasek" href="mailto:{{ $kontakt['email'] }}">{{ $kontakt['email'] }}</a>
        @endif
      </address>
    </div>

    @if (has_nav_menu('stopka'))
      <nav aria-label="{{ __('Menu w stopce', 'przystan') }}">
        <h2 class="mb-3 font-sans text-sm font-semibold uppercase tracking-[0.14em] text-morze-jasne">{{ __('Na stronie', 'przystan') }}</h2>
        {!! wp_nav_menu([
          'theme_location' => 'stopka',
          'container' => false,
          'menu_class' => 'menu-stopka space-y-1',
          'echo' => false,
          'depth' => 1,
        ]) !!}
      </nav>
    @endif
  </div>

  <div class="border-t border-tlo/15">
    <div class="kontener flex flex-col gap-2 py-5 text-sm text-tlo/80 md:flex-row md:items-center md:justify-between">
      <p>
        <strong class="font-semibold text-tlo">{{ __('Strona demonstracyjna.', 'przystan') }}</strong>
        {{ __('Przystań Brda to fikcyjna inwestycja: mieszkania, ceny i dane kontaktowe są przykładowe.', 'przystan') }}
      </p>
      <p>
        {{ __('Projekt i wdrożenie:', 'przystan') }}
        <a class="font-semibold text-tlo hover:text-piasek" href="https://sitebest.eu">SiteBest</a>
      </p>
    </div>
  </div>
</footer>
