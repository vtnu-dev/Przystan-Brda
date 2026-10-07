<?php
/**
 * Seed treści „Przystań Brda”: języki, strony PL/EN, 36 mieszkań w każdym języku, dziennik budowy,
 * menu, ustawienia i obrazy. Idempotentny: każdy obiekt ma meta _przystan_seed i przy ponownym
 * uruchomieniu jest aktualizowany, a nie dublowany.
 *
 * Uruchomienie (z katalogu projektu): wp eval-file scripts/seed.php
 */

use Przystan\Mieszkania\TypTresci as Mieszkania;
use Przystan\Mieszkania\Wyszukiwarka;
use Przystan\Ustawienia\Ustawienia;

if (! defined('ABSPATH')) {
    exit;
}
if (! function_exists('PLL') || ! class_exists(Mieszkania::class)) {
    WP_CLI::error('Włącz wtyczki polylang, advanced-custom-fields i przystan-core.');
}

require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

const SEED_MEDIA = __DIR__ . '/seed-media/';
$tresci = require __DIR__ . '/seed-tresci.php';

/* ------------------------------------------------------------------ pomocnicze */

function seed_znajdz(string $klucz, string $typ = 'any'): int
{
    $ids = get_posts([
        'post_type' => $typ,
        'post_status' => 'any',
        'posts_per_page' => 1,
        'fields' => 'ids',
        'meta_key' => '_przystan_seed',
        'meta_value' => $klucz,
        'lang' => '',
        'suppress_filters' => false,
    ]);

    return (int) ($ids[0] ?? 0);
}

/** @param array<string, mixed> $dane */
function seed_post(string $klucz, array $dane): int
{
    $id = seed_znajdz($klucz, $dane['post_type'] ?? 'post');
    $dane += ['post_status' => 'publish'];
    if ($id) {
        $dane['ID'] = $id;
        wp_update_post($dane);
    } else {
        $id = (int) wp_insert_post($dane, true);
        if (! $id) {
            WP_CLI::error("Nie udało się utworzyć: {$klucz}");
        }
        update_post_meta($id, '_przystan_seed', $klucz);
    }

    return $id;
}

function seed_obraz(string $plik, string $alt): int
{
    $klucz = 'media-' . $plik;
    $id = seed_znajdz($klucz, 'attachment');
    if (! $id) {
        $tmp = wp_tempnam($plik);
        copy(SEED_MEDIA . $plik, $tmp);
        $id = media_handle_sideload(['name' => basename($plik), 'tmp_name' => $tmp], 0);
        if (is_wp_error($id)) {
            WP_CLI::error("Obraz {$plik}: " . $id->get_error_message());
        }
        update_post_meta($id, '_przystan_seed', $klucz);
    }
    update_post_meta($id, '_wp_attachment_image_alt', $alt);

    return (int) $id;
}

/** @param array<string, mixed> $pola */
function seed_pola(int $postId, array $pola): void
{
    foreach ($pola as $nazwa => $wartosc) {
        update_field($nazwa, $wartosc, $postId);
    }
}

function seed_tlumaczenia(array $para): void
{
    foreach ($para as $jezyk => $id) {
        pll_set_post_language($id, $jezyk);
    }
    pll_save_post_translations($para);
}

/* ------------------------------------------------------------------ języki i Polylang */

WP_CLI::log('Języki…');
$modelJezykow = PLL()->model->languages ?? null;
foreach ([['locale' => 'pl_PL', 'slug' => 'pl', 'name' => 'Polski', 'term_group' => 0, 'flag' => 'pl'], ['locale' => 'en_GB', 'slug' => 'en', 'name' => 'English', 'term_group' => 1, 'flag' => 'gb']] as $jezyk) {
    if (! PLL()->model->get_language($jezyk['slug'])) {
        $wynik = $modelJezykow ? $modelJezykow->add($jezyk) : PLL()->model->add_language($jezyk);
        if (is_wp_error($wynik)) {
            WP_CLI::error('Polylang: ' . $wynik->get_error_message());
        }
    }
}
PLL()->model->clean_languages_cache();

$opcje = PLL()->options;
$opcje->set('default_lang', 'pl');
$opcje->set('hide_default', true);  // polski bez /pl/ w adresie
$opcje->set('force_lang', 1);       // język z katalogu: /en/...
$opcje->set('rewrite', true);       // bez /language/ w adresie
$opcje->set('browser', false);      // bez przekierowania wg języka przeglądarki (lepiej dla SEO i testów)
$opcje->set('redirect_lang', false);
$opcje->set('media_support', false); // obrazy wspólne dla obu języków
$opcje->set('post_types', [Mieszkania::TYP]);
$opcje->set('sync', ['taxonomies', 'post_meta', '_thumbnail_id', 'menu_order', '_wp_page_template']);
$opcje->save();

