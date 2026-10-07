<?php

/**
 * Buduje pliki tłumaczeń domeny „przystan” z przystan.pot:
 *  - en_GB.po: pełne tłumaczenie angielskie,
 *  - pl_PL.po: tylko formy liczby mnogiej (polski ma trzy, a _n() bez tłumaczenia zna dwie).
 * Potem: wp i18n make-mo + make-php (zob. README).
 *
 * Uruchomienie: php scripts/tlumaczenia.php
 */

$katalog = __DIR__ . '/../web/app/plugins/przystan-core/languages/';
$pot = (string) file_get_contents($katalog . 'przystan.pot');

$en = [
    'rezerwacja' => 'reserved', 'sprzedane' => 'sold', 'wolne' => 'available', 'cena na zapytanie' => 'price on request',
    '%s zł' => 'PLN %s', '%s m²' => '%s m²', 'parter' => 'ground floor', 'piętro %d' => 'floor %d',
    'Mieszkanie %1$s, %2$s, %3$s, %4$s, %5$s' => 'Apartment %1$s, %2$s, %3$s, %4$s, %5$s',
    'Dane mieszkania' => 'Apartment details', 'Numer lokalu' => 'Unit number', 'Piętro' => 'Floor', '0 = parter' => '0 = ground floor',
    'Pozycja na elewacji' => 'Position on the facade', '1-6, od lewej' => '1-6, from the left', 'Status' => 'Status', 'Pokoje' => 'Rooms',
    'Metraż (m²)' => 'Floor area (m²)', 'Cena (zł)' => 'Price (PLN)', 'Puste = „cena na zapytanie”' => 'Empty = "price on request"',
    'Balkon / taras (m²)' => 'Balcony / terrace (m²)', 'Ogródek' => 'Garden', 'Widok na rzekę' => 'River view', 'Rzut mieszkania' => 'Floor plan',
    'Mieszkania' => 'Apartments', 'Mieszkanie' => 'Apartment', 'Dodaj mieszkanie' => 'Add apartment', 'Edytuj mieszkanie' => 'Edit apartment',
    'Wszystkie mieszkania' => 'All apartments', 'Szukaj mieszkań' => 'Search apartments', 'Brak mieszkań' => 'No apartments found',
    'Metraż' => 'Area', 'Cena' => 'Price', 'SEO' => 'SEO', 'Opis w wynikach wyszukiwania' => 'Search result description',
    'Do 160 znaków. Puste = zajawka lub początek treści.' => 'Up to 160 characters. Empty = excerpt or start of content.',
    'Hero' => 'Hero', 'Nadtytuł' => 'Eyebrow', 'Nagłówek' => 'Heading', 'Nowa linia = łamanie wiersza.' => 'New line = line break.',
    'Wstęp' => 'Intro', 'Zdjęcie' => 'Photo', 'Liczby' => 'Numbers', 'Liczba %d: wartość' => 'Number %d: value', 'Liczba %d: opis' => 'Number %d: label',
    'Atuty' => 'Highlights', 'Atut %d: tytuł' => 'Highlight %d: title', 'Atut %d: opis' => 'Highlight %d: text', 'Atut %d: zdjęcie' => 'Highlight %d: photo',
    'Okolica' => 'Neighbourhood', 'Strona główna' => 'Home', 'Punkty w okolicy' => 'Nearby places', 'Punkt %d: nazwa' => 'Place %d: name',
    'Punkt %d: minuty' => 'Place %d: minutes', 'Punkt %d: jak' => 'Place %d: how', 'pieszo' => 'on foot', 'rowerem' => 'by bike', 'tramwajem' => 'by tram',
    'Standard' => 'Finish', 'Lista (jedna pozycja w wierszu)' => 'List (one item per line)', 'Zdjęcie %d' => 'Photo %d',
    'Okolica i standard' => 'Neighbourhood and finish', 'Kontakt' => 'Contact',
    'Godziny biura sprzedaży (jedna pozycja w wierszu)' => 'Sales office hours (one item per line)', 'Przystań Brda' => 'Przystań Brda',
    'Biuro sprzedaży' => 'Sales office', 'Telefon' => 'Phone', 'E-mail' => 'E-mail', 'Adres' => 'Address', 'Termin oddania' => 'Completion date',
    'Np. „IV kwartał 2027”. Tłumaczenie: Języki → Tłumaczenia ciągów.' => 'E.g. "Q4 2027". Translation: Languages → Translations.',
    'Webhook zapytań' => 'Enquiry webhook', 'Adres odbiorcy' => 'Receiver URL', 'Puste = zapytania tylko zapisują się w panelu.' => 'Empty = enquiries are only saved in the admin.',
    'Każde nowe zapytanie wysyłamy jako JSON (zdarzenie zapytanie.utworzone). Nagłówek X-Przystan-Signature zawiera HMAC-SHA256 z „X-Przystan-Timestamp.treść” liczony tym kluczem.' => 'Every new enquiry is sent as JSON (event zapytanie.utworzone). The X-Przystan-Signature header holds an HMAC-SHA256 of "X-Przystan-Timestamp.body" computed with this key.',
    'brak klucza' => 'no key', 'Wygeneruj nowy klucz' => 'Generate a new key', 'Adres webhooka musi zaczynać się od https://.' => 'The webhook URL must start with https://.',
    'Brak uprawnień.' => 'You are not allowed to do this.', 'Wygenerowano nowy klucz. Przekaż go odbiorcy webhooka.' => 'A new key has been generated. Pass it on to the webhook receiver.',
    'wysłano' => 'sent', 'ponowienie zaplanowane' => 'retry scheduled', 'błąd' => 'failed', 'webhook wyłączony' => 'webhook disabled', 'oczekuje' => 'pending',
    'Podaj imię.' => 'Please enter your name.', 'Imię jest za krótkie.' => 'The name is too short.', 'Imię jest za długie.' => 'The name is too long.',
    'Podaj adres e-mail.' => 'Please enter your e-mail address.', 'Sprawdź adres e-mail, np. jan@przyklad.pl.' => 'Check the e-mail address, e.g. jan@example.com.',
    'Numer telefonu może zawierać tylko cyfry, spacje, +, - i nawiasy.' => 'The phone number may contain only digits, spaces, +, - and brackets.',
    'Wiadomość może mieć najwyżej 2000 znaków.' => 'The message can be up to 2000 characters long.',
    'Potrzebujemy zgody, żeby odpowiedzieć na zapytanie.' => 'We need your consent to reply to your enquiry.', 'Sprawdź to pole.' => 'Please check this field.',
    'Dziękujemy! Odpowiemy w ciągu jednego dnia roboczego.' => 'Thank you! We will reply within one working day.',
    'Formularz wygasł albo został wysłany zbyt szybko. Odśwież stronę i spróbuj ponownie.' => 'The form has expired or was sent too quickly. Refresh the page and try again.',
    'Popraw zaznaczone pola.' => 'Please correct the marked fields.',
    'Wysłano już kilka zapytań. Spróbuj ponownie za kilka minut albo zadzwoń.' => 'Several enquiries have already been sent. Try again in a few minutes or give us a call.',
    'Nie udało się zapisać zapytania. Zadzwoń do nas, proszę.' => 'We could not save your enquiry. Please give us a call.',
    'Ogólne' => 'General', 'Zapytania' => 'Enquiries', 'Zapytanie' => 'Enquiry', 'Wszystkie zapytania' => 'All enquiries', 'Brak zapytań' => 'No enquiries',
    'Webhook' => 'Webhook', 'Data' => 'Date', 'prób: %1$d, ostatni kod: %2$s' => 'attempts: %1$d, last code: %2$s', 'Wyślij ponownie' => 'Send again',
    'Webhook zaplanowany do ponownej wysyłki.' => 'Webhook scheduled to be sent again.', 'Treść zapytania' => 'Enquiry details', 'Imię' => 'Name',
    'Język' => 'Language', 'Zgoda RODO' => 'GDPR consent', 'Wiadomość' => 'Message', 'skrót: parter na elewacji' . "\x04" . 'P' => 'G',
    'Menu główne' => 'Main menu', 'Menu w stopce' => 'Footer menu', 'Błąd 404' => 'Error 404', 'Tej strony nie ma. Rzeka płynie dalej.' => 'This page is gone. The river flows on.',
    'Adres mógł się zmienić albo mieszkanie zostało usunięte z oferty. Zacznij od wyszukiwarki albo strony głównej.' => 'The address may have changed or the apartment was removed from the offer. Start with the search or the home page.',
    'Znajdź mieszkanie' => 'Find an apartment', 'Elewacja budynku od strony rzeki. Wybierz mieszkanie, żeby zobaczyć szczegóły.' => 'River-side facade of the building. Choose an apartment to see the details.',
    'Brda' => 'Brda', 'Legenda' => 'Legend', 'balkon lub taras' => 'balcony or terrace', 'ogródek' => 'garden', 'Termin oddania:' => 'Completion:',
    'Inwestycja w liczbach' => 'The development in numbers', 'Wskaż okno, które Ci się podoba' => 'Point to the window you like',
    'Każdy prostokąt to jedno mieszkanie. Kolor pokazuje, czy jest wolne. Kliknij, żeby zobaczyć rzut, metraż i cenę.' => 'Each rectangle is one apartment. The colour shows whether it is available. Click to see the floor plan, area and price.',
    'Wyszukiwarka z filtrami' => 'Search with filters', 'Dlaczego tutaj' => 'Why here', 'Spokojne mieszkania z widokiem, którego nikt nie zabuduje' => 'Calm apartments with a view no one can build over',
    'Okolica i standard wykończenia' => 'Neighbourhood and finish', 'Dziennik budowy' => 'Construction diary', 'Co nowego na placu budowy' => 'News from the construction site',
    'Wszystkie wpisy' => 'All posts', 'Na bieżąco' => 'Latest news', 'Postęp prac co kilka tygodni: zdjęcia z placu budowy i najważniejsze etapy.' => 'Progress every few weeks: photos from the site and the key milestones.',
    'Starsze wpisy' => 'Older posts', 'Nowsze wpisy' => 'Newer posts', 'Nie ma jeszcze wpisów.' => 'No posts yet.', 'Przejdź do treści' => 'Skip to content',
    'Porozmawiajmy o mieszkaniu' => "Let's talk about your apartment",
    'Pokażemy rzuty, wyliczymy ratę i umówimy spacer po bulwarze. Odpowiadamy w ciągu jednego dnia roboczego.' => 'We will show you the floor plans, work out the instalments and arrange a walk along the boulevard. We reply within one working day.',
    'Napisz do nas' => 'Write to us', 'Imię i nazwisko' => 'Full name', 'Nie wypełniaj tego pola' => 'Leave this field empty', 'opcjonalnie' => 'optional',
    'Zgadzam się na przetwarzanie moich danych w celu odpowiedzi na zapytanie.' => 'I agree to the processing of my data in order to reply to my enquiry.',
    'Polityka prywatności' => 'Privacy policy', 'Wyślij zapytanie' => 'Send enquiry', 'Jesteś tutaj' => 'You are here', 'Zobacz' => 'View',
    'Lista mieszkań spełniających filtry' => 'Apartments matching the filters', 'Lokal' => 'Unit', 'Balkon' => 'Balcony', 'Szczegóły' => 'Details',
    'Żadne mieszkanie nie spełnia tych warunków. Zmień filtry albo napisz do nas: często mamy lokale przed publikacją.' => 'No apartment matches these filters. Change the filters or write to us: we often have units before they are published.',
    'Na stronie' => 'On this site', 'Strona demonstracyjna.' => 'Demo website.',
    'Przystań Brda to fikcyjna inwestycja: mieszkania, ceny i dane kontaktowe są przykładowe.' => 'Przystań Brda is a fictional development: apartments, prices and contact details are examples.',
    'Projekt i wdrożenie:' => 'Design and build:', 'Menu' => 'Menu', 'Mieszkanie %s' => 'Apartment %s', 'Rzut mieszkania %1$s: %2$s, %3$s' => 'Floor plan of apartment %1$s: %2$s, %3$s',
    'Rzut poglądowy. Dokładne wymiary w karcie lokalu od doradcy.' => 'Indicative plan. Exact dimensions in the unit sheet from your advisor.',
    'Parametry' => 'Details', 'Balkon / taras' => 'Balcony / terrace', 'tak' => 'yes', 'nie' => 'no', '%s zł/m²' => 'PLN %s/m²',
    'Zapytaj o to mieszkanie' => 'Ask about this apartment', 'Położenie w budynku' => 'Location in the building', 'Inne mieszkania na tym piętrze' => 'Other apartments on this floor',
    'Zapytaj o mieszkanie %s' => 'Ask about apartment %s',
    'Doradca odpowie w ciągu jednego dnia roboczego: prześle kartę lokalu, harmonogram płatności i zaproponuje termin spotkania.' => 'An advisor will reply within one working day with the unit sheet, the payment schedule and a proposed meeting date.',
    'Godziny otwarcia' => 'Opening hours', 'Filtry' => 'Filters', 'dowolne' => 'any', 'od' => 'from', 'do' => 'to', 'wszystkie' => 'all',
    'widok na rzekę' => 'river view', 'Pokaż mieszkania' => 'Show apartments', 'Wyczyść filtry' => 'Clear filters', 'Pasujące mieszkania: %d' => 'Matching apartments: %d',
    'Okole, Bydgoszcz' => 'Okole, Bydgoszcz', 'Czas dojścia' => 'Travel time', 'min' => 'min',
];

