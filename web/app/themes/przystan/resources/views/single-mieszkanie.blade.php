@extends('layouts.app')

@section('content')
  <div class="kontener pb-20 pt-8">
    @include('partials.okruszki', ['sciezka' => [
      ['nazwa' => $inwestycja['nazwa'] ?? __('Mieszkania', 'przystan'), 'url' => $inwestycja ? add_query_arg('inwestycja', $inwestycja['id'], $urlMieszkania) : $urlMieszkania],
      ['nazwa' => $m['numer'], 'url' => ''],
    ]])

    <div class="mt-8 grid gap-12 lg:grid-cols-[1.25fr_1fr]">
      <div>
        <p class="nadtytul">{{ $inwestycja ? $inwestycja['nazwa'] . ' · ' : '' }}{{ $m['pietro_tekst'] }}</p>
        <h1 class="h2" style="view-transition-name: tytul-mieszkania">{{ sprintf(__('Mieszkanie %s', 'przystan'), $m['numer']) }}</h1>
        <div class="mt-3 flex flex-wrap items-center gap-4">
          <span class="status status--{{ $m['status'] }} text-base">{{ $m['status_etykieta'] }}</span>
          @include('partials.ulubione-przycisk', ['id' => $m['id_ulubione'], 'numer' => $m['numer'], 'tekst' => true, 'klasa' => 'inline-flex min-h-11 items-center gap-2 rounded-full border-2 border-morze px-4 text-sm font-semibold text-morze hover:bg-white aria-pressed:bg-morze aria-pressed:text-white'])
        </div>

        @if ($m['rzut_id'])
          <figure class="mt-8 rounded-xl bg-white p-4 md:p-8">
            {!! wp_get_attachment_image($m['rzut_id'], 'large', false, [
              'class' => 'mx-auto w-full max-w-2xl',
              'sizes' => '(min-width: 1024px) 55vw, 100vw',
              'loading' => 'eager',
              'fetchpriority' => 'high',
              'alt' => sprintf(__('Rzut mieszkania %1$s: %2$s, %3$s', 'przystan'), $m['numer'], $m['pokoje_tekst'], $m['metraz_tekst']),
            ]) !!}
            <figcaption class="mt-4 text-center text-sm text-granat/75">{{ __('Rzut poglądowy. Dokładne wymiary w karcie lokalu od doradcy.', 'przystan') }}</figcaption>
          </figure>
        @endif

        @if (get_the_content())
          <div class="tresc mt-10 max-w-2xl text-lg">@php(the_content())</div>
        @endif
      </div>

      <aside class="self-start lg:sticky lg:top-28" aria-labelledby="parametry-tytul">
        <div class="rounded-xl bg-granat p-6 text-tlo md:p-8">
          <h2 id="parametry-tytul" class="font-sans text-sm font-semibold uppercase tracking-[0.14em] text-morze-jasne">{{ __('Parametry', 'przystan') }}</h2>
          <dl class="mt-4 grid grid-cols-2 gap-x-4 gap-y-5">
            <div><dt class="text-sm text-tlo/75">{{ __('Metraż', 'przystan') }}</dt><dd class="font-serif text-3xl">{{ $m['metraz_tekst'] }}</dd></div>
            <div><dt class="text-sm text-tlo/75">{{ __('Pokoje', 'przystan') }}</dt><dd class="font-serif text-3xl">{{ $m['pokoje'] }}</dd></div>
            <div><dt class="text-sm text-tlo/75">{{ __('Piętro', 'przystan') }}</dt><dd class="text-lg">{{ $m['pietro_tekst'] }}</dd></div>
            <div><dt class="text-sm text-tlo/75">{{ __('Balkon / taras', 'przystan') }}</dt><dd class="text-lg">{{ $m['balkon_tekst'] }}</dd></div>
            <div><dt class="text-sm text-tlo/75">{{ __('Widok na rzekę', 'przystan') }}</dt><dd class="text-lg">{{ $m['widok_na_rzeke'] ? __('tak', 'przystan') : __('nie', 'przystan') }}</dd></div>
            <div><dt class="text-sm text-tlo/75">{{ __('Ogródek', 'przystan') }}</dt><dd class="text-lg">{{ $m['ogrodek'] ? __('tak', 'przystan') : __('nie', 'przystan') }}</dd></div>
          </dl>
          @if ($m['cena_tekst'])
            <p class="mt-6 border-t border-tlo/20 pt-5">
              <span class="block text-sm text-tlo/75">{{ __('Cena', 'przystan') }}</span>
              <span class="font-serif text-4xl">{{ $m['cena_tekst'] }}</span>
              @if ($m['cena'] > 0)
                <span class="mt-1 block text-sm text-tlo/75">{{ sprintf(__('%s zł/m²', 'przystan'), \Przystan\Mieszkania\Mieszkanie::liczba($m['cena'] / max(1, $m['metraz']))) }}</span>
              @endif
            </p>
          @endif
          @if ($m['status'] !== 'sprzedane')
            <a href="#formularz" class="przycisk przycisk--jasny mt-6 w-full">{{ __('Zapytaj o to mieszkanie', 'przystan') }}</a>
          @endif
          @if ($m['cena'] > 0)
            @include('partials.kalkulator', ['cena' => $m['cena']])
          @endif
        </div>
      </aside>
    </div>

    <section class="mt-16" aria-labelledby="polozenie-tytul">
      <h2 id="polozenie-tytul" class="text-3xl">{{ __('Położenie w budynku', 'przystan') }}</h2>
      <div class="mt-6 rounded-xl bg-white p-4 md:p-8">
        @include('components.elewacja', [
          'mieszkania' => $wszystkie, 'pasujace' => [$m['id']], 'id' => 'elewacja-karta', 'e' => $elewacja,
          'woda' => $inwestycja['nad_woda'] ?? true, 'podpis' => $inwestycja['podpis_elewacji'] ?? null, 'nazwa' => $inwestycja['nazwa'] ?? null,
        ])
      </div>
    </section>

    @if ($naPietrze)
      <section class="mt-16" aria-labelledby="pietro-tytul">
        <h2 id="pietro-tytul" class="text-3xl">{{ __('Inne mieszkania na tym piętrze', 'przystan') }}</h2>
        <ul class="mt-6 grid gap-4 sm:grid-cols-3">
          @foreach ($naPietrze as $inne)
            <li class="relative rounded-xl border border-piasek-ciemny bg-white p-5">
              <a href="{{ $inne['url'] }}" class="font-serif text-2xl text-granat no-underline after:absolute after:inset-0 hover:text-morze">{{ $inne['numer'] }}</a>
              <p class="mt-1">{{ $inne['pokoje_tekst'] }} · {{ $inne['metraz_tekst'] }}</p>
              <p class="mt-2"><span class="status status--{{ $inne['status'] }}">{{ $inne['status_etykieta'] }}</span></p>
            </li>
          @endforeach
        </ul>
      </section>
    @endif

    @if ($m['status'] !== 'sprzedane')
      <section id="formularz" class="mt-16 scroll-mt-28 rounded-xl bg-piasek p-6 md:p-10" aria-labelledby="formularz-tytul">
        <h2 id="formularz-tytul" class="text-3xl">{{ sprintf(__('Zapytaj o mieszkanie %s', 'przystan'), $m['numer']) }}</h2>
        <p class="mt-3 max-w-2xl text-granat/85">{{ __('Doradca odpowie w ciągu jednego dnia roboczego: prześle kartę lokalu, harmonogram płatności i zaproponuje termin spotkania.', 'przystan') }}</p>
        @include('partials.formularz', ['mieszkanieId' => $m['id']])
      </section>
    @endif
  </div>
@endsection
