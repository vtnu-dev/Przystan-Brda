<?php

/**
 * Plugin Name:       Przystań Brda - rdzeń
 * Description:       Dane i logika inwestycji: mieszkania, wyszukiwarka (REST), zapytania z webhookiem, ustawienia.
 * Version:           1.0.0
 * Requires at least: 6.6
 * Requires PHP:      8.3
 * Author:            SiteBest
 * Author URI:        https://sitebest.eu
 * Text Domain:       przystan
 * Domain Path:       /languages
 */

if (! defined('ABSPATH')) {
    exit;
}

define('PRZYSTAN_CORE_FILE', __FILE__);
define('PRZYSTAN_CORE_DIR', __DIR__);
define('PRZYSTAN_CORE_VERSION', '1.0.0');

// Prosty autoloader PSR-4, żeby wtyczka działała także poza Bedrockiem (bez własnego vendor/).
spl_autoload_register(static function (string $klasa): void {
    if (! str_starts_with($klasa, 'Przystan\\')) {
        return;
    }
    $plik = __DIR__ . '/src/' . str_replace('\\', '/', substr($klasa, strlen('Przystan\\'))) . '.php';
    if (is_file($plik)) {
        require $plik;
    }
});

register_activation_hook(__FILE__, [Przystan\Plugin::class, 'aktywacja']);
register_deactivation_hook(__FILE__, [Przystan\Plugin::class, 'dezaktywacja']);

Przystan\Plugin::boot();