// Treść utworzona przed konfiguracją Polylang dostaje język domyślny.
PLL()->model->set_language_in_mass();

/* ------------------------------------------------------------------ ustawienia */

WP_CLI::log('Ustawienia…');
update_option('blogname', 'Przystań Brda');
update_option('blogdescription', $tresci['opis_strony']['pl']);
update_option('timezone_string', 'Europe/Warsaw');
update_option('date_format', 'j F Y');
update_option('WPLANG', 'pl_PL');
update_option('default_comment_status', 'closed');
update_option('default_ping_status', 'closed');
update_option('uploads_use_yearmonth_folders', 0);

$ustawienia = Ustawienia::wszystkie();
update_option(Ustawienia::OPCJA, array_merge($ustawienia, [
    'telefon' => '+48 52 000 00 00',
    'email' => 'sprzedaz@przystan.sitebest.eu',
    'adres' => "Biuro sprzedaży Przystań Brda\nul. Przykładowa 4\n85-000 Bydgoszcz",
    'termin' => 'IV kwartał 2027',
    'webhook_klucz' => $ustawienia['webhook_klucz'] ?: Ustawienia::nowyKlucz(),
]), false);

// Tłumaczenia ciągów z ustawień (Języki → Tłumaczenia ciągów).
$mo = new PLL_MO();
$en = PLL()->model->get_language('en');
$mo->import_from_db($en);
foreach (['IV kwartał 2027' => 'Q4 2027', "Biuro sprzedaży Przystań Brda\nul. Przykładowa 4\n85-000 Bydgoszcz" => "Przystań Brda sales office\nul. Przykładowa 4\n85-000 Bydgoszcz, Poland"] as $pl => $tlumaczenie) {
    $mo->add_entry($mo->make_entry($pl, $tlumaczenie));
}
$mo->export_to_db($en);

/* ------------------------------------------------------------------ obrazy */

WP_CLI::log('Obrazy…');
$o = [];
foreach ($tresci['obrazy'] as $nazwa => [$plik, $alt]) {
    $o[$nazwa] = seed_obraz($plik, $alt);
}
$rzuty = [];
foreach (['A', 'B', 'C', 'D', 'E', 'F'] as $typ) {
    foreach (['pl', 'en'] as $j) {
        $rzuty[$typ][$j] = seed_obraz("rzuty/rzut-{$typ}-{$j}.webp", sprintf($j === 'pl' ? 'Rzut mieszkania typu %s' : 'Floor plan, type %s', $typ));
    }
}

/* ------------------------------------------------------------------ strony */

WP_CLI::log('Strony…');
$strony = [];
foreach ($tresci['strony'] as $klucz => $strona) {
    $para = [];
    foreach (['pl', 'en'] as $j) {
        $id = seed_post("strona-{$klucz}-{$j}", [
            'post_type' => 'page',
            'post_title' => $strona['tytul'][$j],
            'post_name' => $strona['slug'][$j],
            'post_content' => $strona['tresc'][$j] ?? '',
            'menu_order' => $strona['kolejnosc'] ?? 0,
        ]);
        update_post_meta($id, '_wp_page_template', $strona['szablon'] ?? 'default');
        pll_set_post_language($id, $j);
        $para[$j] = $id;
    }
    pll_save_post_translations($para);
    $strony[$klucz] = $para;
}

