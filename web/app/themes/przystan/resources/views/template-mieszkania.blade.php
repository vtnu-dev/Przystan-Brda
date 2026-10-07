{{--
  Template Name: Mieszkania (wyszukiwarka)
--}}
@extends('layouts.app')

@section('content')
  @while (have_posts())
    @php(the_post())
    <section class="pb-6 pt-12 md:pt-16">
      <div class="kontener">
        <p class="nadtytul">{{ sprintf(_n('%d mieszkanie', '%d mieszkań', count($wszystkie), 'przystan'), count($wszystkie)) }}</p>
        <h1 class="h2">{{ get_the_title() }}</h1>
        @if (get_the_content())
          <div class="tresc mt-4 max-w-2xl text-lg text-granat/85">@php(the_content())</div>
        @endif
      </div>
    </section>
  @endwhile

  <div class="kontener grid gap-10 pb-20 lg:grid-cols-[19rem_1fr] lg:gap-14">
    {{-- Filtry: zwykły formularz GET (działa bez JS); JS przejmuje go i pyta REST API bez przeładowania strony. --}}
    <form method="get" action="{{ get_permalink() }}" class="self-start rounded-xl bg-white p-6 lg:sticky lg:top-28"
          data-wyszukiwarka data-rest="{{ $restUrl }}" data-jezyk="{{ $jezyk }}" aria-labelledby="filtry-tytul">
      <h2 id="filtry-tytul" class="font-sans text-lg font-semibold">{{ __('Filtry', 'przystan') }}</h2>

      <fieldset class="pole mt-5">
        <legend>{{ __('Pokoje', 'przystan') }}</legend>
        <div class="flex flex-wrap gap-2">
          @foreach ([1, 2, 3, 4] as $p)
            <label class="chip">
              <input type="checkbox" name="pokoje[]" value="{{ $p }}" @checked(in_array($p, $filtry['pokoje'] ?? [], true))>
              <span>{{ $p === 4 ? '4' : $p }}<span class="sr-only">&nbsp;{{ _n('pokój', 'pokoje', $p, 'przystan') }}</span></span>
            </label>
          @endforeach
        </div>
      </fieldset>

      <div class="pole mt-5">
        <label for="f-pietro">{{ __('Piętro', 'przystan') }}</label>
        <select id="f-pietro" name="pietro">
          <option value="">{{ __('dowolne', 'przystan') }}</option>
          @for ($i = 0; $i <= 5; $i++)
            <option value="{{ $i }}" @selected(($filtry['pietro'] ?? null) === $i)>{{ \Przystan\Mieszkania\Mieszkanie::nazwaPietra($i) }}</option>
          @endfor
        </select>
      </div>

      <fieldset class="pole mt-5">
        <legend>{{ __('Metraż (m²)', 'przystan') }}</legend>
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label for="f-min" class="!font-normal">{{ __('od', 'przystan') }}</label>
            <input id="f-min" type="number" name="metraz_min" inputmode="numeric" min="20" max="120" step="1" value="{{ isset($filtry['metraz_min']) ? (int) $filtry['metraz_min'] : '' }}" placeholder="30">
          </div>
          <div>
            <label for="f-max" class="!font-normal">{{ __('do', 'przystan') }}</label>
            <input id="f-max" type="number" name="metraz_max" inputmode="numeric" min="20" max="120" step="1" value="{{ isset($filtry['metraz_max']) ? (int) $filtry['metraz_max'] : '' }}" placeholder="90">
          </div>
        </div>
      </fieldset>

      <div class="pole mt-5">
        <label for="f-status">{{ __('Status', 'przystan') }}</label>
        <select id="f-status" name="status">
          <option value="wszystkie">{{ __('wszystkie', 'przystan') }}</option>
          @foreach (\Przystan\Mieszkania\Filtry::STATUSY as $s)
            <option value="{{ $s }}" @selected(($filtry['status'] ?? '') === $s)>{{ \Przystan\Mieszkania\Mieszkanie::etykietaStatusu($s) }}</option>
          @endforeach
        </select>
      </div>

      <div class="mt-5 space-y-3">
        <label class="flex min-h-11 cursor-pointer items-center gap-3">
          <input type="checkbox" name="widok" value="1" class="size-5 accent-morze" @checked(! empty($filtry['widok']))>
          {{ __('widok na rzekę', 'przystan') }}
        </label>
        <label class="flex min-h-11 cursor-pointer items-center gap-3">
          <input type="checkbox" name="balkon" value="1" class="size-5 accent-morze" @checked(! empty($filtry['balkon']))>
          {{ __('balkon lub taras', 'przystan') }}
        </label>
      </div>

      <div class="mt-6 flex flex-wrap items-center gap-4">
        <button type="submit" class="przycisk" data-pokaz>{{ __('Pokaż mieszkania', 'przystan') }}</button>
        <a href="{{ get_permalink() }}" class="text-sm font-semibold" data-wyczysc>{{ __('Wyczyść filtry', 'przystan') }}</a>
      </div>
    </form>

    <div>
      @include('components.elewacja', ['mieszkania' => $wszystkie, 'pasujace' => $pasujace, 'id' => 'elewacja'])

      <h2 class="mt-12 font-sans text-xl font-semibold" aria-live="polite" data-licznik>
        {{ sprintf(_n('Znaleziono %d mieszkanie', 'Znaleziono %d mieszkań', count($wynik), 'przystan'), count($wynik)) }}
      </h2>

      @include('partials.tabela-mieszkan', ['mieszkania' => $wynik])
    </div>
  </div>
@endsection
