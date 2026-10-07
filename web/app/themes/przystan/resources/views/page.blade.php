@extends('layouts.app')

@section('content')
  @while (have_posts())
    @php(the_post())
    <article class="kontener max-w-3xl pb-20 pt-12 md:pt-16">
      <h1 class="h2">{{ get_the_title() }}</h1>
      <div class="tresc mt-8 text-lg">
        @php(the_content())
      </div>
    </article>
  @endwhile
@endsection
