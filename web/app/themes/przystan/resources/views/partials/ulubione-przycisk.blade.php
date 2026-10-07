{{-- Przełącznik „ulubione” (localStorage, bez logowania). Bez JS jest ukryty, bo nie miałby czego zapisać. --}}
<button type="button" hidden data-ulubione="{{ $id }}" aria-pressed="false"
        data-dodaj="{{ sprintf(__('Dodaj %s do ulubionych', 'przystan'), $numer) }}"
        data-usun="{{ sprintf(__('Usuń %s z ulubionych', 'przystan'), $numer) }}"
        aria-label="{{ sprintf(__('Dodaj %s do ulubionych', 'przystan'), $numer) }}"
        class="{{ $klasa ?? 'inline-flex min-h-11 items-center gap-2 rounded-full border-2 border-current px-4 font-semibold' }} group/ulub">
  <svg class="size-5" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 20s-7-4.4-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 10c0 5.6-7 10-7 10Z" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round" class="group-aria-pressed/ulub:fill-current"/></svg>
  @if (! empty($tekst))<span>{{ __('Ulubione', 'przystan') }}</span>@endif
</button>
