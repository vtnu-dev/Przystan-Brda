@extends('layouts.app')

@section('content')
  <section class="pb-10 pt-12 md:pt-16">
    <div class="kontener">
      <p class="nadtytul">{{ __('Przystań, deweloper z Bydgoszczy', 'przystan') }}</p>
      <h1 class="h2">{{ __('Inwestycje', 'przystan') }}</h1>
      <p class="mt-4 max-w-2xl text-lg text-granat/85">{{ __('Budujemy kameralnie: kilka budynków naraz, każdy w miejscu, w którym sami chcielibyśmy mieszkać. Nad wodą, w zieleni, blisko centrum.', 'przystan') }}</p>
    </div>
  </section>

  <div class="kontener grid gap-8 pb-20 sm:grid-cols-2 lg:grid-cols-3">
    @foreach ($inwestycje as $i)
      <div data-odslon>@include('partials.karta-inwestycji', ['i' => $i, 'poziom' => 'h2'])</div>
    @endforeach
  </div>

  @include('partials.cta-kontakt')
@endsection
