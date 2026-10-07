<!doctype html>
<html @php(language_attributes())>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0f2a3a">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    @php(do_action('get_header'))
    @php(wp_head())

    @vite(['resources/css/app.css', 'resources/js/app.js'])
  </head>

  <body @php(body_class())>
    @php(wp_body_open())
    {{-- Cloudflare Email Obfuscation psuje się na linkach w SVG elewacji; adresy i tak są celowo publiczne (demo). --}}
    <!--email_off-->

    <a class="skip-link" href="#tresc">{{ __('Przejdź do treści', 'przystan') }}</a>

    @include('sections.header')

    <main id="tresc" tabindex="-1" class="focus:outline-none">
      @yield('content')
    </main>

    @include('sections.footer')

    @php(do_action('get_footer'))
    <!--/email_off-->
    @php(wp_footer())
  </body>
</html>