// Pola ACF stron (po ustawieniu szablonu, bo od niego zależy grupa pól).
foreach (['pl', 'en'] as $j) {
    $g = $tresci['glowna'][$j];
    $pola = [
        'hero_nadtytul' => $g['nadtytul'],
        'hero_naglowek' => $g['naglowek'],
        'hero_wstep' => $g['wstep'],
        'hero_zdjecie' => $o['hero'],
        'okolica_naglowek' => $g['okolica_naglowek'],
        'okolica_wstep' => $g['okolica_wstep'],
        'okolica_zdjecie' => $o['bulwar'],
        'opis_seo' => $g['seo'],
    ];
    foreach ($g['liczby'] as $i => [$wartosc, $opis]) {
        $pola['liczba_' . ($i + 1) . '_wartosc'] = $wartosc;
        $pola['liczba_' . ($i + 1) . '_opis'] = $opis;
    }
    foreach ($g['atuty'] as $i => [$tytul, $opis, $zdjecie]) {
        $pola['atut_' . ($i + 1) . '_tytul'] = $tytul;
        $pola['atut_' . ($i + 1) . '_opis'] = $opis;
        $pola['atut_' . ($i + 1) . '_zdjecie'] = $o[$zdjecie];
    }
    seed_pola($strony['glowna'][$j], $pola);

    $ok = $tresci['okolica'][$j];
    $pola = [
        'wstep' => $ok['wstep'],
        'standard_naglowek' => $ok['standard_naglowek'],
        'standard_lista' => implode("\n", $ok['standard']),
        'zdjecie_1' => $o['bulwar'],
        'zdjecie_2' => $o['kuchnia'],
        'zdjecie_3' => $o['sypialnia'],
        'opis_seo' => $ok['seo'],
    ];
    foreach ($ok['punkty'] as $i => [$nazwa, $minuty, $jak]) {
        $pola['punkt_' . ($i + 1) . '_nazwa'] = $nazwa;
        $pola['punkt_' . ($i + 1) . '_minuty'] = $minuty;
        $pola['punkt_' . ($i + 1) . '_jak'] = $jak;
    }
    seed_pola($strony['okolica'][$j], $pola);

    seed_pola($strony['kontakt'][$j], [
        'wstep' => $tresci['kontakt'][$j]['wstep'],
        'godziny' => implode("\n", $tresci['kontakt'][$j]['godziny']),
        'opis_seo' => $tresci['kontakt'][$j]['seo'],
    ]);
    seed_pola($strony['mieszkania'][$j], ['opis_seo' => $tresci['strony']['mieszkania']['seo'][$j]]);
}

update_option('show_on_front', 'page');
update_option('page_on_front', $strony['glowna']['pl']);
update_option('page_for_posts', $strony['dziennik']['pl']);
update_option('wp_page_for_privacy_policy', $strony['prywatnosc']['pl']);

/* ------------------------------------------------------------------ mieszkania */

WP_CLI::log('Mieszkania…');
$typy = ['E', 'C', 'A', 'B', 'D', 'F']; // typ układu wg pozycji 1-6 (narożne największe)
$metraze = json_decode((string) file_get_contents(SEED_MEDIA . 'rzuty/metraze.json'), true);
$pokoje = ['A' => 1, 'B' => 2, 'C' => 2, 'D' => 3, 'E' => 3, 'F' => 4];
$opisyTypow = $tresci['opisy_typow'];

for ($pietro = 0; $pietro <= 5; $pietro++) {
    for ($pozycja = 1; $pozycja <= 6; $pozycja++) {
        $typ = $typy[$pozycja - 1];
        $numer = sprintf('M-%d%d', $pietro, $pozycja);
        $metraz = round($metraze[$typ] + (($pietro % 3) - 1) * 0.3, 1);
        $narozne = in_array($pozycja, [1, 2, 5, 6], true);
        $widok = $narozne || $pietro >= 2;
        $balkon = match (true) {
            $pietro === 0 => 0.0,
            $narozne => [1 => 6.5, 2 => 5.2, 5 => 7.4, 6 => 9.8][$pozycja] * ($pietro === 5 ? 2 : 1),
            $pietro >= 3 => 4.0,
            default => 0.0,
        };
        $stawka = 9400 + $pietro * 220 + ($widok ? 500 : 0) + ($pietro === 5 ? 800 : 0);
        $cena = ($pietro === 5 && $pozycja === 6) ? 0 : (int) (round($metraz * $stawka / 1000) * 1000);

        $los = crc32($numer) % 100;
        $sprzedane = 30 - $pietro * 4;
        $status = $los < $sprzedane ? 'sprzedane' : ($los < $sprzedane + 22 ? 'rezerwacja' : 'wolne');

        $wspolne = [
            'numer' => $numer,
            'pietro' => $pietro,
            'pozycja' => $pozycja,
            'pokoje' => $pokoje[$typ],
            'metraz' => $metraz,
            'cena' => $cena ?: '',
            'status' => $status,
            'balkon_m2' => $balkon,
            'ogrodek' => $pietro === 0 ? 1 : 0,
            'widok_na_rzeke' => $widok ? 1 : 0,
        ];

        $para = [];
        foreach (['pl', 'en'] as $j) {
            $id = seed_post("mieszkanie-{$numer}-{$j}", [
                'post_type' => Mieszkania::TYP,
                'post_title' => $numer,
                'post_name' => $j === 'pl' ? strtolower($numer) : strtolower(str_replace('-', '', $numer)),
                'post_content' => $opisyTypow[$typ][$j],
                'menu_order' => $pietro * 10 + $pozycja,
            ]);
            pll_set_post_language($id, $j);
            seed_pola($id, $wspolne + ['rzut' => $rzuty[$typ][$j]]);
            $para[$j] = $id;
        }
        pll_save_post_translations($para);
    }
}
Wyszukiwarka::wyczysc();

