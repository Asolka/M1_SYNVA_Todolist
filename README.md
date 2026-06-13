# Ma Tout Doux List

Application web de gestion de tâches avec un thème visuel inspiré d'Animal Crossing.

## Fonctionnalités

- **Liste des tâches** : affichage en tableau avec tri par colonnes (titre, catégorie, priorité, statut, dates)
- **Création / Édition / Suppression** de tâches via un formulaire avec validation
- **Catégories, priorités, statuts** : chaque tâche est classée selon ces critères avec des badges colorés
- **Étiquettes** : système d'étiquettes personnalisées avec couleurs (relation N:N)
- **Dashboard** : tableau de bord avec compteurs et graphiques Chart.js (camembert par statut, barres par catégorie et priorité)
- **Sécurité** : codes de session aléatoires pour protéger les IDs, requêtes SQL préparées (mysqli)
- **Responsive** : adapté mobile avec tableau scrollable

## Stack technique

- **Backend** : PHP 8 / MySQL (mysqli)
- **Frontend** : HTML5, CSS3, Bootstrap 5, Chart.js
- **Serveur** : WAMP (Windows, Apache, MySQL, PHP)
- **Typographie** : Google Fonts (Quicksand)

## Structure du projet

```
todolist/
├── assets/
│   ├── css/
│   │   └── style.css           # Thème pastel Animal Crossing
│   ├── images/                 # Icônes et images du thème
│   └── js/
│       └── script.js
├── includes/
│   ├── connectDB.inc.php       # Connexion BDD + fonction de sécurité
│   ├── header.inc.php          # En-tête HTML + navbar
│   ├── footer.inc.php          # Pied de page + scripts CDN
│   └── auth.php                # Identifiants BDD (non versionné)
├── sql/
│   └── todolist.sql            # Script de création de la BDD
├── todolist.php                # Page d'accueil (liste des tâches)
├── taskForm.php                # Formulaire de création / édition
├── saveTask.php                # Traitement du formulaire
├── deleteTask.php              # Suppression d'une tâche
└── dashboard.php               # Tableau de bord avec graphiques
```

## Installation

1. Installer WAMP (ou équivalent)
2. Cloner le projet dans le dossier `www/`
3. Importer `sql/todolist.sql` dans phpMyAdmin
4. Créer le fichier `includes/auth.php` avec les identifiants de connexion :
   ```php
   <?php
   $host = "localhost";
   $user = "root";
   $passwd = "";
   $db = "todolist";
   ?>
   ```
5. Accéder à `http://localhost/todolist/todolist.php`

## Base de données

6 tables : `tache`, `categorie`, `priorite`, `statut`, `etiquette`, `est_marquee_par` (relation N:N tâche-étiquette).

## Projet

Projet M1 SYNVA 2025-2026
