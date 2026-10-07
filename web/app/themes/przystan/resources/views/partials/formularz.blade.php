{{--
  Formularz zapytania. Bez JS wysyła się przez admin-post.php i wraca z wynikiem;
  z JS (modules/formularz.js) idzie do REST API bez przeładowania. Walidacja jest ta sama (wtyczka).
  Parametry: $mieszkanieId (opcjonalnie).
--}}
@php
  use Przystan\Zapytania\Antyspam;
  use Przystan\Zapytania\FormularzBezJs;
  use Przystan\Ustawienia\Ustawienia;

  $wynikForm = FormularzBezJs::wynik();
  $bledy = $wynikForm['bledy'] ?? [];
  $stare = $wynikForm['dane'] ?? [];
  $wartosc = fn (string $pole) => (string) ($stare[$pole] ?? '');
  $mieszkanieId = $mieszkanieId ?? 0;
  $prywatnosc = get_privacy_policy_url();
  $pola = [
    'imie' => ['etykieta' => __('Imię i nazwisko', 'przystan'), 'typ' => 'text', 'autocomplete' => 'name', 'wymagane' => true],
    'email' => ['etykieta' => __('E-mail', 'przystan'), 'typ' => 'email', 'autocomplete' => 'email', 'wymagane' => true],
    'telefon' => ['etykieta' => __('Telefon', 'przystan'), 'typ' => 'tel', 'autocomplete' => 'tel', 'wymagane' => false],
  ];
@endphp

<form method="post" action="{{ admin_url('admin-post.php') }}" class="mt-8 grid gap-5 md:grid-cols-2" novalidate
      data-formularz data-rest="{{ rest_url('przystan/v1/zapytania') }}">
  <input type="hidden" name="action" value="{{ FormularzBezJs::AKCJA }}">
  <input type="hidden" name="mieszkanie" value="{{ (int) $mieszkanieId }}">
  <input type="hidden" name="{{ Antyspam::POLE_CZAS }}" value="{{ Antyspam::znacznik(time(), Ustawienia::kluczAntyspamu()) }}">

  {{-- Pułapka na boty: ukryta dla ludzi i czytników ekranu. --}}
  <div class="absolute -left-[9999px] h-px w-px overflow-hidden" aria-hidden="true">
    <label for="f-www">{{ __('Nie wypełniaj tego pola', 'przystan') }}</label>
    <input id="f-www" type="text" name="{{ Antyspam::POLE_PULAPKA }}" tabindex="-1" autocomplete="off">
  </div>

  <div class="md:col-span-2" role="status" aria-live="polite" data-komunikat>
    @if ($wynikForm)
      <p class="rounded-lg px-4 py-3 font-semibold {{ $wynikForm['ok'] ? 'bg-morze text-white' : 'bg-white text-red-800' }}" tabindex="-1">{{ $wynikForm['komunikat'] }}</p>
    @endif
  </div>

  @foreach ($pola as $nazwa => $p)
    <div class="pole {{ $nazwa === 'telefon' ? 'md:col-span-2 md:max-w-sm' : '' }}">
      <label for="f-{{ $nazwa }}">{{ $p['etykieta'] }}@if (! $p['wymagane']) <span class="font-normal text-granat/75">({{ __('opcjonalnie', 'przystan') }})</span>@endif</label>
      <input id="f-{{ $nazwa }}" type="{{ $p['typ'] }}" name="{{ $nazwa }}" autocomplete="{{ $p['autocomplete'] }}"
             value="{{ $wartosc($nazwa) }}" @if ($p['wymagane']) required aria-required="true" @endif
             @if (isset($bledy[$nazwa])) aria-invalid="true" @endif aria-describedby="f-{{ $nazwa }}-blad">
      <p id="f-{{ $nazwa }}-blad" class="blad" data-blad="{{ $nazwa }}">{{ $bledy[$nazwa] ?? '' }}</p>
    </div>
  @endforeach

  <div class="pole md:col-span-2">
    <label for="f-wiadomosc">{{ __('Wiadomość', 'przystan') }} <span class="font-normal text-granat/75">({{ __('opcjonalnie', 'przystan') }})</span></label>
    <textarea id="f-wiadomosc" name="wiadomosc" rows="4" maxlength="2000" aria-describedby="f-wiadomosc-blad"
              @if (isset($bledy['wiadomosc'])) aria-invalid="true" @endif>{{ $wartosc('wiadomosc') }}</textarea>
    <p id="f-wiadomosc-blad" class="blad" data-blad="wiadomosc">{{ $bledy['wiadomosc'] ?? '' }}</p>
  </div>

  <div class="pole md:col-span-2">
    <label class="flex cursor-pointer items-start gap-3 !font-normal">
      <input type="checkbox" name="zgoda" value="1" class="mt-1 size-5 shrink-0 accent-morze" required aria-required="true"
             aria-describedby="f-zgoda-blad" @if (isset($bledy['zgoda'])) aria-invalid="true" @endif @checked(! empty($stare['zgoda']))>
      <span>
        {{ __('Zgadzam się na przetwarzanie moich danych w celu odpowiedzi na zapytanie.', 'przystan') }}
        @if ($prywatnosc)
          <a href="{{ $prywatnosc }}">{{ __('Polityka prywatności', 'przystan') }}</a>
        @endif
      </span>
    </label>
    <p id="f-zgoda-blad" class="blad" data-blad="zgoda">{{ $bledy['zgoda'] ?? '' }}</p>
  </div>

  <div class="md:col-span-2">
    <button type="submit" class="przycisk" data-wyslij>{{ __('Wyślij zapytanie', 'przystan') }}</button>
  </div>
</form>
