{{--
  Rysowana elewacja budynku. Każde okno to link do karty mieszkania z pełnym opisem dla czytników ekranu.
  Parametry: $mieszkania (lista z Mieszkanie::zPosta), $pasujace (ID pasujących do filtrów albo null = wszystkie).
--}}
@php
  use App\Elewacja as E;
  $pasujace = $pasujace ?? null;
  $id = $id ?? 'elewacja';
  $szer = E::SZEROKOSC;
  $wys = E::wysokosc();
  $dol = E::dol();
@endphp

<div class="elewacja" data-elewacja id="{{ $id }}">
  <svg viewBox="0 0 {{ $szer }} {{ $wys }}" role="group" aria-labelledby="{{ $id }}-tytul" focusable="false">
    <title id="{{ $id }}-tytul">{{ __('Elewacja budynku od strony rzeki. Wybierz mieszkanie, żeby zobaczyć szczegóły.', 'przystan') }}</title>
    <defs>
      <pattern id="wzor-rezerwacja" width="14" height="14" patternUnits="userSpaceOnUse" patternTransform="rotate(45)">
        <rect width="14" height="14" fill="#e9c88f" />
        <rect width="5" height="14" fill="#c99a4b" />
      </pattern>
      <pattern id="wzor-sprzedane" width="10" height="10" patternUnits="userSpaceOnUse" patternTransform="rotate(-45)">
        <rect width="10" height="10" fill="#d8cfb8" />
        <rect width="1.5" height="10" fill="#0f2a3a" fill-opacity=".35" />
      </pattern>
    </defs>

    {{-- Bryła budynku --}}
    <rect x="{{ E::X0 - 14 }}" y="{{ E::DACH }}" width="{{ E::X1 - E::X0 + 28 }}" height="{{ E::GORA - E::DACH }}" fill="#d8cfb8" stroke="#0f2a3a" stroke-width="3" />
    <rect x="{{ E::X0 }}" y="{{ E::GORA }}" width="{{ E::X1 - E::X0 }}" height="{{ $dol - E::GORA }}" fill="#e8dfc9" stroke="#0f2a3a" stroke-width="3" />
    @for ($p = 0; $p < E::PIETRA; $p++)
      @php $y = E::yPietra($p); @endphp
      <line x1="{{ E::X0 }}" x2="{{ E::X1 }}" y1="{{ $y + E::WYS_PIETRA }}" y2="{{ $y + E::WYS_PIETRA }}" stroke="#0f2a3a" stroke-opacity=".18" stroke-width="2" />
      <text x="{{ E::X0 - 40 }}" y="{{ $y + 52 }}" text-anchor="middle" class="pietro-etykieta" fill="#0f2a3a" font-size="24" font-family="Newsreader Variable, serif">{{ E::etykietaPietra($p) }}</text>
    @endfor

    {{-- Mieszkania --}}
    @foreach ($mieszkania as $m)
      @php
        $o = E::okno($m);
        $przygaszony = is_array($pasujace) && ! in_array($m['id'], $pasujace, true);
      @endphp
      <a href="{{ $m['url'] }}"
         class="lokal lokal--{{ $m['status'] }} {{ $przygaszony ? 'przygaszony' : '' }}"
         aria-label="{{ $m['opis'] }}{{ $m['cena_tekst'] ? ', ' . $m['cena_tekst'] : '' }}"
         data-id="{{ $m['id'] }}"
         data-numer="{{ $m['numer'] }}"
         data-szczegoly="{{ $m['pokoje_tekst'] }} · {{ $m['metraz_tekst'] }} · {{ $m['pietro_tekst'] }}"
         data-cena="{{ $m['cena_tekst'] }}"
         data-status="{{ $m['status_etykieta'] }}">
        <rect class="okno" x="{{ $o['x'] }}" y="{{ $o['y'] }}" width="{{ $o['w'] }}" height="{{ $o['h'] }}" rx="3" />
        @if ($m['balkon_m2'] > 0)
          <rect x="{{ $o['x'] - 6 }}" y="{{ $o['y'] + $o['h'] + 2 }}" width="{{ $o['w'] + 12 }}" height="6" fill="#0f2a3a" />
        @endif
        @if ($m['ogrodek'])
          <path d="M{{ $o['x'] }} {{ $dol }} q{{ $o['w'] / 6 }} -16 {{ $o['w'] / 3 }} 0 q{{ $o['w'] / 6 }} -18 {{ $o['w'] / 3 }} 0 q{{ $o['w'] / 6 }} -16 {{ $o['w'] / 3 }} 0z" fill="#6fb3b3" />
        @endif
        <text x="{{ $o['cx'] }}" y="{{ $o['cy'] }}" text-anchor="middle">{{ $m['numer'] }}</text>
      </a>
    @endforeach

    {{-- Bulwar i rzeka --}}
    <rect x="0" y="{{ $dol }}" width="{{ $szer }}" height="18" fill="#d8cfb8" />
    <g class="linie-wody linie-wody--ruch" aria-hidden="true">
      <path d="M-40 {{ $dol + 40 }} C 160 {{ $dol + 28 }}, 360 {{ $dol + 52 }}, 600 {{ $dol + 40 }} S 1000 {{ $dol + 28 }}, 1260 {{ $dol + 40 }}" />
      <path d="M-40 {{ $dol + 64 }} C 200 {{ $dol + 54 }}, 420 {{ $dol + 76 }}, 640 {{ $dol + 64 }} S 1040 {{ $dol + 52 }}, 1260 {{ $dol + 64 }}" opacity=".6" />
      <path d="M-40 {{ $dol + 88 }} C 240 {{ $dol + 80 }}, 460 {{ $dol + 98 }}, 700 {{ $dol + 88 }} S 1080 {{ $dol + 78 }}, 1260 {{ $dol + 88 }}" opacity=".35" />
    </g>
    <text x="{{ E::X1 }}" y="{{ $dol + 72 }}" text-anchor="end" fill="#1f6f78" font-size="22" font-style="italic" font-family="Newsreader Variable, serif" aria-hidden="true">{{ __('Brda', 'przystan') }}</text>
  </svg>

  <div class="elewacja-dymek" role="status" hidden data-dymek></div>

  <ul class="mt-4 flex flex-wrap gap-x-6 gap-y-2 text-sm" aria-label="{{ __('Legenda', 'przystan') }}">
    <li class="flex items-center gap-2"><span class="inline-block h-4 w-6 rounded-sm bg-morze" aria-hidden="true"></span>{{ __('wolne', 'przystan') }}</li>
    <li class="flex items-center gap-2"><span class="inline-block h-4 w-6 rounded-sm" style="background:repeating-linear-gradient(45deg,#e9c88f 0 5px,#c99a4b 5px 8px)" aria-hidden="true"></span>{{ __('rezerwacja', 'przystan') }}</li>
    <li class="flex items-center gap-2"><span class="inline-block h-4 w-6 rounded-sm" style="background:repeating-linear-gradient(-45deg,#d8cfb8 0 7px,#a8a294 7px 8px)" aria-hidden="true"></span>{{ __('sprzedane', 'przystan') }}</li>
    <li class="flex items-center gap-2"><span class="inline-block h-1.5 w-6 bg-granat" aria-hidden="true"></span>{{ __('balkon lub taras', 'przystan') }}</li>
    <li class="flex items-center gap-2"><span class="inline-block h-3 w-6 rounded-t-full bg-morze-jasne" aria-hidden="true"></span>{{ __('ogródek', 'przystan') }}</li>
  </ul>
</div>
