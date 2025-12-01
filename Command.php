<?php
declare(strict_types=1);

/**
 * Command :
 * - Centralise la logique des commandes CLI.
 * - Valide les entrées (messages d'erreur si inconnues ou mal formatées).
 * - Délègue au ContactManager pour le CRUD.
 */

require_once __DIR__ . '/ContactManager.php';

class Command
{
    private ContactManager $manager;

    public function __construct()
    {
        $this->manager = new ContactManager();
    }

    /**
     * Exécute une commande saisie.
     */
    public function execute(string $input): void
    {
        $command = trim($input);

        switch ($command) {
            case 'list':
                $this->listContacts();
                break;

            case 'create':
                $this->createContactInteractive();
                break;

            case 'delete':
                $this->deleteContactInteractive();
                break;

            case 'modify':
                $this->modifyContactInteractive();
                break;

            case 'help':
                $this->showHelp();
                break;

            case 'quit':
                echo "Programme terminé.\n";
                exit(0);

            default:
                echo "Commande inconnue ou mal formatée. Tapez 'help' pour l'aide.\n";
        }
    }

    private function listContacts(): void
    {
        $contacts = $this->manager->findAll();
        if (empty($contacts)) {
            echo "Aucun contact trouvé.\n";
            return;
        }

        echo "ID | Nom | Email | Téléphone\n";
        echo "-------------------------------------------\n";
        foreach ($contacts as $contact) {
            echo $contact . "\n"; // utilise __toString()
        }
    }

    private function createContactInteractive(): void
    {
        $name  = trim((string) readline('Nom : '));
        $email = trim((string) readline('Email : '));
        $phone = trim((string) readline('Téléphone (optionnel) : '));
        $phone = $phone !== '' ? $phone : null;

        if ($name === '' || $email === '') {
            echo "Format incorrect : nom et email sont requis.\n";
            return;
        }

        $contact = $this->manager->create($name, $email, $phone);
        if ($contact instanceof Contact) {
            echo "Contact créé : {$contact}\n";
        } else {
            echo "Impossible de créer le contact (voir logs).\n";
        }
    }

    private function deleteContactInteractive(): void
    {
        $idStr = trim((string) readline('ID du contact à supprimer : '));
        if ($idStr === '' || !ctype_digit($idStr)) {
            echo "Format incorrect : l'ID doit être un entier positif.\n";
            return;
        }
        $id = (int) $idStr;

        $ok = $this->manager->delete($id);
        echo $ok ? "Contact supprimé.\n" : "Aucun contact supprimé (id introuvable).\n";
    }

    private function modifyContactInteractive(): void
    {
        $idStr = trim((string) readline('ID du contact à modifier : '));
        if ($idStr === '' || !ctype_digit($idStr)) {
            echo "Format incorrect : l'ID doit être un entier positif.\n";
            return;
        }
        $id = (int) $idStr;

        $existing = $this->manager->findById($id);
        if (!$existing) {
            echo "Contact introuvable pour l'id {$id}.\n";
            return;
        }

        echo "Valeurs actuelles : {$existing}\n";
        $name  = trim((string) readline('Nouveau nom (laisser vide pour conserver) : '));
        $email = trim((string) readline('Nouvel email (laisser vide pour conserver) : '));
        $phone = trim((string) readline('Nouveau téléphone (laisser vide pour conserver) : '));

        $newName  = $name !== '' ? $name : $existing->getName();
        $newEmail = $email !== '' ? $email : $existing->getEmail();
        $newPhone = $phone !== '' ? $phone : $existing->getPhoneNumber();

        $updated = $this->manager->update($id, $newName, $newEmail, $newPhone);
        if ($updated instanceof Contact) {
            echo "Contact modifié : {$updated}\n";
        } else {
            echo "Impossible de modifier le contact (voir logs).\n";
        }
    }

    private function showHelp(): void
    {
        echo "Commandes disponibles :\n";
        echo " - list   : afficher tous les contacts\n";
        echo " - create : ajouter un nouveau contact (prompts interactifs)\n";
        echo " - delete : supprimer un contact (id demandé)\n";
        echo " - modify : modifier un contact (id + prompts)\n";
        echo " - help   : afficher cette aide\n";
        echo " - quit   : quitter le programme\n";
    }
}
