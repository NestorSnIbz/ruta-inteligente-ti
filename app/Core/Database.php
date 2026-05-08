<?php

final class Database
{
    private static ?PDO $pdo = null;
    private static array $dotenv = [];

    public static function pdo(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        // Carga .env para permitir despliegue en Render (variables por entorno) y desarrollo local.
        self::$dotenv = self::loadDotenv(dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . '.env');
        $databaseUrl = (string) self::env('DATABASE_URL', '');

        if ($databaseUrl === '') {
            throw new RuntimeException('Falta DATABASE_URL en el archivo .env.');
        }

        $parts = parse_url($databaseUrl);
        if (!is_array($parts) || empty($parts['host']) || empty($parts['path'])) {
            throw new RuntimeException('DATABASE_URL no tiene un formato válido.');
        }

        // DATABASE_URL se usa para crear el DSN de PDO con SSL (Supabase Postgres).
        $host = (string) ($parts['host'] ?? '');
        $port = (int) ($parts['port'] ?? 5432);
        $db = ltrim((string) ($parts['path'] ?? ''), '/');
        $user = urldecode((string) ($parts['user'] ?? ''));
        $pass = urldecode((string) ($parts['pass'] ?? ''));

        $baseDsn = 'pgsql:host=' . $host . ';port=' . $port . ';dbname=' . $db . ';sslmode=require';
        $hostaddr = (string) self::env('DATABASE_HOSTADDR', '');
        if ($hostaddr !== '') {
            $baseDsn = 'pgsql:host=' . $host . ';hostaddr=' . $hostaddr . ';port=' . $port . ';dbname=' . $db . ';sslmode=require';
        }

        try {
            self::$pdo = new PDO($baseDsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            $message = $e->getMessage();
            $lower = strtolower($message);

            if (str_contains($lower, 'network is unreachable')) {
                $ipv4 = self::resolveIpv4($host);
                if ($ipv4 !== null) {
                    $fallbackDsn = 'pgsql:host=' . $host . ';hostaddr=' . $ipv4 . ';port=' . $port . ';dbname=' . $db . ';sslmode=require';
                    self::$pdo = new PDO($fallbackDsn, $user, $pass, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ]);
                } else {
                    throw $e;
                }
            } else {
                throw $e;
            }
        }

        return self::$pdo;
    }

    private static function env(string $key, mixed $default = null): mixed
    {
        $value = getenv($key);
        if ($value !== false && $value !== '') {
            return $value;
        }

        if (array_key_exists($key, self::$dotenv) && self::$dotenv[$key] !== '') {
            return self::$dotenv[$key];
        }

        return $default;
    }

    private static function loadDotenv(string $filePath): array
    {
        if (!is_file($filePath)) {
            return [];
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            return [];
        }

        $vars = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            $pos = strpos($line, '=');
            if ($pos === false) {
                continue;
            }

            $k = trim(substr($line, 0, $pos));
            $v = trim(substr($line, $pos + 1));

            if ($v !== '' && (($v[0] === '"' && str_ends_with($v, '"')) || ($v[0] === "'" && str_ends_with($v, "'")))) {
                $v = substr($v, 1, -1);
            }

            if ($k !== '') {
                $vars[$k] = $v;
            }
        }

        return $vars;
    }

    private static function resolveIpv4(string $host): ?string
    {
        $records = @dns_get_record($host, DNS_A);
        if (is_array($records) && !empty($records)) {
            foreach ($records as $record) {
                if (is_array($record) && isset($record['ip']) && is_string($record['ip']) && $record['ip'] !== '') {
                    return $record['ip'];
                }
            }
        }

        $ips = @gethostbynamel($host);
        if (is_array($ips) && !empty($ips) && is_string($ips[0]) && $ips[0] !== '') {
            return $ips[0];
        }

        $ip = @gethostbyname($host);
        if (is_string($ip) && $ip !== '' && $ip !== $host && filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return $ip;
        }

        return null;
    }
}
