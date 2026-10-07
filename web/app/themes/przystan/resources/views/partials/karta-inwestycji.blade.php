{{-- Karta inwestycji na liście i na stronie głównej. --}}
@php $poziom = $poziom ?? 'h3'; @endphp
<article class="group relative flex flex-col overflow-hidden rounded-xl bg-white">
  @if ($i['zdjecie'])
    {!! wp_get_attachment_image($i['zdjecie'], 'karta', false, [
      'class' => 'aspect-[16/10] w-full object-cover transition-transform duration-700 group-hover:scale-[1.03]',
      'sizes' => '(min-width: 1024px) 30vw, (min-width: 640px) 45vw, 100vw',
      'loading' => 'lazy',
      'alt' => '',
    ]) !!}
  @endif
  <div class="flex grow flex-col p-6">
    <p class="text-sm font-semibold uppercase tracking-[0.12em] {{ $i['status'] === 'planowana' ? 'text-bursztyn' : 'text-morze' }}">{{ $i['status_etykieta'] }}</p>
    <{{ $poziom }} class="mt-2 text-3xl">
      <a href="{{ $i['url'] }}" class="text-granat no-underline after:absolute after:inset-0 group-hover:text-morze">{{ $i['nazwa'] }}</a>
    </{{ $poziom }}>
    <p class="mt-1 text-granat/80">{{ $i['lokalizacja'] }}</p>
    <p class="mt-4 grow text-granat/85">{{ $i['zajawka'] }}</p>
    <p class="mt-5 flex flex-wrap gap-x-5 gap-y-1 border-t border-piasek-ciemny pt-4 text-sm">
      @if ($i['mieszkan'] > 0)
        <span><strong>{{ sprintf(_n('%d wolne', '%d wolnych', $i['wolnych'], 'przystan'), $i['wolnych']) }}</strong> / {{ $i['mieszkan'] }}</span>
      @endif
      @if ($i['termin'])
        <span>{{ $i['termin'] }}</span>
      @endif
    </p>
  </div>
</article>
