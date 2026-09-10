<?php

function loadEnv(string $path): void
{
    if (!file_exists($path)) {
        throw new Exception('.env tidak ditemukan.');
    }

    $lines = file(
        $path,
        FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES
    );

    foreach ($lines as $line) {

        $line = trim($line);

        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }

        [$name, $value] = array_pad(
            explode('=', $line, 2),
            2,
            ''
        );

        $_ENV[trim($name)] = trim($value, " \"'");
    }
}


function getDatabaseConnection(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    loadEnv(dirname(__DIR__) . '/.env');

    $dsn =
        'pgsql:' .
        'host=' . $_ENV['DB_HOST'] .
        ';port=' . $_ENV['DB_PORT'] .
        ';dbname=' . $_ENV['DB_NAME'];

    $pdo = new PDO(
        $dsn,
        $_ENV['DB_USER'],
        $_ENV['DB_PASSWORD'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );

    /*
     * Schema aplikasi kita
     */
    $pdo->exec('SET search_path TO ramah_tamah');

    return $pdo;
}
