@extends('layouts.app')

@section('content')
  <section class="kontener max-w-3xl py-20 md:py-28">
    <p class="nadtytul">{{ __('Błąd 404', 'przystan') }}</p>
    <h1 class="h2">{{ __('Tej strony nie ma. Rzeka płynie dalej.', 'przystan') }}</h1>
    <p class="mt-5 text-lg text-granat/85">{{ __('Adres mógł się zmienić albo mieszkanie zostało usunięte z oferty. Zacznij od wyszukiwarki albo strony głównej.', 'przystan') }}</p>
    <div class="mt-8 flex flex-wrap gap-3">
      <a href="{{ $urlMieszkania }}" class="przycisk">{{ __('Znajdź mieszkanie', 'przystan') }}</a>
      <a href="{{ $urlGlowna }}" class="przycisk przycisk--obrys">{{ __('Strona główna', 'przystan') }}</a>
    </div>
    @include('partials.linie-wody', ['klasa' => 'mt-16 h-14 text-morze linie-wody--ruch'])
  </section>
@endsection
