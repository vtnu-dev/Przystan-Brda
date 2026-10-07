{{--
  Template Name: Kontakt
--}}
@extends('layouts.app')

@php
  $pole = fn (string $n) => function_exists('get_field') ? get_field($n) : null;
  $godziny = array_filter(array_map('trim', preg_split('/\R/', (string) $pole('godziny'))));
@endphp

@section('content')
  @while (have_posts())
    @php the_post(); @endphp
    <div class="kontener grid gap-12 pb-20 pt-12 md:pt-16 lg:grid-cols-[1fr_1.4fr] lg:gap-20">
      <div>
        <p class="nadtytul">{{ __('Biuro sprzedaży', 'przystan') }}</p>
        <h1 class="h2">{{ get_the_title() }}</h1>
        @if ($pole('wstep'))
          <p class="mt-5 text-lg text-granat/85">{{ $pole('wstep') }}</p>
        @endif

        <dl class="mt-10 space-y-6">
          @if ($kontakt['telefon'])
            <div><dt class="text-sm font-semibold uppercase tracking-wider text-granat/75">{{ __('Telefon', 'przystan') }}</dt>
              <dd class="mt-1 font-serif text-3xl"><a href="{{ $kontakt['telefon_href'] }}" class="text-granat no-underline hover:text-morze">{{ $kontakt['telefon'] }}</a></dd></div>
          @endif
          @if ($kontakt['email'])
            <div><dt class="text-sm font-semibold uppercase tracking-wider text-granat/75">{{ __('E-mail', 'przystan') }}</dt>
              <dd class="mt-1 text-xl"><a href="mailto:{{ $kontakt['email'] }}">{{ $kontakt['email'] }}</a></dd></div>
          @endif
          @if ($kontakt['adres'])
            <div><dt class="text-sm font-semibold uppercase tracking-wider text-granat/75">{{ __('Adres', 'przystan') }}</dt>
              <dd class="mt-1 text-lg"><address class="not-italic">{!! nl2br(e($kontakt['adres'])) !!}</address></dd></div>
          @endif
          @if ($godziny)
            <div><dt class="text-sm font-semibold uppercase tracking-wider text-granat/75">{{ __('Godziny otwarcia', 'przystan') }}</dt>
              <dd class="mt-1"><ul>@foreach ($godziny as $g)<li>{{ $g }}</li>@endforeach</ul></dd></div>
          @endif
        </dl>
      </div>

      <section id="formularz" class="scroll-mt-28 self-start rounded-xl bg-piasek p-6 md:p-10" aria-labelledby="formularz-tytul">
        <h2 id="formularz-tytul" class="text-3xl">{{ __('Napisz do nas', 'przystan') }}</h2>
        @include('partials.formularz')
      </section>
    </div>
  @endwhile
@endsection
