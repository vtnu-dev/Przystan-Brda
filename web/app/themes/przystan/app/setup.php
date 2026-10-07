<?php

/**
 * Konfiguracja motywu.
 */

namespace App;

use Illuminate\Support\Facades\Vite;

/**
 * Style edytora bloków (wpisy dziennika budowy) zgodne z frontem.
 */
add_filter('block_editor_settings_all', function ($settings) {
    $style = Vite::asset('resources/css/editor.css');

    $settings['styles'][] = [
        'css' => "@import url('{$style}')",
    ];

    return $settings;
});

add_action('admin_head', function () {
    if (! get_current_screen()?->is_block_editor()) {
        return;
    }

    if (! Vite::isRunningHot()) {
        $dependencies = json_decode(Vite::content('editor.deps.json'));

        foreach ($dependencies as $dependency) {
            if (! wp_script_is($dependency)) {
                wp_enqueue_script($dependency);
            }
        }
    }
    echo Vite::withEntryPoints([
        'resources/js/editor.js',
    ])->toHtml();
});

/**
 * theme.json generowany przez Vite z ustawień Tailwinda.
 */
add_filter('theme_file_path', function ($path, $file) {
    return $file === 'theme.json'
        ? public_path('build/assets/theme.json')
        : $path;
}, 10, 2);

add_filter('should_load_separate_core_block_assets', '__return_false');

add_action('after_setup_theme', function () {
    remove_theme_support('block-templates');
    remove_theme_support('core-block-patterns');

    register_nav_menus([
        'glowne' => __('Menu główne', 'przystan'),
        'stopka' => __('Menu w stopce', 'przystan'),
    ]);

    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('responsive-embeds');
    add_theme_support('html5', ['caption', 'gallery', 'search-form', 'script', 'style']);

    // Rozmiary pod układ strony (srcset dobiera właściwy na telefonie i komputerze).
    add_image_size('hero', 1920, 1072, true);
    add_image_size('hero-telefon', 828, 1100, true);
    add_image_size('karta', 800, 600, true);
    add_image_size('szeroki', 1376, 768, true);
}, 20);

/**
 * Nowe obrazy zapisujemy jako WebP (mniejsze pliki, lepszy LCP).
 */
add_filter('image_editor_output_format', function (array $formaty) {
    return $formaty + [
        'image/jpeg' => 'image/webp',
        'image/png' => 'image/webp',
    ];
});
add_filter('wp_editor_set_quality', fn() => 78);
add_filter('big_image_size_threshold', fn() => 2000);

/**
 * Bez zbędnych skryptów i stylów na froncie: emoji, oEmbed, style bloków poza wpisami.
 */
add_action('init', function () {
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
    add_filter('emoji_svg_url', '__return_false');

    remove_action('wp_head', 'wp_oembed_add_discovery_links');
    remove_action('wp_head', 'wp_oembed_add_host_js');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'wp_shortlink_wp_head');
    remove_action('wp_head', 'rest_output_link_wp_head');
});

add_action('wp_enqueue_scripts', function () {
    if (! is_singular('post')) {
        wp_dequeue_style('wp-block-library');
        wp_dequeue_style('wp-block-library-theme');
        wp_dequeue_style('global-styles');
        wp_dequeue_style('classic-theme-styles');
    }
}, 100);

add_filter('wp_resource_hints', function (array $adresy, string $typ) {
    return $typ === 'dns-prefetch' ? array_filter($adresy, fn($a) => ! str_contains((string) (is_array($a) ? ($a['href'] ?? '') : $a), 's.w.org')) : $adresy;
}, 10, 2);
