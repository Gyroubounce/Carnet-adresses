<?php
require_once 'ContactManager.php';

class Command {
    private ContactManager $manager;

    public function __construct() {
        $this->manager = new ContactManager();
    }

    public function execute(string $command): void {
        switch ($command) {
            case "list":
                $this->listContacts();
                break;

            case "create":
                $this->createContact();
                break;

            case "delete":
                $this->deleteContact();
                break;

            case "modify":
                $this->modifyContact();
                break;

            case "help":
                $this->showHelp();
                break;

            case "quit":
                echo "Programme terminé.\n";
                exit;

            default:
                echo "Commande inconnue. Tapez 'help' pour voir les commandes disponibles.\n";
        }
    }

    private function listContacts(): void {
        $contacts = $this->manager->findAll();
        if (empty($contacts)) {
            echo "Aucun contact trouvé.\n";
        } else {
            foreach ($contacts as $contact) {
                echo $contact . "\n";
            }
        }
    }

    private function createContact(): void {
        $name  = readline("Nom : ");
        $email = readline("Email : ");
        $phone = readline("Téléphone : ");

        try {
            $stmt = $this->manager->getPDO()->prepare(
                "INSERT INTO contact (name, email, phone_number) VALUES (:name, :email, :phone)"
            );
            $stmt->execute([
                'name'  => $name,
                'email' => $email,
                'phone' => $phone
            ]);
            echo "Contact créé avec succès.\n";
        } catch (PDOException $e) {
            error_log("Erreur lors de la création : " . $e->getMessage());
            echo "Impossible de créer le contact.\n";
        }
    }

    private function deleteContact(): void {
        $id = (int)readline("ID du contact à supprimer : ");
        try {
            $stmt = $this->manager->getPDO()->prepare("DELETE FROM contact WHERE id = :id");
            $stmt->execute(['id' => $id]);
            echo "Contact supprimé (si existant).\n";
        } catch (PDOException $e) {
            error_log("Erreur lors de la suppression : " . $e->getMessage());
            echo "Impossible de supprimer le contact.\n";
        }
    }

    private function modifyContact(): void {
        $id    = (int)readline("ID du contact à modifier : ");
        $name  = readline("Nouveau nom : ");
        $email = readline("Nouvel email : ");
        $phone = readline("Nouveau téléphone : ");

        try {
            $stmt = $this->manager->getPDO()->prepare(
                "UPDATE contact SET name = :name, email = :email, phone_number = :phone WHERE id = :id"
            );
            $stmt->execute([
                'id'    => $id,
                'name'  => $name,
                'email' => $email,
                'phone' => $phone
            ]);
            echo "Contact modifié (si existant).\n";
        } catch (PDOException $e) {
            error_log("Erreur lors de la modification : " . $e->getMessage());
            echo "Impossible de modifier le contact.\n";
        }
    }

    private function showHelp(): void {
        echo "Commandes disponibles :\n";
        echo " - list   : afficher tous les contacts\n";
        echo " - create : ajouter un nouveau contact\n";
        echo " - delete : supprimer un contact\n";
        echo " - modify : modifier un contact\n";
        echo " - help   : afficher cette aide\n";
        echo " - quit   : quitter le programme\n";
    }
}
