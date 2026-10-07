@extends('layouts.app')

@section('content')
  @while (have_posts())
    @php(the_post())
    <article class="pb-20 pt-8">
      <div class="kontener max-w-3xl">
        @include('partials.okruszki', ['sciezka' => [
          ['nazwa' => get_the_title((int) get_option('page_for_posts')), 'url' => get_permalink((int) get_option('page_for_posts'))],
          ['nazwa' => get_the_title(), 'url' => ''],
        ]])
        <p class="mt-10 text-granat/75"><time datetime="{{ get_post_time('c', true) }}">{{ get_the_date() }}</time></p>
        <h1 class="h2 mt-2">{{ get_the_title() }}</h1>
      </div>

      @if (has_post_thumbnail())
        <div class="kontener mt-10 max-w-5xl">
          {!! get_the_post_thumbnail(null, 'szeroki', [
            'class' => 'aspect-[16/9] w-full rounded-[var(--radius-fala)] object-cover',
            'sizes' => '(min-width: 1024px) 64rem, 100vw',
            'loading' => 'eager',
            'fetchpriority' => 'high',
          ]) !!}
        </div>
      @endif

      <div class="kontener tresc mt-10 max-w-3xl text-lg">
        @php(the_content())
      </div>
    </article>
  @endwhile

  @include('partials.cta-kontakt')
@endsection
