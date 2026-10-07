{{-- Separator sekcji: trzy linie wody. $klasa pozwala zmienić kolor i wysokość. --}}
<svg class="linie-wody {{ $klasa ?? 'h-10 text-morze' }}" viewBox="0 0 1200 60" preserveAspectRatio="none" aria-hidden="true" focusable="false">
  <path pathLength="1" d="M-40 14 C 180 4, 380 26, 600 14 S 1000 4, 1240 14" />
  <path pathLength="1" d="M-40 32 C 220 22, 420 44, 640 32 S 1040 20, 1240 32" opacity=".6" />
  <path pathLength="1" d="M-40 50 C 260 42, 460 60, 700 50 S 1080 40, 1240 50" opacity=".35" />
</svg>
