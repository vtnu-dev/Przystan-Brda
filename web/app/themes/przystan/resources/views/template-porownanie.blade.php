{{--
  Template Name: Ulubione i porównanie
--}}
@extends('layouts.app')

@section('content')
  @while (have_posts())
    @php the_post(); @endphp
    <section class="kontener pb-20 pt-12 md:pt-16">
      <p class="nadtytul">{{ __('Bez logowania, zapisane w tej przeglądarce', 'przystan') }}</p>
      <h1 class="h2">{{ get_the_title() }}</h1>
      <div class="tresc mt-4 max-w-2xl text-lg text-granat/85">@php the_content(); @endphp</div>

      {{-- Treść buduje modules/porownanie.js z REST API (GET /przystan/v1/mieszkania?ids[]=…). --}}
      <div class="mt-10" data-porownanie data-rest="{{ rest_url('przystan/v1/mieszkania') }}" data-jezyk="{{ $jezyk }}"
           data-etykiety="{{ wp_json_encode([
             'inwestycja' => __('Inwestycja', 'przystan'),
             'pietro' => __('Piętro', 'przystan'),
             'pokoje' => __('Pokoje', 'przystan'),
             'metraz' => __('Metraż', 'przystan'),
             'balkon' => __('Balkon / taras', 'przystan'),
             'widok' => __('Widok na rzekę', 'przystan'),
             'ogrodek' => __('Ogródek', 'przystan'),
             'cena' => __('Cena', 'przystan'),
             'cena_m2' => __('Cena za m²', 'przystan'),
             'status' => __('Status', 'przystan'),
             'tak' => __('tak', 'przystan'),
             'nie' => __('nie', 'przystan'),
             'zobacz' => __('Zobacz kartę', 'przystan'),
             'usun' => __('Usuń z ulubionych', 'przystan'),
             'podpis' => __('Porównanie ulubionych mieszkań', 'przystan'),
           ]) }}">
        <p class="text-granat/80" data-pusto>
          {{ __('Nie masz jeszcze ulubionych mieszkań. Na karcie mieszkania kliknij „Ulubione”, a pojawi się tutaj.', 'przystan') }}
          <a href="{{ $urlMieszkania }}">{{ __('Przejdź do wyszukiwarki', 'przystan') }}</a>
        </p>
        <noscript><p class="mt-4">{{ __('Ulubione działają po włączeniu JavaScriptu.', 'przystan') }}</p></noscript>
      </div>
    </section>
  @endwhile
@endsection
