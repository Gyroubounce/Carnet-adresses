<?php
require_once 'DBConnect.php';
require_once 'Contact.php';

class ContactManager {
    private PDO $pdo;

    public function getPDO(): ?PDO {
    return $this->pdo;
    }

    public function __construct() {
        $db = new DBConnect();
        $this->pdo = $db->getPDO();
    }

    public function findAll(): array {
        $stmt = $this->pdo->query("SELECT * FROM contact");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $contacts = [];
        foreach ($rows as $row) {
            $contacts[] = new Contact(
                $row['id'],
                $row['name'],
                $row['email'],
                $row['phone_number']
            );
        }

        return $contacts;
    }

    public function findById(int $id): ?Contact {
        $stmt = $this->pdo->prepare("SELECT * FROM contact WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return new Contact($row['id'], $row['name'], $row['email'], $row['phone_number']);
        }
        return null;
    }
}
