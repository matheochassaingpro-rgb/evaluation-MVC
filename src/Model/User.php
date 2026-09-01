<?php
/**
 * @file src/Model/User.php
 * Classe représentant un utilisateur dans le système de covoiturage.
 */
class User {
    protected $id;
    protected $nom;
    protected $prenom;
    protected $telephone;
    protected $email;

    public function __construct(int $id, string $nom, string $prenom, string $telephone, string $email) {
        $this->id = $id;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->telephone = $telephone;
        $this->email = $email;
    }

    // Getters
    public function getId(): int { return $this->id; }
    public function getNom(): string { return $this->nom; }
    public function getPrenom(): string { return $this->prenom; }
    public function getTelephone(): string { return $this->telephone; }
    public function getEmail(): string { return $this->email; }

    // Méthode statique pour créer un utilisateur à partir d'un tableau associatif (pour la flexibilité)
    public static function fromArray(array $data): self {
        return new self(
            (int)$data['id'], 
            $data['nom'], 
            $data['prenom'], 
            $data['telephone'], 
            $data['email']
        );
    }
}