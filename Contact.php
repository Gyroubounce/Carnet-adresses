<?php
declare(strict_types=1);

/**
 * Entité Contact :
 * - Ne dépend pas de la base de données.
 * - Représente un seul contact.
 * - Getters/Setters pour validation.
 * - __toString pour l'affichage dans le shell.
 */

class Contact
{
    private ?int $id;
    private string $name;
    private string $email;
    private ?string $phoneNumber;

    public function __construct(?int $id, string $name, string $email, ?string $phoneNumber)
    {
        $this->id = $id;
        $this->setName($name);
        $this->setEmail($email);
        $this->setPhoneNumber($phoneNumber);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        if (trim($name) === '') {
            throw new InvalidArgumentException('Le nom ne peut pas être vide.');
        }
        $this->name = $name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Email invalide : ' . $email);
        }
        $this->email = $email;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): void
    {
        // Autorise null ou chaîne (format libre), à valider côté manager si besoin
        $this->phoneNumber = $phoneNumber !== null ? trim($phoneNumber) : null;
    }

    public function __toString(): string
    {
        $id   = $this->id ?? '-';
        $tel  = $this->phoneNumber ?? '-';
        return "Contact #{$id} | {$this->name} | {$this->email} | {$tel}";
    }
}
