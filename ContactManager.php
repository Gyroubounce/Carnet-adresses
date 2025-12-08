<?php
declare(strict_types=1);

/**
 * ContactManager :
 * - Contient la logique d'accès à la base (CRUD).
 * - Retourne des objets Contact (jamais des tableaux bruts pour appelant).
 * - Requêtes préparées (anti injection).
 */

require_once __DIR__ . '/DBConnect.php';
require_once __DIR__ . '/Contact.php';

class ContactManager
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = DBConnect::getPDO();
    }

    /**
     * Crée un contact et retourne l'entité créée.
     */
    public function create(string $name, string $email, ?string $phone): ?Contact
    {
        try {
            $stmt = $this->pdo->prepare(
                'INSERT INTO contact (name, email, phone_number) VALUES (:name, :email, :phone)'
            );
            $stmt->execute([
                ':name'  => $name,
                ':email' => $email,
                ':phone' => $phone
            ]);

            $id = (int) $this->pdo->lastInsertId();
            return new Contact($id, $name, $email, $phone);
        } catch (PDOException $e) {
            error_log('Erreur create contact : ' . $e->getMessage());
            return null;
        } catch (InvalidArgumentException $e) {
            error_log('Validation create contact : ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Retourne tous les contacts sous forme d'objets Contact.
     *
     * @return Contact[]
     */
    public function findAll(): array
    {
        try {
            $stmt = $this->pdo->query('SELECT id, name, email, phone_number FROM contact ORDER BY id ASC');
            $rows = $stmt->fetchAll();

            $contacts = [];
            foreach ($rows as $row) {
                $contacts[] = new Contact(
                    (int) $row['id'],
                    (string) $row['name'],
                    (string) $row['email'],
                    $row['phone_number'] !== null ? (string) $row['phone_number'] : null
                );
            }
            return $contacts;
        } catch (PDOException $e) {
            error_log('Erreur findAll : ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Cherche un contact par id.
     */
    public function findById(int $id): ?Contact
    {
        try {
            $stmt = $this->pdo->prepare(
                'SELECT id, name, email, phone_number FROM contact WHERE id = :id LIMIT 1'
            );
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch();

            if (!$row) {
                return null;
            }

            return new Contact(
                (int) $row['id'],
                (string) $row['name'],
                (string) $row['email'],
                $row['phone_number'] !== null ? (string) $row['phone_number'] : null
            );
        } catch (PDOException $e) {
            error_log('Erreur findById : ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Met à jour un contact. Retourne le contact mis à jour ou null si échec.
     */
    public function update(int $id, string $name, string $email, ?string $phone): ?Contact
    {
        try {
            $stmt = $this->pdo->prepare(
                'UPDATE contact SET name = :name, email = :email, phone_number = :phone WHERE id = :id'
            );
            $ok = $stmt->execute([
                ':id'    => $id,
                ':name'  => $name,
                ':email' => $email,
                ':phone' => $phone
            ]);

            if (!$ok || $stmt->rowCount() === 0) {
                return null;
            }

            return $this->findById($id);
        } catch (PDOException $e) {
            error_log('Erreur update : ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Supprime un contact. Retourne true si suppression effective.
     */
    public function delete(int $id): bool
    {
        try {
            $stmt = $this->pdo->prepare('DELETE FROM contact WHERE id = :id');
            $stmt->execute([':id' => $id]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log('Erreur delete : ' . $e->getMessage());
            return false;
        }
    }
}
