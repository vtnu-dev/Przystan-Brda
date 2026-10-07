{{-- Oś czasu etapów. Stan pokazany ikoną i tekstem, nie samym kolorem. --}}
@php
  $stany = ['zrobione' => __('zrobione', 'przystan'), 'w_toku' => __('w toku', 'przystan'), 'planowane' => __('planowane', 'przystan')];
  $kolko = ['zrobione' => 'border-morze bg-morze text-white', 'w_toku' => 'border-bursztyn bg-tlo text-bursztyn', 'planowane' => 'border-granat/40 bg-tlo text-granat/40'];
  $tekst = ['zrobione' => 'text-morze', 'w_toku' => 'text-bursztyn', 'planowane' => 'text-granat/75'];
@endphp
<ol class="relative mt-8 space-y-7 border-l-2 border-piasek-ciemny pl-8" data-odslon>
  @foreach ($etapy as $etap)
    <li class="relative">
      <span class="absolute -left-[2.6rem] top-0.5 flex size-6 items-center justify-center rounded-full border-2 {{ $kolko[$etap['stan']] }}" aria-hidden="true">
        @if ($etap['stan'] === 'zrobione')
          <svg class="size-3.5" viewBox="0 0 16 16"><path d="m3 8 3.5 3.5L13 5" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        @elseif ($etap['stan'] === 'w_toku')
          <span class="size-2 rounded-full bg-current"></span>
        @endif
      </span>
      <p class="text-sm text-granat/75">{{ $etap['data'] }}</p>
      <p class="font-serif text-xl">{{ $etap['nazwa'] }}</p>
      <p class="text-sm font-semibold {{ $tekst[$etap['stan']] }}">{{ $stany[$etap['stan']] }}</p>
    </li>
  @endforeach
</ol>
