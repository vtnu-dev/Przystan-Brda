@php($poziom = $poziom ?? 'h2')
<article class="group relative flex flex-col rounded-lg" data-karta data-karta-bez-cienia>
  @if (has_post_thumbnail($wpis))
    <div class="overflow-hidden rounded-lg">
      {!! get_the_post_thumbnail($wpis, 'karta', [
        'class' => 'aspect-[4/3] w-full object-cover',
        'sizes' => '(min-width: 768px) 30vw, 100vw',
        'loading' => 'lazy',
        'alt' => '',
      ]) !!}
    </div>
  @endif
  <p class="mt-5 text-sm text-granat/75"><time datetime="{{ get_post_time('c', true, $wpis) }}">{{ get_the_date('', $wpis) }}</time></p>
  <{{ $poziom }} class="mt-2 text-2xl">
    <a href="{{ get_permalink($wpis) }}" class="text-granat no-underline after:absolute after:inset-0 group-hover:text-morze">{{ get_the_title($wpis) }}</a>
  </{{ $poziom }}>
  <p class="mt-3 text-granat/85">{{ get_the_excerpt($wpis) }}</p>
</article>