$enMnoga = [
    '%d pokój' => ['%d room', '%d rooms'],
    '%d wolne' => ['%d available', '%d available'],
    '%d mieszkanie' => ['%d apartment', '%d apartments'],
    'pokój' => ['room', 'rooms'],
];
$plMnoga = [
    '%d pokój' => ['%d pokój', '%d pokoje', '%d pokoi'],
    '%d wolne' => ['%d wolne', '%d wolne', '%d wolnych'],
    '%d mieszkanie' => ['%d mieszkanie', '%d mieszkania', '%d mieszkań'],
    'pokój' => ['pokój', 'pokoje', 'pokoi'],
];

// Wpisy z .pot: [kontekst, msgid, msgid_plural]
preg_match_all('/^(?:msgctxt "(.*)"\n)?msgid "(.*)"\n(?:msgid_plural "(.*)"\n)?msgstr/m', $pot, $wpisy, PREG_SET_ORDER);

$esc = static fn(string $s) => addcslashes($s, "\"\\\n");
$naglowek = static fn(string $jezyk, string $formy) => "msgid \"\"\nmsgstr \"\"\n\"Project-Id-Version: Przystań Brda\\n\"\n\"Language: {$jezyk}\\n\"\n\"MIME-Version: 1.0\\n\"\n\"Content-Type: text/plain; charset=UTF-8\\n\"\n\"Content-Transfer-Encoding: 8bit\\n\"\n\"Plural-Forms: {$formy};\\n\"\n\"X-Domain: przystan\\n\"\n\n";

