{{-- Lista wpisów = „Dziennik budowy” (strona wpisów ustawiona w Ustawienia → Czytanie). --}}
@extends('layouts.app')

@section('content')
  <section class="pb-8 pt-12 md:pt-16">
    <div class="kontener">
      <p class="nadtytul">{{ __('Na bieżąco', 'przystan') }}</p>
      <h1 class="h2">{{ is_home() && get_option('page_for_posts') ? get_the_title((int) get_option('page_for_posts')) : wp_strip_all_tags(get_the_archive_title()) }}</h1>
      <p class="mt-4 max-w-2xl text-lg text-granat/85">{{ __('Postęp prac co kilka tygodni: zdjęcia z placu budowy i najważniejsze etapy.', 'przystan') }}</p>
    </div>
  </section>

  <div class="kontener pb-20">
    @if (have_posts())
      <div class="grid gap-x-8 gap-y-14 md:grid-cols-3">
        @while (have_posts())
          @php(the_post())
          @include('partials.karta-wpisu', ['wpis' => get_post(), 'poziom' => 'h2'])
        @endwhile
      </div>
      <div class="mt-14">
        {!! get_the_posts_navigation(['prev_text' => __('Starsze wpisy', 'przystan'), 'next_text' => __('Nowsze wpisy', 'przystan')]) !!}
      </div>
    @else
      <p>{{ __('Nie ma jeszcze wpisów.', 'przystan') }}</p>
    @endif
  </div>
@endsection
