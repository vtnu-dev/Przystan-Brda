{{--
  Kalkulator raty (rata równa). Liczy w przeglądarce (modules/kalkulator.js); bez JS jest ukryty.
  Parametry: $cena (zł).
--}}
<section class="mt-6 rounded-xl border border-tlo/20 p-5" hidden data-kalkulator data-cena="{{ (int) $cena }}"
         data-jezyk="{{ $jezyk }}" aria-labelledby="kalkulator-tytul">
  <h3 id="kalkulator-tytul" class="font-sans text-sm font-semibold uppercase tracking-[0.14em] text-morze-jasne">{{ __('Ile wyniesie rata?', 'przystan') }}</h3>
  <div class="mt-4 space-y-4 text-sm">
    <label class="block">
      <span class="flex justify-between"><span>{{ __('Wkład własny', 'przystan') }}</span><output data-wynik="wklad" class="font-semibold"></output></span>
      <input type="range" min="10" max="50" step="5" value="20" data-pole="wklad" class="mt-2 w-full accent-morze-jasne">
    </label>
    <label class="block">
      <span class="flex justify-between"><span>{{ __('Okres kredytu', 'przystan') }}</span><output data-wynik="lata" class="font-semibold"></output></span>
      <input type="range" min="10" max="35" step="1" value="25" data-pole="lata" class="mt-2 w-full accent-morze-jasne">
    </label>
    <label class="block">
      <span class="flex justify-between"><span>{{ __('Oprocentowanie roczne', 'przystan') }}</span><output data-wynik="procent" class="font-semibold"></output></span>
      <input type="range" min="4" max="10" step="0.1" value="7.2" data-pole="procent" class="mt-2 w-full accent-morze-jasne">
    </label>
  </div>
  <p class="mt-5 border-t border-tlo/20 pt-4" aria-live="polite">
    <span class="block text-sm text-tlo/75">{{ __('Rata miesięczna (orientacyjnie)', 'przystan') }}</span>
    <output data-wynik="rata" class="font-serif text-3xl"></output>
    <span class="mt-1 block text-sm text-tlo/75"><span>{{ __('Kwota kredytu:', 'przystan') }}</span> <output data-wynik="kredyt"></output></span>
  </p>
  <p class="mt-3 text-xs text-tlo/70">{{ __('Wyliczenie poglądowe, nie jest ofertą banku. Dokładną symulację przygotuje doradca.', 'przystan') }}</p>
</section>
