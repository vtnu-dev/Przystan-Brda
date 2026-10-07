<section class="bg-morze text-white" aria-labelledby="cta-tytul">
  <div class="kontener flex flex-col gap-8 py-14 md:flex-row md:items-center md:justify-between md:py-16">
    <div class="max-w-xl">
      <h2 id="cta-tytul" class="h2 text-white">{{ __('Porozmawiajmy o mieszkaniu', 'przystan') }}</h2>
      <p class="mt-4 text-lg text-white/90">{{ __('Pokażemy rzuty, wyliczymy ratę i umówimy spacer po bulwarze. Odpowiadamy w ciągu jednego dnia roboczego.', 'przystan') }}</p>
    </div>
    <div class="flex flex-wrap gap-3">
      <a href="{{ $urlKontakt }}" class="przycisk przycisk--jasny">{{ __('Napisz do nas', 'przystan') }}</a>
      @if ($kontakt['telefon'])
        <a href="{{ $kontakt['telefon_href'] }}" class="przycisk border-2 border-white/70 bg-transparent text-white hover:border-white hover:bg-white/10">{{ $kontakt['telefon'] }}</a>
      @endif
    </div>
  </div>
</section>
