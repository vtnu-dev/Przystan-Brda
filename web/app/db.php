<?php
/**
 * Lokalnie (DB_ENGINE=sqlite w .env) WordPress działa na SQLite przez oficjalną wtyczkę
 * sqlite-database-integration (zależność tylko dev). Na produkcji ten plik nic nie robi
 * i WordPress używa zwykłego MySQL.
 */
if (($_ENV['DB_ENGINE'] ?? getenv('DB_ENGINE')) !== 'sqlite') {
    return;
}

$przystan_sqlite = __DIR__ . '/plugins/sqlite-database-integration';
if (! is_file($przystan_sqlite . '/wp-includes/sqlite/db.php')) {
    return;
}

defined('DB_ENGINE') || define('DB_ENGINE', 'sqlite');
defined('DB_DIR') || define('DB_DIR', __DIR__ . '/database/');
require_once $przystan_sqlite . '/wp-includes/sqlite/db.php';
