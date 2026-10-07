{{-- Lista mieszkań. Wiersze odtwarza też JavaScript (modules/wyszukiwarka.js) z tych samych pól REST. --}}
<div class="mt-5" data-wyniki>
  <table class="tabela-mieszkan" data-zobacz="{{ __('Zobacz', 'przystan') }}"
         data-ulub-dodaj="{{ __('Dodaj %s do ulubionych', 'przystan') }}" data-ulub-usun="{{ __('Usuń %s z ulubionych', 'przystan') }}">
    <caption class="sr-only">{{ __('Lista mieszkań spełniających filtry', 'przystan') }}</caption>
    <thead>
      <tr>
        <th scope="col">{{ __('Lokal', 'przystan') }}</th>
        <th scope="col">{{ __('Piętro', 'przystan') }}</th>
        <th scope="col">{{ __('Pokoje', 'przystan') }}</th>
        <th scope="col">{{ __('Metraż', 'przystan') }}</th>
        <th scope="col">{{ __('Balkon', 'przystan') }}</th>
        <th scope="col">{{ __('Cena', 'przystan') }}</th>
        <th scope="col">{{ __('Status', 'przystan') }}</th>
        <th scope="col"><span class="sr-only">{{ __('Szczegóły', 'przystan') }}</span></th>
        <th scope="col"><span class="sr-only">{{ __('Ulubione', 'przystan') }}</span></th>
      </tr>
    </thead>
    <tbody data-wiersze>
      @foreach ($mieszkania as $m)
        <tr data-id="{{ $m['id'] }}">
          <td class="td-numer font-serif text-xl" data-etykieta="{{ __('Lokal', 'przystan') }}">{{ $m['numer'] }}</td>
          <td data-etykieta="{{ __('Piętro', 'przystan') }}">{{ $m['pietro_tekst'] }}</td>
          <td data-etykieta="{{ __('Pokoje', 'przystan') }}">{{ $m['pokoje'] }}</td>
          <td data-etykieta="{{ __('Metraż', 'przystan') }}">{{ $m['metraz_tekst'] }}</td>
          <td data-etykieta="{{ __('Balkon', 'przystan') }}">{{ $m['balkon_tekst'] }}</td>
          <td data-etykieta="{{ __('Cena', 'przystan') }}">{{ $m['cena_tekst'] ?: '-' }}</td>
          <td data-etykieta="{{ __('Status', 'przystan') }}"><span class="status status--{{ $m['status'] }}">{{ $m['status_etykieta'] }}</span></td>
          <td class="td-link"><a href="{{ $m['url'] }}" class="font-semibold">{{ __('Zobacz', 'przystan') }}<span class="sr-only"> {{ $m['numer'] }}</span></a></td>
          <td class="td-ulub">@include('partials.ulubione-przycisk', ['id' => $m['id_ulubione'], 'numer' => $m['numer'], 'klasa' => 'inline-flex size-11 items-center justify-center rounded-full text-morze hover:bg-white'])</td>
        </tr>
      @endforeach
    </tbody>
  </table>
  <p class="mt-6 text-granat/80 {{ $mieszkania ? 'hidden' : '' }}" data-brak>
    {{ __('Żadne mieszkanie nie spełnia tych warunków. Zmień filtry albo napisz do nas: często mamy lokale przed publikacją.', 'przystan') }}
  </p>
</div>
