<?php

/**
 * Plugin Name: Przystań Brda - bezpieczeństwo
 * Description: Hardening: bez XML-RPC, bez listy użytkowników w REST, bez wyliczania autorów, nagłówki bezpieczeństwa.
 * Version:     1.0.0
 * Author:      SiteBest
 */

if (! defined('ABSPATH')) {
    exit;
}

// XML-RPC: wyłączony (stary punkt ataków słownikowych), bez nagłówka X-Pingback.
add_filter('xmlrpc_enabled', '__return_false');
add_filter('wp_headers', static function (array $naglowki): array {
    unset($naglowki['X-Pingback']);

    return $naglowki;
});
remove_action('wp_head', 'rsd_link');

// Wersja WordPressa nie trafia do kodu strony ani kanałów RSS.
remove_action('wp_head', 'wp_generator');
add_filter('the_generator', '__return_empty_string');

// Lista użytkowników przez REST tylko dla zalogowanych redaktorów.
add_filter('rest_endpoints', static function (array $trasy): array {
    if (current_user_can('list_users')) {
        return $trasy;
    }
    foreach (array_keys($trasy) as $trasa) {
        if (str_starts_with($trasa, '/wp/v2/users')) {
            unset($trasy[$trasa]);
        }
    }

    return $trasy;
});

// Wyliczanie loginów przez ?author=1 i /author/nazwa/ → przekierowanie na stronę główną.
add_action('template_redirect', static function (): void {
    if (is_author() || (isset($_GET['author']) && ! is_admin())) {
        wp_safe_redirect(home_url('/'), 301);
        exit;
    }
});
add_filter('wp_sitemaps_add_provider', static fn($dostawca, string $nazwa) => $nazwa === 'users' ? false : $dostawca, 10, 2);

// Ogólny komunikat przy błędnym logowaniu (bez podpowiedzi, czy istnieje login).
add_filter('login_errors', static fn() => __('Nieprawidłowe dane logowania.', 'przystan'));

// Nagłówki bezpieczeństwa na froncie. X-Frame-Options, X-Content-Type-Options i Referrer-Policy
// ustawia już nginx (szablon vhosta CloudPanel), więc tu tylko to, czego serwer nie daje.
// CSP tylko dla niezalogowanych na produkcji (pasek admina i podgląd Vite w dev używają skryptów spoza tej listy).
add_action('send_headers', static function (): void {
    if (is_admin()) {
        return;
    }
    header('Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=(), usb=()');

    if (wp_get_environment_type() === 'production' && ! is_user_logged_in()) {
        header("Content-Security-Policy: default-src 'self'; script-src 'self' 'inline-speculation-rules'; style-src 'self' 'unsafe-inline'; img-src 'self' data:; font-src 'self'; connect-src 'self'; form-action 'self'; frame-ancestors 'self'; base-uri 'self'; object-src 'none'; upgrade-insecure-requests");
    }
});
