<?php
declare(strict_types=1);

/**
 * DBConnect : Singleton pour la connexion PDO.
 * - Une seule instance PDO pour tout le projet.
 * - Chargement des identifiants depuis config.php (non versionné).
 */

final class DBConnect
{
    /** @var ?PDO */
    private static ?PDO $pdo = null;

    /** @var array<string,mixed> */
    private static array $config = [];

    private function __construct() {}

    /**
     * Initialise la configuration si nécessaire.
     */
    private static function initConfig(): void
    {
        if (empty(self::$config)) {
            /** @var array<string,mixed> $cfg */
            $cfg = require __DIR__ . '/config.php';
            self::$config = $cfg;
        }
    }

    /**
     * Retourne l'instance PDO unique.
     */
    public static function getPDO(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        try {
            self::initConfig();

            $dsn = sprintf(
                '%s:host=%s;dbname=%s;charset=%s',
                self::$config['driver'],
                self::$config['host'],
                self::$config['dbname'],
                self::$config['charset']
            );

            self::$pdo = new PDO(
                $dsn,
                (string) self::$config['user'],
                (string) self::$config['password'],
                self::$config['options']
            );
        } catch (PDOException $e) {
            // Log technique et message utilisateur neutre
            error_log('Erreur de connexion PDO : ' . $e->getMessage());
            echo "Impossible de se connecter à la base de données. Réessayez plus tard.\n";
            exit(1);
        }

        return self::$pdo;
    }
}
