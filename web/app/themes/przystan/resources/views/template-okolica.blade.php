{{--
  Template Name: Okolica i standard
--}}
@extends('layouts.app')

@php
  $pole = fn (string $n) => function_exists('get_field') ? get_field($n) : null;
  $punkty = [];
  for ($i = 1; $i <= 6; $i++) {
    if ($pole("punkt_{$i}_nazwa")) {
      $punkty[] = ['nazwa' => $pole("punkt_{$i}_nazwa"), 'minuty' => (int) $pole("punkt_{$i}_minuty"), 'jak' => $pole("punkt_{$i}_jak")];
    }
  }
  $standard = array_filter(array_map('trim', preg_split('/\R/', (string) $pole('standard_lista'))));
  $zdjecia = array_filter([(int) $pole('zdjecie_1'), (int) $pole('zdjecie_2'), (int) $pole('zdjecie_3')]);
  $jak = ['pieszo' => __('pieszo', 'przystan'), 'rowerem' => __('rowerem', 'przystan'), 'tramwajem' => __('tramwajem', 'przystan')];
@endphp

@section('content')
  @while (have_posts())
    @php the_post(); @endphp
    <section class="pb-12 pt-12 md:pt-16">
      <div class="kontener">
        <p class="nadtytul">{{ __('Okole, Bydgoszcz', 'przystan') }}</p>
        <h1 class="h2 max-w-3xl">{{ get_the_title() }}</h1>
        @if ($pole('wstep'))
          <p class="mt-5 max-w-2xl text-lg text-granat/85">{{ $pole('wstep') }}</p>
        @endif
      </div>
    </section>

    @if ($punkty)
      <section class="pb-16" aria-labelledby="punkty-tytul">
        <div class="kontener">
          <h2 id="punkty-tytul" class="sr-only">{{ __('Czas dojścia', 'przystan') }}</h2>
          <ol class="grid gap-px overflow-hidden rounded-xl bg-piasek-ciemny sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($punkty as $punkt)
              <li class="flex items-baseline justify-between gap-4 bg-white p-6">
                <span class="text-lg">{{ $punkt['nazwa'] }}</span>
                <span class="shrink-0 text-right">
                  <span class="font-serif text-4xl text-morze">{{ $punkt['minuty'] }}</span>
                  <span class="block text-sm text-granat/75">{{ __('min', 'przystan') }} {{ $jak[$punkt['jak']] ?? '' }}</span>
                </span>
              </li>
            @endforeach
          </ol>
        </div>
      </section>
    @endif

    @if (get_the_content())
      <section class="pb-16">
        <div class="kontener tresc max-w-3xl text-lg">@php the_content(); @endphp</div>
      </section>
    @endif

    <section class="bg-white py-16 md:py-20" aria-labelledby="standard-tytul">
      <div class="kontener grid gap-12 lg:grid-cols-2">
        <div>
          <p class="nadtytul">{{ __('Standard', 'przystan') }}</p>
          <h2 id="standard-tytul" class="h2">{{ $pole('standard_naglowek') }}</h2>
          @if ($standard)
            <ul class="mt-8 space-y-3">
              @foreach ($standard as $pozycja)
                <li class="flex gap-3"><span class="mt-3 h-px w-5 shrink-0 bg-morze" aria-hidden="true"></span>{{ $pozycja }}</li>
              @endforeach
            </ul>
          @endif
        </div>
        @if ($zdjecia)
          <div class="grid grid-cols-2 gap-4">
            @foreach ($zdjecia as $i => $zdjecie)
              {!! wp_get_attachment_image($zdjecie, 'karta', false, [
                'class' => 'w-full object-cover ' . ($loop->first ? 'col-span-2 aspect-[16/9] rounded-[var(--radius-fala)]' : 'aspect-square rounded-lg'),
                'sizes' => $loop->first ? '(min-width: 1024px) 45vw, 100vw' : '(min-width: 1024px) 22vw, 50vw',
                'loading' => 'lazy',
              ]) !!}
            @endforeach
          </div>
        @endif
      </div>
    </section>
  @endwhile

  @include('partials.cta-kontakt')
@endsection
