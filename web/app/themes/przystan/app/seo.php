<?php

/**
 * SEO techniczne bez wtyczki SEO: tytuły, meta description, Open Graph, noindex sterowany z .env,
 * dane strukturalne JSON-LD. Canonical i hreflang dają WordPress i Polylang.
 */

namespace App;

use Przystan\Inwestycje\Inwestycja;
use Przystan\Mieszkania\Mieszkanie;
use Przystan\Ustawienia\Ustawienia;

add_filter('document_title_separator', fn() => '|');

/**
 * Tytuł karty mieszkania z najważniejszymi parametrami (lepszy wynik w Google niż samo „M-35”).
 */
add_filter('document_title_parts', function (array $czesci) {
    if (is_singular('mieszkanie')) {
        $m = Mieszkanie::zPosta(get_post());
        $czesci['title'] = sprintf('%s, %s, %s', sprintf(__('Mieszkanie %s', 'przystan'), $m['numer']), $m['pokoje_tekst'], $m['metraz_tekst']);
    }
    if (is_front_page()) {
        $czesci['title'] = get_bloginfo('name') . ': ' . get_bloginfo('description');
        unset($czesci['tagline'], $czesci['site']);
    }

    return $czesci;
});

/**
 * Cała strona demo ma noindex (PRZYSTAN_NOINDEX=true w .env); na prawdziwej inwestycji wystarczy zmienić zmienną.
 */
add_filter('wp_robots', function (array $robots) {
    if (defined('PRZYSTAN_NOINDEX') && PRZYSTAN_NOINDEX) {
        $robots['noindex'] = true;
        $robots['nofollow'] = true;
        unset($robots['max-image-preview']);
    }

    return $robots;
});

function opis(): string
{
    $id = get_queried_object_id();
    $opis = $id && function_exists('get_field') ? (string) get_field('opis_seo', $id) : '';

    if ($opis === '' && is_singular('mieszkanie')) {
        $m = Mieszkanie::zPosta(get_post());
        $opis = $m['opis'] . ($m['cena_tekst'] ? '. ' . $m['cena_tekst'] : '') . '. ' . get_bloginfo('description') . '.';
    } elseif ($opis === '' && is_singular()) {
        $opis = wp_strip_all_tags(get_the_excerpt($id));
    } elseif ($opis === '' && is_home()) {
        $opis = __('Postęp prac co kilka tygodni: zdjęcia z placu budowy i najważniejsze etapy.', 'przystan');
    }

    return trim(wp_html_excerpt($opis ?: get_bloginfo('description'), 160, '…'));
}

function obrazUdostepniania(): ?array
{
    $id = 0;
    if (is_singular('mieszkanie')) {
        $id = (int) get_post_meta(get_queried_object_id(), 'rzut', true);
    } elseif (is_singular() && has_post_thumbnail()) {
        $id = (int) get_post_thumbnail_id();
    }
    if (! $id) {
        $id = (int) get_posts(['post_type' => 'attachment', 'post_status' => 'inherit', 'posts_per_page' => 1, 'fields' => 'ids', 'meta_key' => '_przystan_seed', 'meta_value' => 'media-og.jpg'])[0] ?? 0;
    }
    $obraz = $id ? wp_get_attachment_image_src($id, 'full') : false;

    return $obraz ? ['url' => $obraz[0], 'w' => $obraz[1], 'h' => $obraz[2], 'alt' => (string) get_post_meta($id, '_wp_attachment_image_alt', true)] : null;
}

function aktualnyUrl(): string
{
    if (is_singular()) {
        return (string) get_permalink();
    }
    if (is_home() && get_option('page_for_posts')) {
        return (string) get_permalink((int) get_option('page_for_posts'));
    }

    return Strony::strefaGlowna();
}

add_action('wp_head', function () {
    if (is_404()) {
        return;
    }
    $tytul = wp_get_document_title();
    $opis = opis();
    $obraz = obrazUdostepniania();
    $locale = str_replace('-', '_', get_bloginfo('language'));

    $meta = [
        ['name', 'description', $opis],
        ['property', 'og:type', is_singular('post') ? 'article' : 'website'],
        ['property', 'og:site_name', get_bloginfo('name')],
        ['property', 'og:title', $tytul],
        ['property', 'og:description', $opis],
        ['property', 'og:url', aktualnyUrl()],
        ['property', 'og:locale', $locale],
        ['name', 'twitter:card', 'summary_large_image'],
    ];
    if (function_exists('pll_languages_list')) {
        foreach (pll_languages_list(['fields' => 'locale']) as $inny) {
            if ($inny !== $locale) {
                $meta[] = ['property', 'og:locale:alternate', $inny];
            }
        }
    }
    if ($obraz) {
        array_push($meta, ['property', 'og:image', $obraz['url']], ['property', 'og:image:width', (string) $obraz['w']], ['property', 'og:image:height', (string) $obraz['h']], ['property', 'og:image:alt', $obraz['alt']]);
    }

    foreach ($meta as [$atrybut, $nazwa, $wartosc]) {
        if ($wartosc !== '') {
            printf("<meta %s=\"%s\" content=\"%s\">\n", $atrybut, esc_attr($nazwa), esc_attr($wartosc));
        }
    }
}, 2);

/**
 * JSON-LD. Dane strukturalne opisują to samo, co widać na stronie (wymóg Google).
 */
