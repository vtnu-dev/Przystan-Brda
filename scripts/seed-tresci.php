<?php

/**
 * Treści demo (PL i EN) dla scripts/seed.php. Firma i inwestycja są fikcyjne.
 */

$akapity = static fn(string ...$p) => implode("\n\n", array_map(static fn($a) => "<!-- wp:paragraph -->\n<p>{$a}</p>\n<!-- /wp:paragraph -->", $p));

return [
    'opis_strony' => [
        'pl' => 'Mieszkania nad wodą i w zieleni, Bydgoszcz',
        'en' => 'Homes by the water and among greenery, Bydgoszcz',
    ],

    'inwestycje' => [
        'przystan-brda' => [
            'kolejnosc' => 1,
            'zdjecie' => 'hero',
            'nazwa' => ['pl' => 'Przystań Brda', 'en' => 'Przystań Brda'],
            'slug' => ['pl' => 'przystan-brda', 'en' => 'przystan-brda-river'],
            'status' => 'w_budowie',
            'lokalizacja' => ['pl' => 'Bydgoszcz, Okole', 'en' => 'Bydgoszcz, Okole'],
            'termin' => ['pl' => 'IV kw. 2027', 'en' => 'Q4 2027'],
            'kondygnacje' => 6, 'lokali' => 6, 'woda' => true, 'podpis' => ['pl' => 'Brda', 'en' => 'Brda river'],
            'zajawka' => [
                'pl' => '36 mieszkań nad rzeką, przy nowym bulwarze. Z okien widać wodę, a do Starego Rynku dojdziesz w kwadrans.',
                'en' => '36 riverside apartments on a new boulevard. Water views from the windows and a 15-minute walk to the Old Market Square.',
            ],
            'tresc' => [
                'pl' => ['Przystań Brda stoi frontem do rzeki, na Okolu, jednej z najstarszych części Bydgoszczy. Budynek ma sześć kondygnacji i 36 mieszkań od 32 do 84 m². Na parterze są ogródki, wyżej balkony, a na ostatnim piętrze tarasy z widokiem na park po drugiej stronie wody.', 'Przed wejściem powstaje 120 metrów nowego bulwaru z ławkami i lipami. Garaż jest pod budynkiem, a w nim komórki lokatorskie, rowerownia i stanowiska do ładowania aut.'],
                'en' => ['Przystań Brda faces the river in Okole, one of the oldest parts of Bydgoszcz. The building has six storeys and 36 apartments from 32 to 84 m². Ground-floor units have gardens, upper floors have balconies and the top floor has terraces overlooking the park across the water.', 'A new 120-metre boulevard with benches and lime trees is being built at the entrance. The garage is underground, with storage rooms, a bike room and car charging points.'],
            ],
            'etapy' => [
                [['Pozwolenie na budowę', 'Building permit'], ['III kw. 2025', 'Q3 2025'], 'zrobione'],
                [['Fundamenty i garaż', 'Foundations and garage'], ['IV kw. 2025', 'Q4 2025'], 'zrobione'],
                [['Stan surowy', 'Building shell'], ['II kw. 2026', 'Q2 2026'], 'zrobione'],
                [['Elewacja i okna', 'Facade and windows'], ['III kw. 2026', 'Q3 2026'], 'w_toku'],
                [['Bulwar i zieleń', 'Boulevard and greenery'], ['IV kw. 2026 - II kw. 2027', 'Q4 2026 - Q2 2027'], 'w_toku'],
                [['Odbiór i przekazanie kluczy', 'Handover of keys'], ['IV kw. 2027', 'Q4 2027'], 'planowane'],
            ],
        ],
        'willa-lipowa' => [
            'kolejnosc' => 2,
            'zdjecie' => 'lipowa',
            'nazwa' => ['pl' => 'Willa Lipowa', 'en' => 'Willa Lipowa'],
            'slug' => ['pl' => 'willa-lipowa', 'en' => 'willa-lipowa-villa'],
            'status' => 'gotowe',
            'lokalizacja' => ['pl' => 'Bydgoszcz, Bielawy', 'en' => 'Bydgoszcz, Bielawy'],
            'termin' => ['pl' => 'gotowe, klucze od ręki', 'en' => 'ready, keys now'],
            'kondygnacje' => 4, 'lokali' => 4, 'woda' => false, 'podpis' => ['pl' => 'ul. Lipowa', 'en' => 'Lipowa Street'],
            'zajawka' => [
                'pl' => '16 mieszkań w kameralnej willi wśród starych lip. Budynek jest gotowy: można obejrzeć mieszkanie i od razu się wprowadzić.',
                'en' => '16 apartments in an intimate villa among old lime trees. The building is finished: view an apartment and move in straight away.',
            ],
            'tresc' => [
                'pl' => ['Willa Lipowa to cztery kondygnacje i tylko 16 mieszkań, po cztery na piętrze. Elewacja z jasnego tynku i drewna, duże okna i balkony z widokiem na korony lip, które rosną tu od kilkudziesięciu lat.', 'Budynek ma pozwolenie na użytkowanie. Mieszkania można obejrzeć w każdy dzień roboczy, a większość jest gotowa do odbioru w ciągu miesiąca od podpisania umowy.'],
                'en' => ['Willa Lipowa has four storeys and only 16 apartments, four per floor. A facade of light render and wood, large windows and balconies overlooking lime trees that have grown here for decades.', 'The building has its occupancy permit. Apartments can be viewed on any working day, and most can be handed over within a month of signing.'],
            ],
            'etapy' => [
                [['Pozwolenie na budowę', 'Building permit'], ['2023', '2023'], 'zrobione'],
                [['Stan surowy', 'Building shell'], ['III kw. 2024', 'Q3 2024'], 'zrobione'],
                [['Wykończenie i zieleń', 'Finishing and greenery'], ['II kw. 2025', 'Q2 2025'], 'zrobione'],
                [['Pozwolenie na użytkowanie', 'Occupancy permit'], ['IV kw. 2025', 'Q4 2025'], 'zrobione'],
                [['Przekazywanie kluczy', 'Handing over keys'], ['od I kw. 2026', 'from Q1 2026'], 'w_toku'],
            ],
        ],
        'tarasy-myslecinek' => [
            'kolejnosc' => 3,
            'zdjecie' => 'myslecinek',
            'nazwa' => ['pl' => 'Tarasy Myślęcinek', 'en' => 'Tarasy Myślęcinek'],
            'slug' => ['pl' => 'tarasy-myslecinek', 'en' => 'tarasy-myslecinek-terraces'],
            'status' => 'planowana',
            'lokalizacja' => ['pl' => 'Bydgoszcz, Myślęcinek', 'en' => 'Bydgoszcz, Myślęcinek'],
            'termin' => ['pl' => 'II kw. 2027', 'en' => 'Q2 2027'],
            'kondygnacje' => 5, 'lokali' => 5, 'woda' => false, 'podpis' => ['pl' => '', 'en' => ''],
            'zajawka' => [
                'pl' => 'Budynek z zielonymi tarasami na skraju leśnego parku. Projekt jest w przygotowaniu: zapisz się, a napiszemy przed startem sprzedaży.',
                'en' => 'A building with green terraces on the edge of a forest park. The design is in progress: sign up and we will write before sales start.',
            ],
            'tresc' => [
                'pl' => ['Tarasy Myślęcinek powstaną przy największym parku w Bydgoszczy. Każde piętro jest cofnięte względem niższego, więc prawie każde mieszkanie dostanie duży taras z zielenią. Za domem zaczynają się ścieżki leśne, a do centrum dojedziesz tramwajem w 20 minut.', 'Wizualizacja jest poglądowa. Rzuty i ceny pokażemy najpierw osobom zapisanym na powiadomienie.'],
                'en' => ['Tarasy Myślęcinek will be built next to the largest park in Bydgoszcz. Each floor steps back from the one below, so almost every apartment gets a large planted terrace. Forest paths start behind the building, and the tram takes you to the centre in 20 minutes.', 'The visualisation is indicative. We will show floor plans and prices to people on the notification list first.'],
            ],
            'etapy' => [
                [['Zakup działki', 'Land purchase'], ['2026', '2026'], 'zrobione'],
                [['Projekt budynku', 'Building design'], ['IV kw. 2026', 'Q4 2026'], 'w_toku'],
                [['Pozwolenie na budowę', 'Building permit'], ['I kw. 2027', 'Q1 2027'], 'planowane'],
                [['Start sprzedaży', 'Sales start'], ['II kw. 2027', 'Q2 2027'], 'planowane'],
                [['Rozpoczęcie budowy', 'Construction start'], ['III kw. 2027', 'Q3 2027'], 'planowane'],
                [['Oddanie budynku', 'Completion'], ['2029', '2029'], 'planowane'],
            ],
        ],
    ],

    'obrazy' => [
        'hero' => ['hero-zmierzch.jpg', 'Budynek Przystań Brda o zmierzchu, odbity w rzece'],
        'bulwar' => ['bulwar.jpg', 'Bulwar nad Brdą przed budynkiem, młode lipy i ławki'],
        'salon' => ['salon.jpg', 'Jasny salon z oknem na rzekę'],
        'kuchnia' => ['kuchnia.jpg', 'Kuchnia z granatowymi szafkami i stołem przy oknie'],
        'sypialnia' => ['sypialnia.jpg', 'Sypialnia z dużym oknem i widokiem na drzewa nad rzeką'],
        'budowa1' => ['budowa-fundamenty.jpg', 'Płyta fundamentowa i dźwig na placu budowy nad rzeką'],
        'budowa2' => ['budowa-stan-surowy.jpg', 'Budynek w stanie surowym z rusztowaniem od strony rzeki'],
        'budowa3' => ['budowa-elewacja.jpg', 'Murarze układają jasną cegłę elewacyjną na rusztowaniu'],
        'budowa4' => ['budowa-bulwar.jpg', 'Sadzenie lip i układanie granitu na nowym bulwarze'],
        'og' => ['og.jpg', 'Przystań Brda, mieszkania nad rzeką w Bydgoszczy'],
        'lipowa' => ['lipowa.jpg', 'Willa Lipowa: jasny budynek z drewnianymi wstawkami wśród lip'],
        'lipowa_wnetrze' => ['lipowa-wnetrze.jpg', 'Jasny salon z jodełką i oknem na korony lip'],
        'myslecinek' => ['myslecinek.jpg', 'Wizualizacja: Tarasy Myślęcinek, budynek z zielonymi tarasami przy lesie'],
    ],

    'strony' => [
        'glowna' => [
            'tytul' => ['pl' => 'Mieszkania nad Brdą', 'en' => 'Apartments by the Brda river'],
            'slug' => ['pl' => 'strona-glowna', 'en' => 'home'],
            'szablon' => 'default',
        ],
        'mieszkania' => [
            'tytul' => ['pl' => 'Mieszkania', 'en' => 'Apartments'],
            'slug' => ['pl' => 'mieszkania', 'en' => 'apartments'],
            'szablon' => 'template-mieszkania.blade.php',
            'kolejnosc' => 1,
            'tresc' => [
                'pl' => $akapity('Wskaż okno na elewacji albo zawęź listę filtrami. Statusy i ceny aktualizujemy na bieżąco, a link z wybranymi filtrami możesz wysłać bliskim.'),
                'en' => $akapity('Point to a window on the facade or narrow the list with filters. We keep statuses and prices up to date, and you can send a link with your filters to family.'),
            ],
            'seo' => [
                'pl' => '36 mieszkań nad Brdą: od 32 do 84 m², z balkonem, ogródkiem lub widokiem na rzekę. Sprawdź wolne lokale i ceny.',
                'en' => '36 apartments by the Brda river, 32 to 84 m², with a balcony, garden or river view. Check available units and prices.',
            ],
        ],
        'okolica' => [
            'tytul' => ['pl' => 'Okolica i standard', 'en' => 'Neighbourhood and finish'],
            'slug' => ['pl' => 'okolica', 'en' => 'neighbourhood'],
            'szablon' => 'template-okolica.blade.php',
            'kolejnosc' => 2,
            'tresc' => [
                'pl' => $akapity(
                    'Okole to jedna z najstarszych części Bydgoszczy. Rzeka płynie tu wolno, a nowy bulwar łączy osiedle z centrum. Rano biegają tu ludzie z psami, wieczorem siedzi się na schodach nad wodą.',
                    'Wszystko, czego potrzebujesz na co dzień, masz w zasięgu spaceru: sklepy, przychodnię, szkołę i przystanek tramwajowy. Do pracy w centrum dojedziesz rowerem ścieżką wzdłuż rzeki.',
                ),
                'en' => $akapity(
                    'Okole is one of the oldest parts of Bydgoszcz. The river flows slowly here and a new boulevard links the area with the city centre. In the morning people walk their dogs here, in the evening they sit on the steps by the water.',
                    'Everything you need every day is within walking distance: shops, a clinic, a school and a tram stop. You can cycle to work in the centre along the riverside path.',
                ),
            ],
        ],
        'porownanie' => [
            'tytul' => ['pl' => 'Ulubione', 'en' => 'Favourites'],
            'slug' => ['pl' => 'ulubione', 'en' => 'favourites'],
            'szablon' => 'template-porownanie.blade.php',
            'kolejnosc' => 6,
            'tresc' => [
                'pl' => $akapity('Mieszkania, które oznaczysz sercem, porównasz tu obok siebie: metraż, piętro, balkon, cena za metr. Lista zostaje w tej przeglądarce, bez zakładania konta.'),
                'en' => $akapity('Apartments you mark with a heart can be compared here side by side: area, floor, balcony, price per square metre. The list stays in this browser, no account needed.'),
            ],
        ],
        'dziennik' => [
            'tytul' => ['pl' => 'Dziennik budowy', 'en' => 'Construction diary'],
            'slug' => ['pl' => 'dziennik-budowy', 'en' => 'construction-diary'],
            'szablon' => 'default',
            'kolejnosc' => 3,
        ],
        'kontakt' => [
            'tytul' => ['pl' => 'Kontakt', 'en' => 'Contact'],
            'slug' => ['pl' => 'kontakt', 'en' => 'contact'],
            'szablon' => 'template-kontakt.blade.php',
            'kolejnosc' => 4,
        ],
        'prywatnosc' => [
            'tytul' => ['pl' => 'Polityka prywatności', 'en' => 'Privacy policy'],
            'slug' => ['pl' => 'polityka-prywatnosci', 'en' => 'privacy-policy'],
            'szablon' => 'default',
            'kolejnosc' => 5,
            'tresc' => [
                'pl' => $akapity(
                    '<strong>To jest strona demonstracyjna.</strong> Przystań Brda to fikcyjna inwestycja, a formularz służy do pokazania działania strony. Nie wpisuj w nim prawdziwych danych.',
                    'Dane z formularza (imię, e-mail, telefon, treść wiadomości) zapisujemy w panelu strony, żeby odpowiedzieć na zapytanie. Mogą też trafić do systemu obsługi klienta przez zabezpieczone połączenie (webhook). Przechowujemy je do 12 miesięcy od ostatniego kontaktu.',
                    'Strona nie używa ciasteczek reklamowych ani narzędzi śledzących. Nie wczytuje czcionek ani skryptów z zewnętrznych serwerów.',
                    'Masz prawo wglądu do swoich danych, ich poprawienia i usunięcia. W sprawach danych napisz na adres podany na stronie Kontakt.',
                ),
                'en' => $akapity(
                    '<strong>This is a demo website.</strong> Przystań Brda is a fictional development and the form is here to show how the site works. Please do not enter real data.',
                    'Form data (name, e-mail, phone, message) is stored in the site admin so we can reply. It may also be sent to a customer service system over a secured connection (webhook). We keep it for up to 12 months after the last contact.',
                    'The site uses no advertising cookies or tracking tools. It does not load fonts or scripts from external servers.',
                    'You have the right to access, correct and delete your data. For data questions, write to the address on the Contact page.',
                ),
            ],
        ],
    ],

    'glowna' => [
        'pl' => [
            'nadtytul' => 'Przystań Brda · Bydgoszcz, Okole',
            'naglowek' => "Mieszkania\ndwa kroki od rzeki",
            'wstep' => '36 mieszkań w spokojnym budynku przy nowym bulwarze. Z okien widać wodę, a do Starego Rynku dojdziesz w kwadrans.',
            'seo' => 'Przystań Brda: 36 mieszkań nad rzeką w Bydgoszczy, od 32 do 84 m². Balkony, ogródki, widok na Brdę. Oddanie w IV kwartale 2027.',
            'liczby' => [['36', 'mieszkań od 32 do 84 m²'], ['6', 'kondygnacji, na parterze ogródki'], ['120 m', 'bulwaru przed wejściem'], ['12 min', 'pieszo do Starego Rynku']],
            'atuty' => [
                ['Widok, którego nikt nie zabuduje', 'Budynek stoi frontem do rzeki, a po drugiej stronie jest park. Większość mieszkań ma okna na wodę.', 'salon'],
                ['Kuchnia z dziennym światłem', 'Każdy aneks ma okno. Pomieszczenia mają 2,75 m wysokości, a okna sięgają od podłogi.', 'kuchnia'],
                ['Sypialnie w ciszy', 'Za oknem korony drzew i rzeka, a nie parking. Okna mają podwyższoną izolację akustyczną.', 'sypialnia'],
            ],
            'okolica_naglowek' => 'Bulwar, park i Stary Rynek na piechotę',
            'okolica_wstep' => 'Przed domem ciągnie się nowy bulwar z ławkami i ścieżką rowerową. Do Wyspy Młyńskiej dojdziesz w 8 minut, do Starego Rynku w 12.',
        ],
        'en' => [
            'nadtytul' => 'Przystań Brda · Bydgoszcz, Okole',
            'naglowek' => "Apartments\ntwo steps from the river",
            'wstep' => '36 apartments in a calm building on the new boulevard. You can see the water from your windows and walk to the Old Market Square in 15 minutes.',
            'seo' => 'Przystań Brda: 36 riverside apartments in Bydgoszcz, 32 to 84 m². Balconies, gardens, views of the Brda. Ready in Q4 2027.',
            'liczby' => [['36', 'apartments, 32 to 84 m²'], ['6', 'storeys, gardens on the ground floor'], ['120 m', 'of boulevard by the entrance'], ['12 min', 'walk to the Old Market Square']],
            'atuty' => [
                ['A view no one can build over', 'The building faces the river with a park on the other bank. Most apartments have windows onto the water.', 'salon'],
                ['Kitchens with daylight', 'Every kitchen area has a window. Ceilings are 2.75 m high and windows reach down to the floor.', 'kuchnia'],
                ['Quiet bedrooms', 'Treetops and the river outside, not a car park. Windows have extra sound insulation.', 'sypialnia'],
            ],
            'okolica_naglowek' => 'Boulevard, park and the Old Town on foot',
            'okolica_wstep' => 'A new boulevard with benches and a bike path runs past the front door. Mill Island is an 8-minute walk, the Old Market Square 12 minutes.',
        ],
    ],

    'okolica' => [
        'pl' => [
            'wstep' => 'Spokojna część miasta nad wodą, a jednocześnie kilka minut od centrum. Sprawdź, ile zajmie Ci droga tam, gdzie chodzisz najczęściej.',
            'seo' => 'Okolica inwestycji Przystań Brda: bulwar, Wyspa Młyńska, Stary Rynek, szkoła i tramwaj w zasięgu spaceru. Standard wykończenia budynku.',
            'punkty' => [['Bulwar nad Brdą', 1, 'pieszo'], ['Przystanek tramwajowy', 3, 'pieszo'], ['Szkoła podstawowa', 6, 'pieszo'], ['Wyspa Młyńska', 8, 'pieszo'], ['Stary Rynek', 12, 'pieszo'], ['Dworzec Bydgoszcz Główna', 9, 'tramwajem']],
            'standard_naglowek' => 'Standard, który widać od progu',
            'standard' => ['Pomieszczenia o wysokości 2,75 m', 'Okna trzyszybowe z podwyższoną izolacją akustyczną', 'Ogrzewanie podłogowe z miejskiej sieci', 'Balkony i tarasy z balustradą ze szkła i stali', 'Winda z garażu na każde piętro', 'Komórki lokatorskie i rowerownia', 'Stanowiska do ładowania aut elektrycznych', 'Monitoring części wspólnych'],
        ],
        'en' => [
            'wstep' => 'A quiet part of town by the water, yet only minutes from the centre. See how long it takes to get to the places you go most often.',
            'seo' => 'Around Przystań Brda: the boulevard, Mill Island, the Old Market Square, a school and trams within walking distance. Building finish standard.',
            'punkty' => [['Brda boulevard', 1, 'pieszo'], ['Tram stop', 3, 'pieszo'], ['Primary school', 6, 'pieszo'], ['Mill Island', 8, 'pieszo'], ['Old Market Square', 12, 'pieszo'], ['Bydgoszcz Główna station', 9, 'tramwajem']],
            'standard_naglowek' => 'Quality you notice at the door',
            'standard' => ['Ceilings 2.75 m high', 'Triple-glazed windows with extra sound insulation', 'Underfloor heating from the city network', 'Balconies and terraces with glass and steel railings', 'Lift from the garage to every floor', 'Storage rooms and a bike room', 'Charging points for electric cars', 'CCTV in common areas'],
        ],
    ],

    'kontakt' => [
        'pl' => [
            'wstep' => 'Biuro sprzedaży jest na parterze budynku obok placu budowy. Najlepiej umówić się wcześniej: pokażemy rzuty i przejdziemy się bulwarem.',
            'godziny' => ['poniedziałek-piątek: 9:00-17:00', 'sobota: 10:00-14:00'],
            'seo' => 'Biuro sprzedaży Przystań Brda w Bydgoszczy: telefon, e-mail, godziny otwarcia i formularz zapytania o mieszkanie.',
        ],
        'en' => [
            'wstep' => 'The sales office is on the ground floor next to the construction site. Please book a visit: we will show you the floor plans and take a walk along the boulevard.',
            'godziny' => ['Monday-Friday: 9:00-17:00', 'Saturday: 10:00-14:00'],
            'seo' => 'Przystań Brda sales office in Bydgoszcz: phone, e-mail, opening hours and an apartment enquiry form.',
        ],
    ],

    'opisy_typow' => [
        'A' => [
            'pl' => $akapity('Kawalerka z dużym oknem: jeden jasny pokój z aneksem kuchennym, osobna łazienka i przedpokój z miejscem na szafę. Dobra na start albo na wynajem.'),
            'en' => $akapity('A studio with a large window: one bright room with a kitchen area, a separate bathroom and a hall with space for a wardrobe. Good for a first home or for renting out.'),
        ],
        'B' => [
            'pl' => $akapity('Dwa pokoje z garderobą. Salon z aneksem i sypialnia mają okna od strony rzeki, a garderoba zastępuje szafę w sypialni.'),
            'en' => $akapity('Two rooms with a walk-in wardrobe. The living room and bedroom both face the river, and the wardrobe replaces a bedroom closet.'),
        ],
        'C' => [
            'pl' => $akapity('Przestronne dwa pokoje ze schowkiem. Duży salon z aneksem, sypialnia na dwa łóżka lub łóżko i biurko, wygodny przedpokój.'),
            'en' => $akapity('Spacious two rooms with a storage room. A large living room with a kitchen area, a bedroom for a double bed or a bed and a desk, a comfortable hall.'),
        ],
        'D' => [
            'pl' => $akapity('Trzy pokoje dla rodziny. Salon i sypialnia od strony rzeki, mniejszy pokój dla dziecka lub do pracy z domu.'),
            'en' => $akapity('Three rooms for a family. The living room and main bedroom face the river, with a smaller room for a child or a home office.'),
        ],
        'E' => [
            'pl' => $akapity('Narożne trzy pokoje z oknami na dwie strony. Duża sypialnia, osobny pokój i salon z aneksem, w którym zmieści się stół dla sześciu osób.'),
            'en' => $akapity('A corner three-room apartment with windows on two sides. A large bedroom, a separate room and a living room with space for a table for six.'),
        ],
        'F' => [
            'pl' => $akapity('Największe mieszkanie w budynku: cztery pokoje, garderoba i duży salon. Na ostatnim piętrze z tarasem z widokiem na rzekę i park.'),
            'en' => $akapity('The largest apartment in the building: four rooms, a walk-in wardrobe and a big living room. On the top floor it comes with a terrace overlooking the river and park.'),
        ],
    ],

    'wpisy' => [
        'fundamenty' => [
            'data' => '2025-11-20',
            'zdjecie' => 'budowa1',
            'tytul' => ['pl' => 'Płyta fundamentowa gotowa', 'en' => 'Foundation slab completed'],
            'slug' => ['pl' => 'plyta-fundamentowa-gotowa', 'en' => 'foundation-slab-completed'],
            'zajawka' => ['pl' => 'Zalaliśmy płytę fundamentową i garaż podziemny. Nad placem stanął żuraw.', 'en' => 'The foundation slab and underground garage are poured. A tower crane now stands over the site.'],
            'tresc' => [
                'pl' => $akapity('Po sześciu tygodniach prac ziemnych zalaliśmy płytę fundamentową pod cały budynek i garaż podziemny. Beton dojrzewa, a nad placem stanął żuraw, który zostanie z nami do wiosny.', 'Ze względu na bliskość rzeki ściany garażu mają dodatkową izolację przeciwwodną. Prace przy brzegu prowadzimy w porozumieniu z zarządcą rzeki.'),
                'en' => $akapity('After six weeks of earthworks we poured the foundation slab for the whole building and the underground garage. The concrete is curing and a tower crane, which will stay with us until spring, now stands over the site.', 'Because the river is so close, the garage walls have extra waterproofing. Works near the bank are carried out together with the river authority.'),
            ],
        ],
        'stan-surowy' => [
            'data' => '2026-04-15',
            'zdjecie' => 'budowa2',
            'tytul' => ['pl' => 'Stan surowy zamknięty', 'en' => 'Building shell completed'],
            'slug' => ['pl' => 'stan-surowy-zamkniety', 'en' => 'building-shell-completed'],
            'zajawka' => ['pl' => 'Mamy wszystkie sześć kondygnacji i dach. Teraz okna i instalacje.', 'en' => 'All six storeys and the roof are done. Next: windows and installations.'],
            'tresc' => [
                'pl' => $akapity('Budynek ma już wszystkie sześć kondygnacji i dach. Zgodnie z harmonogramem zaczynamy montaż okien i instalacji w mieszkaniach.', 'Jeśli masz umowę rezerwacyjną, w maju zaprosimy Cię na pierwszy spacer po budowie w kasku. Szczegóły prześle opiekun klienta.'),
                'en' => $akapity('The building now has all six storeys and the roof. On schedule, we are starting to fit windows and installations in the apartments.', 'If you have a reservation agreement, in May we will invite you for a first hard-hat walk around the site. Your client advisor will send the details.'),
            ],
        ],
        'elewacja' => [
            'data' => '2026-07-10',
            'zdjecie' => 'budowa3',
            'tytul' => ['pl' => 'Elewacja z jasnej cegły', 'en' => 'Light brick facade going up'],
            'slug' => ['pl' => 'elewacja-z-jasnej-cegly', 'en' => 'light-brick-facade'],
            'zajawka' => ['pl' => 'Na rusztowaniach trwa murowanie elewacji. Cegła pochodzi z cegielni pod Bydgoszczą.', 'en' => 'Bricklayers are working on the facade. The bricks come from a brickworks near Bydgoszcz.'],
            'tresc' => [
                'pl' => $akapity('Na rusztowaniach trwa murowanie elewacji z jasnej cegły klinkierowej. Wybraliśmy ją, bo dobrze się starzeje i pasuje do zabytkowych spichrzy nad Brdą.', 'Okna są już zamontowane na wszystkich piętrach, więc w środku pracują ekipy instalacyjne i tynkarze.'),
                'en' => $akapity('Bricklayers are laying the facade in light clinker brick. We chose it because it ages well and matches the historic granaries along the Brda.', 'Windows are installed on every floor, so plumbers, electricians and plasterers are working inside.'),
            ],
        ],
        'bulwar' => [
            'data' => '2026-09-25',
            'zdjecie' => 'budowa4',
            'tytul' => ['pl' => 'Sadzimy lipy na bulwarze', 'en' => 'Planting lime trees on the boulevard'],
            'slug' => ['pl' => 'sadzimy-lipy-na-bulwarze', 'en' => 'planting-lime-trees'],
            'zajawka' => ['pl' => 'Przed budynkiem powstaje 120 metrów bulwaru z granitu, ławkami i dwunastoma lipami.', 'en' => 'In front of the building, 120 metres of granite boulevard with benches and twelve lime trees are taking shape.'],
            'tresc' => [
                'pl' => $akapity('Przed budynkiem powstaje 120 metrów nowego bulwaru. Układamy granit, montujemy ławki i sadzimy dwanaście lip, które za kilka lat dadzą cień.', 'Bulwar będzie ogólnodostępny i połączy się ze ścieżką rowerową w stronę centrum. Otwarcie planujemy razem z oddaniem budynku.'),
                'en' => $akapity('A new 120-metre boulevard is taking shape in front of the building. We are laying granite, installing benches and planting twelve lime trees that will give shade in a few years.', 'The boulevard will be open to everyone and will link to the bike path towards the centre. We plan to open it together with the building.'),
            ],
        ],
    ],
];
