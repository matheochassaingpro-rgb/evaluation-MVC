<?php
/**
 * @file src/Model/Agency.php
 * Classe représentant une Agence (Ville).
 */
class Agency {
    protected $id;
    protected $name;

    public function __construct(int $id, string $name) {
        $this->id = $id;
        $this->name = $name;
    }
    
    // Getters nécessaires pour la réutilisation du code.
    public function getId(): int { return $this->id; }
    public function getName(): string { return $this->name; }
}