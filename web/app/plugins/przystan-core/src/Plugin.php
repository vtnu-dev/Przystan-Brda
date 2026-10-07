<?php

namespace Przystan;

/**
 * Punkt wejścia wtyczki: każdy moduł rejestruje własne hooki w metodzie rejestruj().
 */
final class Plugin
{
    public static function boot(): void
    {
        add_action('init', static function (): void {
            load_plugin_textdomain('przystan', false, dirname(plugin_basename(PRZYSTAN_CORE_FILE)) . '/languages');
        }, 0);

        Mieszkania\TypTresci::rejestruj();
        Mieszkania\Pola::rejestruj();
        Strony\Pola::rejestruj();
        Polylang\Integracja::rejestruj();
        Rest\MieszkaniaController::rejestruj();
        Rest\ZapytaniaController::rejestruj();
        Zapytania\TypTresci::rejestruj();
        Zapytania\FormularzBezJs::rejestruj();
        Webhook\Wysylka::rejestruj();
        Ustawienia\Strona::rejestruj();
    }

    public static function aktywacja(): void
    {
        Ustawienia\Ustawienia::zapewnijKlucz();
        Mieszkania\TypTresci::typ();
        Zapytania\TypTresci::typ();
        flush_rewrite_rules();
    }

    public static function dezaktywacja(): void
    {
        wp_clear_scheduled_hook(Webhook\Wysylka::HOOK);
        flush_rewrite_rules();
    }
}
