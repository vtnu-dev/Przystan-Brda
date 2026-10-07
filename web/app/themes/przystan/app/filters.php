<?php

/**
 * Filtry motywu.
 */

namespace App;

add_filter('excerpt_more', fn() => '…');
add_filter('excerpt_length', fn() => 24);

/**
 * Menu: klasa aktywnej pozycji jako aria-current (czytniki ekranu ogłaszają bieżącą stronę).
 */
add_filter('nav_menu_link_attributes', function (array $atrybuty, $pozycja) {
    if (in_array('current-menu-item', (array) $pozycja->classes, true)) {
        $atrybuty['aria-current'] = 'page';
    }

    return $atrybuty;
}, 10, 2);

/**
 * Karta mieszkania w menu zaznacza pozycję „Mieszkania”.
 */
add_filter('nav_menu_css_class', function (array $klasy, $pozycja) {
    if (is_singular('mieszkanie') && (int) $pozycja->object_id === Strony::id(Strony::MIESZKANIA)) {
        $klasy[] = 'current-menu-item';
    }
    if (is_singular('post') && (int) $pozycja->object_id === (int) get_option('page_for_posts')) {
        $klasy[] = 'current-menu-item';
    }

    return $klasy;
}, 10, 2);

/**
 * Lista „Dziennik budowy” bez paginacji po 9 wpisach.
 */
add_action('pre_get_posts', function (\WP_Query $zapytanie) {
    if (! is_admin() && $zapytanie->is_main_query() && $zapytanie->is_home()) {
        $zapytanie->set('posts_per_page', 9);
    }
});
