@extends('layouts.app')

@section('content')
  {{-- Nagłówek inwestycji ze zdjęciem --}}
  <section class="relative isolate overflow-hidden bg-granat text-tlo">
    @if ($inw['zdjecie'])
      {!! wp_get_attachment_image($inw['zdjecie'], 'szeroki', false, [
        'class' => 'absolute inset-0 -z-10 h-full w-full object-cover',
        'sizes' => '100vw',
        'loading' => 'eager',
        'fetchpriority' => 'high',
        'alt' => '',
      ]) !!}
    @endif
    <div class="absolute inset-0 -z-10 bg-gradient-to-t from-granat/90 via-granat/45 to-granat/10" aria-hidden="true"></div>
    <div class="kontener flex min-h-[min(62svh,36rem)] flex-col justify-end pb-12 pt-24">
      <nav aria-label="{{ __('Jesteś tutaj', 'przystan') }}" class="text-sm">
        <ol class="flex flex-wrap items-center gap-2 text-tlo/85">
          <li><a class="text-tlo" href="{{ $urlGlowna }}">{{ __('Strona główna', 'przystan') }}</a></li>
          <li aria-hidden="true">/</li>
          <li><a class="text-tlo" href="{{ get_post_type_archive_link('inwestycja') }}">{{ __('Inwestycje', 'przystan') }}</a></li>
          <li aria-hidden="true">/</li>
          <li><span aria-current="page" class="font-semibold">{{ $inw['nazwa'] }}</span></li>
        </ol>
      </nav>
      <p class="nadtytul mt-6 !text-morze-jasne">{{ $inw['status_etykieta'] }} · {{ $inw['lokalizacja'] }}</p>
      <h1 class="text-tlo" style="font-size: var(--text-hero); view-transition-name: tytul-inwestycji">{{ $inw['nazwa'] }}</h1>
      @if ($inw['zajawka'])
        <p class="mt-4 max-w-2xl text-lg text-tlo/90">{{ $inw['zajawka'] }}</p>
      @endif
    </div>
  </section>

  <section class="bg-tlo" aria-label="{{ __('Najważniejsze informacje', 'przystan') }}">
    <dl class="kontener grid grid-cols-2 gap-x-6 gap-y-8 py-12 md:grid-cols-4">
      <div class="flex flex-col border-l-2 border-morze pl-5"><dt class="order-2 mt-1 text-granat/80">{{ __('status', 'przystan') }}</dt><dd class="order-1 font-serif text-3xl">{{ $inw['status_etykieta'] }}</dd></div>
      @if ($inw['mieszkan'] > 0)
        <div class="flex flex-col border-l-2 border-morze pl-5"><dt class="order-2 mt-1 text-granat/80">{{ __('mieszkań', 'przystan') }}</dt><dd class="order-1 font-serif text-3xl">{{ $inw['mieszkan'] }}</dd></div>
        <div class="flex flex-col border-l-2 border-morze pl-5"><dt class="order-2 mt-1 text-granat/80">{{ __('wolnych', 'przystan') }}</dt><dd class="order-1 font-serif text-3xl">{{ $inw['wolnych'] }}</dd></div>
      @endif
      @if ($inw['termin'])
        <div class="flex flex-col border-l-2 border-morze pl-5"><dt class="order-2 mt-1 text-granat/80">{{ $inw['status'] === 'planowana' ? __('start sprzedaży', 'przystan') : __('termin oddania', 'przystan') }}</dt><dd class="order-1 font-serif text-3xl">{{ $inw['termin'] }}</dd></div>
      @endif
    </dl>
  </section>

  <div class="kontener grid gap-14 pb-16 pt-6 lg:grid-cols-[1.3fr_1fr]">
    <div class="tresc text-lg">@php the_content(); @endphp</div>

    @if ($inw['etapy'])
      <section aria-labelledby="harmonogram-tytul">
        <h2 id="harmonogram-tytul" class="text-3xl">{{ __('Harmonogram', 'przystan') }}</h2>
        @include('partials.harmonogram', ['etapy' => $inw['etapy']])
      </section>
    @endif
  </div>

  @if ($mieszkania)
    <section class="bg-white py-16" aria-labelledby="elewacja-tytul">
      <div class="kontener">
        <div class="flex flex-wrap items-end justify-between gap-4">
          <h2 id="elewacja-tytul" class="h2">{{ __('Wskaż okno, które Ci się podoba', 'przystan') }}</h2>
          <a href="{{ add_query_arg('inwestycja', $inw['id'], $urlMieszkania) }}" class="przycisk">{{ __('Wyszukiwarka z filtrami', 'przystan') }}</a>
        </div>
        <div class="mt-8">
          @include('components.elewacja', [
            'mieszkania' => $mieszkania, 'pasujace' => null, 'id' => 'elewacja', 'e' => $elewacja,
            'woda' => $inw['nad_woda'], 'podpis' => $inw['podpis_elewacji'], 'nazwa' => $inw['nazwa'],
          ])
        </div>
      </div>
    </section>
  @endif

  @if ($inw['status'] === 'planowana')
    <section id="formularz" class="kontener scroll-mt-28 py-16" aria-labelledby="formularz-tytul">
      <div class="rounded-xl bg-piasek p-6 md:p-10">
        <h2 id="formularz-tytul" class="text-3xl">{{ __('Powiadom mnie o starcie sprzedaży', 'przystan') }}</h2>
        <p class="mt-3 max-w-2xl text-granat/85">{{ __('Zapisani dostają rzuty i ceny kilka dni przed publikacją. Jedna wiadomość na start sprzedaży, bez newslettera.', 'przystan') }}</p>
        @include('partials.formularz', ['inwestycjaId' => $inw['id'], 'rodzaj' => 'powiadomienie'])
      </div>
    </section>
  @endif

  @if ($wpisy)
    <section class="py-16" aria-labelledby="dziennik-tytul">
      <div class="kontener">
        <h2 id="dziennik-tytul" class="h2">{{ __('Z placu budowy', 'przystan') }}</h2>
        <div class="mt-10 grid gap-8 md:grid-cols-3">
          @foreach ($wpisy as $wpis)
            @include('partials.karta-wpisu', ['wpis' => $wpis, 'poziom' => 'h3'])
          @endforeach
        </div>
      </div>
    </section>
  @endif

  @if ($inne)
    <section class="bg-piasek py-16" aria-labelledby="inne-tytul">
      <div class="kontener">
        <h2 id="inne-tytul" class="h2">{{ __('Inne inwestycje', 'przystan') }}</h2>
        <div class="mt-10 grid gap-8 md:grid-cols-2">
          @foreach ($inne as $i)
            @include('partials.karta-inwestycji', ['i' => $i])
          @endforeach
        </div>
      </div>
    </section>
  @endif
@endsection
