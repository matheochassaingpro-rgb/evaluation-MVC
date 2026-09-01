# Modèle de Données Logiques et Conceptuelles (MCD/MLD)

## 1. Diagramme Conceptuel des Données (MCD - Description Textuelle)
Le MCD représente les entités principales de notre système et leurs relations :

*   **EMPLOYÉ** (ProfilsUtilisateurs) : Contient l'identité complète de l'employé (Nom, Prénom, Téléphone, Email)[cite: 2].
*   **COMPTE_CONNEXION** (UserAccounts) : Lie un EMPLOYÉ à ses droits d'accès (`ROLE_USER` / `ROLE_ADMIN`) et contient le mot de passe sécurisé[cite: 2].
*   **AGENCE** : Représente une localisation physique (Ville) de l'entreprise[cite: 2].
*   **TRAJET** : Décrit un voyage planifié entre deux agences, gérant la capacité et les places disponibles[cite: 2].

### Relations :
1. Un **EMPLOYÉ** *possède un et un seul compte* dans **COMPTE_CONNEXION** (1,1 $\rightarrow$ 1,1)[cite: 2].
2. Un **TRAJET** *part de* une **AGENCE** de départ et *arrive à* une **AGENCE** d'arrivée (1,1 $\rightarrow$ 1,1)[cite: 2].
3. Un **TRAJET** *est créé par* un **EMPLOYÉ** (auteur du trajet) (1,1 $\rightarrow$ 1,N)[cite: 2].

---

## 2. Modèle Logique de Données (MLD - Format SQL)
Le MLD structure les données dans les tables relationnelles effectives du projet :

### Table : Agences
*   `id` (PK, INT) : Identifiant unique de l'agence[cite: 2].
*   `nom` (VARCHAR(100)) : Nom de la ville (UNIQUE)[cite: 2].

### Table : ProfilsUtilisateurs
*   `id` (PK, INT) : Identifiant unique de l'employé[cite: 2].
*   `nom` (VARCHAR(100)) : Nom de famille[cite: 2].
*   `prenom` (VARCHAR(100)) : Prénom[cite: 2].
*   `telephone` (VARCHAR(20)) : Numéro de téléphone unique[cite: 2].
*   `email` (VARCHAR(150)) : Adresse email professionnelle unique[cite: 2].

### Table : UserAccounts
*   `user_id` (PK, INT) : Clé étrangère liée à `ProfilsUtilisateurs.id` (avec suppression en cascade)[cite: 2].
*   `password_hash` (VARCHAR(255)) : Mot de passe haché (Password Hash)[cite: 2].
*   `role` (ENUM('ROLE_USER', 'ROLE_ADMIN')) : Rôle attribué dans l'application[cite: 2].

### Table : Trajets
*   `id` (PK, INT) : Identifiant unique du trajet[cite: 2].
*   `agence_depart_id` (FK, INT) : Lien vers `Agences.id` (Ville de départ)[cite: 2].
*   `agence_arrivee_id` (FK, INT) : Lien vers `Agences.id` (Ville d'arrivée)[cite: 2].
*   `date_depart` (DATETIME) : Date et heure de départ prévue[cite: 2].
*   `date_arrivee` (DATETIME) : Date et heure d'arrivée prévue[cite: 2].
*   `total_places` (SMALLINT) : Capacité maximale de places pour le covoiturage[cite: 2].
*   `places_disponibles` (SMALLINT) : Nombre de places encore libres[cite: 2].
*   `personne_contact` (VARCHAR(255)) : Informations de contact sur place[cite: 2].
*   `auteur_id` (FK, INT) : Lien vers `ProfilsUtilisateurs.id` (employé créateur du trajet)[cite: 2].

**Contraintes d'Intégrité :** Les clés étrangères (`FOREIGN KEY`) garantissent la cohérence relationnelle entre les agences, les utilisateurs et les trajets planifiés.