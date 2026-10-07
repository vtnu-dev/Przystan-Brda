@extends('layouts.app')

@section('content')
  {{-- Hero: zdjęcie budynku nad rzeką o zmierzchu --}}
  <section class="relative isolate overflow-hidden bg-granat text-tlo" aria-labelledby="hero-tytul" data-hero>
    @if ($hero['zdjecie'])
      {!! wp_get_attachment_image($hero['zdjecie'], 'hero', false, [
        'class' => 'absolute inset-0 -z-10 h-full w-full object-cover object-[60%_center]',
        'sizes' => '100vw',
        'loading' => 'eager',
        'fetchpriority' => 'high',
        'decoding' => 'async',
      ]) !!}
    @endif
    {{-- B: falujące odbicie w rzece (canvas liczony w Workerze, modules/hero.js); bez JS niewidoczny. --}}
    <canvas class="pointer-events-none absolute inset-0 -z-10 h-full w-full opacity-0 transition-opacity duration-700" data-odbicie data-woda="0.69" data-pozycja="0.6" aria-hidden="true"></canvas>
    {{-- A: rysunek architekta, który zamienia się w zdjęcie (sam CSS, patrz .hero-szkic w app.css). --}}
    @include('partials.hero-szkic')
    <div class="absolute inset-0 -z-10 bg-gradient-to-r from-granat/90 via-granat/60 to-granat/0" aria-hidden="true"></div>
    <div class="absolute inset-x-0 bottom-0 -z-10 h-1/3 bg-gradient-to-t from-granat/70 to-transparent" aria-hidden="true"></div>

    <div class="kontener flex min-h-[min(78svh,46rem)] flex-col justify-end pb-16 pt-28 md:min-h-[min(86svh,52rem)] md:justify-center md:pb-24">
      @if ($hero['nadtytul'])
        <p class="nadtytul !text-morze-jasne">{{ $hero['nadtytul'] }}</p>
      @endif
      <h1 id="hero-tytul" class="max-w-3xl text-tlo" style="font-size: var(--text-hero)">
        @foreach (preg_split('/\R/', $hero['naglowek']) as $i => $linia)
          @if ($i > 0)<br>@endif
          @if ($i === 1)<em class="font-normal text-piasek">{{ $linia }}</em>@else{{ $linia }}@endif
        @endforeach
      </h1>
      @if ($hero['wstep'])
        <p class="mt-6 max-w-xl text-lg text-tlo/90 md:text-xl">{{ $hero['wstep'] }}</p>
      @endif
      <div class="mt-9 flex flex-wrap gap-3">
        <a href="{{ $urlMieszkania }}" class="przycisk przycisk--jasny">
          {{ __('Znajdź mieszkanie', 'przystan') }}
          <span class="text-morze">{{ sprintf(_n('%d wolne', '%d wolnych', $wolnych, 'przystan'), $wolnych) }}</span>
        </a>
        <a href="{{ $urlOkolica }}" class="przycisk border-2 border-tlo/60 bg-transparent text-tlo hover:border-tlo hover:bg-tlo/10">{{ __('Okolica i standard', 'przystan') }}</a>
      </div>
      @if ($kontakt['termin'])
        <p class="mt-10 text-sm text-tlo/80">{{ __('Termin oddania:', 'przystan') }} <strong class="text-tlo">{{ $kontakt['termin'] }}</strong></p>
      @endif
    </div>
    <button type="button" hidden data-ruch-pauza aria-pressed="false"
            data-pauza="{{ __('Zatrzymaj ruch wody', 'przystan') }}" data-wznow="{{ __('Wznów ruch wody', 'przystan') }}"
            class="absolute bottom-4 right-4 inline-flex min-h-11 items-center gap-2 rounded-full bg-granat/60 px-4 text-sm font-semibold text-tlo backdrop-blur hover:bg-granat/80 md:bottom-6 md:right-8">
      <svg class="size-4" viewBox="0 0 16 16" aria-hidden="true"><path d="M5 3v10M11 3v10" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" data-ikona-pauza/><path d="M5 3l8 5-8 5z" fill="currentColor" hidden data-ikona-wznow/></svg>
      <span>{{ __('Zatrzymaj ruch wody', 'przystan') }}</span>
    </button>
  </section>

  {{-- Liczby --}}
  @if ($liczby)
    <section class="bg-tlo" aria-label="{{ __('Inwestycja w liczbach', 'przystan') }}">
      <dl class="kontener grid grid-cols-2 gap-x-6 gap-y-8 py-14 md:grid-cols-4 md:py-16">
        @foreach ($liczby as $liczba)
          <div class="flex flex-col border-l-2 border-morze pl-5">
            <dt class="order-2 mt-1 text-granat/80">{{ $liczba['opis'] }}</dt>
            <dd class="order-1 font-serif text-5xl leading-none text-granat">{{ $liczba['wartosc'] }}</dd>
          </div>
        @endforeach
      </dl>
    </section>
  @endif

  {{-- Elewacja --}}
  <section class="bg-white py-16 md:py-24" aria-labelledby="wybierz-tytul">
    <div class="kontener grid gap-10 lg:grid-cols-[1fr_2.2fr] lg:items-center">
      <div>
        <p class="nadtytul">{{ __('Mieszkania', 'przystan') }}</p>
        <h2 id="wybierz-tytul" class="h2">{{ __('Wskaż okno, które Ci się podoba', 'przystan') }}</h2>
        <p class="mt-5 text-lg text-granat/85">{{ __('Każdy prostokąt to jedno mieszkanie. Kolor pokazuje, czy jest wolne. Kliknij, żeby zobaczyć rzut, metraż i cenę.', 'przystan') }}</p>
        <a href="{{ $urlMieszkania }}" class="przycisk mt-8">{{ __('Wyszukiwarka z filtrami', 'przystan') }}</a>
      </div>
      @include('components.elewacja', [
        'mieszkania' => $mieszkania, 'pasujace' => null, 'id' => 'elewacja-glowna', 'e' => $elewacja,
        'woda' => $flagowa['nad_woda'] ?? true, 'podpis' => $flagowa['podpis_elewacji'] ?? null, 'nazwa' => $flagowa['nazwa'] ?? null,
      ])
    </div>
  </section>

  {{-- Inwestycje --}}
  @if (count($inwestycje) > 1)
    <section class="bg-piasek py-16 md:py-24" aria-labelledby="inwestycje-tytul">
      <div class="kontener">
        <div class="flex flex-wrap items-end justify-between gap-4">
          <div>
            <p class="nadtytul">{{ __('Inwestycje', 'przystan') }}</p>
            <h2 id="inwestycje-tytul" class="h2">{{ __('Trzy adresy, jeden sposób budowania', 'przystan') }}</h2>
          </div>
          <a href="{{ get_post_type_archive_link('inwestycja') }}" class="font-semibold">{{ __('Wszystkie inwestycje', 'przystan') }}</a>
        </div>
        <div class="mt-10 grid gap-8 md:grid-cols-3">
          @foreach ($inwestycje as $i)
            <div data-odslon style="--opoznienie: {{ $loop->index * 90 }}ms">@include('partials.karta-inwestycji', ['i' => $i])</div>
          @endforeach
        </div>
      </div>
    </section>
  @endif

  {{-- Atuty --}}
  @if ($atuty)
    <section class="py-16 md:py-24" aria-labelledby="atuty-tytul">
      <div class="kontener">
        <p class="nadtytul">{{ __('Dlaczego tutaj', 'przystan') }}</p>
        <h2 id="atuty-tytul" class="h2 max-w-2xl">{{ __('Spokojne mieszkania z widokiem, którego nikt nie zabuduje', 'przystan') }}</h2>
        <div class="mt-12 grid gap-10 md:grid-cols-3">
          @foreach ($atuty as $atut)
            <article>
              @if ($atut['zdjecie'])
                {!! wp_get_attachment_image($atut['zdjecie'], 'karta', false, [
                  'class' => 'aspect-[4/3] w-full object-cover ' . ($loop->first ? 'rounded-[var(--radius-fala)]' : 'rounded-lg'),
                  'sizes' => '(min-width: 768px) 30vw, 100vw',
                  'loading' => 'lazy',
                ]) !!}
              @endif
              <h3 class="mt-6" style="font-size: var(--text-h3)">{{ $atut['tytul'] }}</h3>
              <p class="mt-3 text-granat/85">{{ $atut['opis'] }}</p>
            </article>
          @endforeach
        </div>
      </div>
    </section>
  @endif

  {{-- Okolica --}}
  <section class="bg-piasek py-16 md:py-24" aria-labelledby="okolica-tytul">
    <div class="kontener grid items-center gap-10 md:grid-cols-2 md:gap-16">
      @if ($okolica['zdjecie'])
        {!! wp_get_attachment_image($okolica['zdjecie'], 'karta', false, [
          'class' => 'aspect-[4/3] w-full rounded-[var(--radius-fala)] object-cover',
          'sizes' => '(min-width: 768px) 45vw, 100vw',
          'loading' => 'lazy',
        ]) !!}
      @endif
      <div>
        <p class="nadtytul">{{ __('Okolica', 'przystan') }}</p>
        <h2 id="okolica-tytul" class="h2">{{ $okolica['naglowek'] }}</h2>
        <p class="mt-5 text-lg text-granat/85">{{ $okolica['wstep'] }}</p>
        <a href="{{ $urlOkolica }}" class="przycisk przycisk--obrys mt-8">{{ __('Okolica i standard wykończenia', 'przystan') }}</a>
      </div>
    </div>
  </section>

  {{-- Dziennik budowy --}}
  @if ($wpisy)
    <section class="py-16 md:py-24" aria-labelledby="dziennik-tytul">
      <div class="kontener">
        <div class="flex flex-wrap items-end justify-between gap-4">
          <div>
            <p class="nadtytul">{{ __('Dziennik budowy', 'przystan') }}</p>
            <h2 id="dziennik-tytul" class="h2">{{ __('Co nowego na placu budowy', 'przystan') }}</h2>
          </div>
          @if (get_option('page_for_posts'))
            <a href="{{ get_permalink((int) get_option('page_for_posts')) }}" class="font-semibold">{{ __('Wszystkie wpisy', 'przystan') }}</a>
          @endif
        </div>
        <div class="mt-10 grid gap-8 md:grid-cols-3">
          @foreach ($wpisy as $wpis)
            @include('partials.karta-wpisu', ['wpis' => $wpis, 'poziom' => 'h3'])
          @endforeach
        </div>
      </div>
    </section>
  @endif

  @include('partials.cta-kontakt')
@endsection