/* ------------------------------------------------------------------ dziennik budowy */

WP_CLI::log('Dziennik budowy…');
wp_delete_post(1, true); // „Witaj, świecie!”
foreach ($tresci['wpisy'] as $klucz => $wpis) {
    $para = [];
    foreach (['pl', 'en'] as $j) {
        $id = seed_post("wpis-{$klucz}-{$j}", [
            'post_type' => 'post',
            'post_title' => $wpis['tytul'][$j],
            'post_name' => $wpis['slug'][$j],
            'post_content' => $wpis['tresc'][$j],
            'post_excerpt' => $wpis['zajawka'][$j],
            'post_date' => $wpis['data'] . ' 10:00:00',
            'post_date_gmt' => get_gmt_from_date($wpis['data'] . ' 10:00:00'),
        ]);
        set_post_thumbnail($id, $o[$wpis['zdjecie']]);
        pll_set_post_language($id, $j);
        $para[$j] = $id;
    }
    pll_save_post_translations($para);
}

// Kategoria domyślna w obu językach.
$kategoriaPl = (int) get_option('default_category');
wp_update_term($kategoriaPl, 'category', ['name' => 'Dziennik budowy', 'slug' => 'dziennik']);
pll_set_term_language($kategoriaPl, 'pl');
$kategoriaEn = (int) pll_get_term($kategoriaPl, 'en');
if (! $kategoriaEn) {
    $term = term_exists('construction', 'category') ?: wp_insert_term('Construction diary', 'category', ['slug' => 'construction']);
    $kategoriaEn = (int) (is_array($term) ? $term['term_id'] : $term);
    pll_set_term_language($kategoriaEn, 'en');
    pll_save_term_translations(['pl' => $kategoriaPl, 'en' => $kategoriaEn]);
}
foreach ($tresci['wpisy'] as $klucz => $wpis) {
    wp_set_post_categories(seed_znajdz("wpis-{$klucz}-pl", 'post'), [$kategoriaPl]);
    wp_set_post_categories(seed_znajdz("wpis-{$klucz}-en", 'post'), [$kategoriaEn]);
}

/* ------------------------------------------------------------------ menu */

WP_CLI::log('Menu…');
$lokalizacje = [];
foreach (['pl', 'en'] as $j) {
    foreach (['glowne' => ['mieszkania', 'okolica', 'dziennik', 'kontakt'], 'stopka' => ['mieszkania', 'okolica', 'dziennik', 'kontakt', 'prywatnosc']] as $miejsce => $pozycje) {
        $nazwa = "Przystań {$miejsce} " . strtoupper($j);
        $menu = wp_get_nav_menu_object($nazwa);
        $menuId = $menu ? (int) $menu->term_id : (int) wp_create_nav_menu($nazwa);
        foreach (wp_get_nav_menu_items($menuId) ?: [] as $stara) {
            wp_delete_post($stara->ID, true);
        }
        foreach ($pozycje as $i => $strona) {
            wp_update_nav_menu_item($menuId, 0, [
                'menu-item-object-id' => $strony[$strona][$j],
                'menu-item-object' => 'page',
                'menu-item-type' => 'post_type',
                'menu-item-status' => 'publish',
                'menu-item-position' => $i + 1,
            ]);
        }
        $lokalizacje[$miejsce][$j] = $menuId;
    }
}
$motyw = get_stylesheet();
$opcje->set('nav_menus', [$motyw => $lokalizacje]);
$opcje->save();
set_theme_mod('nav_menu_locations', ['glowne' => $lokalizacje['glowne']['pl'], 'stopka' => $lokalizacje['stopka']['pl']]);

/* ------------------------------------------------------------------ porządki */

update_option('permalink_structure', '/%postname%/');
flush_rewrite_rules(true);

WP_CLI::success(sprintf('Gotowe: %d stron, %d mieszkań, %d wpisów (na język).', count($strony), 36, count($tresci['wpisy'])));
