{{--
  Hero A: „rysunek z rzeki”. Kreska rysuje bryłę budynku, okna zapalają się piętrami, potem granatowa
  nakładka gaśnie i zostaje zdjęcie. Współrzędne w układzie zdjęcia (1376×768), więc rysunek leży mniej więcej
  na budynku ze zdjęcia; preserveAspectRatio działa jak object-cover. Animacja w CSS (.hero-szkic w app.css):
  działa bez JavaScriptu, przy „ogranicz ruch” nakładki nie ma wcale.
--}}
@php
  $x0 = 309; $x1 = 1075; $y0 = 59; $y1 = 493; $pietra = 5; $kolumny = 9;
  $hp = ($y1 - $y0) / $pietra;
  $wk = ($x1 - $x0) / $kolumny;
@endphp
<div class="hero-szkic pointer-events-none absolute inset-0 -z-10" aria-hidden="true">
  <svg class="h-full w-full" viewBox="0 0 1376 768" preserveAspectRatio="xMidYMid slice" focusable="false">
    <g class="hero-szkic__kreska" fill="none" stroke="#6fb3b3" stroke-width="2.5" stroke-linecap="round">
      <path pathLength="1" d="M{{ $x0 }} {{ $y1 }} V{{ $y0 }} H{{ $x1 }} V{{ $y1 }}" />
      <path pathLength="1" d="M{{ $x0 - 20 }} {{ $y0 }} H{{ $x1 + 20 }}" style="--k: 1" />
      @for ($p = 1; $p < $pietra; $p++)
        <path pathLength="1" d="M{{ $x0 }} {{ round($y0 + $p * $hp) }} H{{ $x1 }}" opacity=".45" style="--k: {{ 1 + $p * 0.25 }}" />
      @endfor
      <path pathLength="1" d="M120 {{ $y1 }} H1256" style="--k: 2" />
    </g>
    <g class="hero-szkic__okna">
      @for ($p = 0; $p < $pietra; $p++)
        @for ($k = 0; $k < $kolumny; $k++)
          <rect x="{{ round($x0 + $k * $wk + $wk * 0.24) }}" y="{{ round($y1 - ($p + 1) * $hp + $hp * 0.2) }}"
                width="{{ round($wk * 0.52) }}" height="{{ round($hp * 0.6) }}" rx="2"
                style="--p: {{ $p }}; --k: {{ ($k * 37 + $p * 11) % 9 }}" />
        @endfor
      @endfor
    </g>
    <g class="hero-szkic__woda" fill="none" stroke="#6fb3b3" stroke-width="2" stroke-linecap="round">
      <path pathLength="1" d="M0 560 C 230 545, 460 575, 690 560 S 1150 545, 1376 560" />
      <path pathLength="1" d="M0 610 C 260 597, 500 623, 740 610 S 1180 596, 1376 610" opacity=".6" style="--k: .3" />
      <path pathLength="1" d="M0 662 C 290 651, 540 673, 790 662 S 1200 650, 1376 662" opacity=".35" style="--k: .6" />
    </g>
  </svg>
</div>