$poEn = $naglowek('en_GB', 'nplurals=2; plural=(n != 1)');
$poPl = $naglowek('pl_PL', 'nplurals=3; plural=(n==1 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2)');
$braki = [];

foreach ($wpisy as $w) {
    [$kontekst, $msgid, $mnoga] = [$w[1] ?? '', stripcslashes($w[2]), isset($w[3]) ? stripcslashes($w[3]) : ''];
    if ($msgid === '') {
        continue;
    }
    $ctx = $kontekst !== '' ? "msgctxt \"{$kontekst}\"\n" : '';

    if ($mnoga !== '') {
        $formy = $enMnoga[$msgid] ?? null;
        if (! $formy) {
            $braki[] = $msgid;
            continue;
        }
        $poEn .= "{$ctx}msgid \"{$esc($msgid)}\"\nmsgid_plural \"{$esc($mnoga)}\"\nmsgstr[0] \"{$esc($formy[0])}\"\nmsgstr[1] \"{$esc($formy[1])}\"\n\n";
        $pl = $plMnoga[$msgid];
        $poPl .= "{$ctx}msgid \"{$esc($msgid)}\"\nmsgid_plural \"{$esc($mnoga)}\"\nmsgstr[0] \"{$esc($pl[0])}\"\nmsgstr[1] \"{$esc($pl[1])}\"\nmsgstr[2] \"{$esc($pl[2])}\"\n\n";
        continue;
    }

    $klucz = $kontekst !== '' ? stripcslashes($kontekst) . "\x04" . $msgid : $msgid;
    if (! isset($en[$klucz])) {
        $braki[] = $msgid;
        continue;
    }
    $poEn .= "{$ctx}msgid \"{$esc($msgid)}\"\nmsgstr \"{$esc($en[$klucz])}\"\n\n";
}

file_put_contents($katalog . 'przystan-en_GB.po', $poEn);
file_put_contents($katalog . 'przystan-pl_PL.po', $poPl);

echo $braki ? "Brak tłumaczenia:\n - " . implode("\n - ", $braki) . "\n" : "Wszystkie ciągi przetłumaczone.\n";
exit($braki ? 1 : 0);
