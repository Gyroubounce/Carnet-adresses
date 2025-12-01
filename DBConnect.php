<?php
class DBConnect {
    private ?PDO $pdo = null;

    public function __construct() {
        try {
            $this->pdo = new PDO(
                "mysql:host=localhost;dbname=carnet_adresses;charset=utf8mb4",
                "root",   // utilisateur Laragon
                ""        // mot de passe vide par défaut
            );
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Erreur de connexion : " . $e->getMessage());
        }
    }

    public function getPDO(): ?PDO {
        return $this->pdo;
    }
}