add_action('wp_head', function () {
    if (is_404()) {
        return;
    }
    $u = Ustawienia::wszystkie();
    $organizacja = [
        '@type' => 'Organization',
        '@id' => home_url('/#organizacja'),
        'name' => get_bloginfo('name'),
        'url' => home_url('/'),
        'telephone' => $u['telefon'],
        'email' => $u['email'],
    ];
    $graf = [$organizacja];

    // Inwestycja jako ApartmentComplex: na swojej stronie pełny opis, w karcie mieszkania jako „containedInPlace”.
    $kompleks = static function (int $id, bool $pelny): array {
        $inw = Inwestycja::zId($id);
        $dane = [
            '@type' => 'ApartmentComplex',
            '@id' => $inw['url'] . '#inwestycja',
            'name' => $inw['nazwa'],
            'url' => $inw['url'],
            'address' => ['@type' => 'PostalAddress', 'addressLocality' => $inw['lokalizacja'], 'addressCountry' => 'PL'],
        ];
        if ($pelny) {
            $dane += [
                'description' => $inw['zajawka'],
                'numberOfAccommodationUnits' => $inw['mieszkan'] ?: null,
                'numberOfAvailableAccommodationUnits' => $inw['mieszkan'] ? $inw['wolnych'] : null,
                'image' => $inw['zdjecie'] ? wp_get_attachment_image_url($inw['zdjecie'], 'szeroki') : null,
            ];
        }

        return array_filter($dane, fn($v) => $v !== null);
    };

    if (is_singular('inwestycja')) {
        $graf[] = $kompleks(get_queried_object_id(), true);
        $graf[] = okruszkiLd([[__('Inwestycje', 'przystan'), (string) get_post_type_archive_link('inwestycja')], [get_the_title(), (string) get_permalink()]]);
    }

    if (is_singular('mieszkanie')) {
        $m = Mieszkanie::zPosta(get_post());
        $dostepnosc = ['wolne' => 'https://schema.org/InStock', 'rezerwacja' => 'https://schema.org/LimitedAvailability', 'sprzedane' => 'https://schema.org/SoldOut'][$m['status']];
        $mieszkanie = [
            '@type' => 'Apartment',
            'name' => sprintf(__('Mieszkanie %s', 'przystan'), $m['numer']),
            'url' => $m['url'],
            'numberOfRooms' => $m['pokoje'],
            'floorLevel' => (string) $m['pietro'],
            'floorSize' => ['@type' => 'QuantitativeValue', 'value' => $m['metraz'], 'unitCode' => 'MTK'],
            'containedInPlace' => $m['inwestycja'] ? $kompleks($m['inwestycja'], false) : null,
        ];
        if ($m['rzut']) {
            $mieszkanie['image'] = $m['rzut'];
        }
        $graf[] = $mieszkanie;
        $graf[] = [
            '@type' => 'Offer',
            'url' => $m['url'],
            'itemOffered' => ['@type' => 'Apartment', 'name' => $mieszkanie['name']],
            'availability' => $dostepnosc,
            'priceCurrency' => 'PLN',
            'price' => $m['cena'] ?: null,
            'seller' => ['@id' => home_url('/#organizacja')],
        ];
        $graf[] = okruszkiLd([[$m['inwestycja_nazwa'] ?: __('Mieszkania', 'przystan'), $m['inwestycja'] ? (string) get_permalink($m['inwestycja']) : Strony::url(Strony::MIESZKANIA)], [$m['numer'], $m['url']]]);
    }

    if (is_singular('post')) {
        $graf[] = [
            '@type' => 'BlogPosting',
            'headline' => get_the_title(),
            'datePublished' => get_post_time('c', true),
            'dateModified' => get_post_modified_time('c', true),
            'inLanguage' => get_bloginfo('language'),
            'image' => get_the_post_thumbnail_url(null, 'szeroki') ?: null,
            'author' => ['@id' => home_url('/#organizacja')],
            'publisher' => ['@id' => home_url('/#organizacja')],
            'mainEntityOfPage' => get_permalink(),
        ];
        $blog = (int) get_option('page_for_posts');
        $graf[] = okruszkiLd([[get_the_title($blog), (string) get_permalink($blog)], [get_the_title(), (string) get_permalink()]]);
    }

    $dane = ['@context' => 'https://schema.org', '@graph' => array_values(array_map(fn($w) => array_filter($w, fn($v) => $v !== null && $v !== ''), $graf))];
    echo '<script type="application/ld+json">' . wp_json_encode($dane, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) . "</script>\n";
}, 3);

/** @param list<array{0: string, 1: string}> $sciezka */
function okruszkiLd(array $sciezka): array
{
    array_unshift($sciezka, [__('Strona główna', 'przystan'), Strony::strefaGlowna()]);

    return [
        '@type' => 'BreadcrumbList',
        'itemListElement' => array_map(fn($el, $i) => ['@type' => 'ListItem', 'position' => $i + 1, 'name' => $el[0], 'item' => $el[1]], $sciezka, array_keys($sciezka)),
    ];
}

/**
 * Mapa strony: bez użytkowników (w mu-pluginie) i bez kategorii (jedna kategoria = zbędny duplikat listy wpisów).
 */
add_filter('wp_sitemaps_taxonomies', fn() => []);
