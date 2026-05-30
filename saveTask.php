<?php
include("includes/connectDB.inc.php");      //Connexion à la BDD

//On vérifie le code de session
if (array_key_exists('code', $_POST) and array_key_exists($_POST['code'], $_SESSION['code'])) {
    $id_tache = intval($_SESSION['code'][$_POST['code']]);       //On récupère le code de session s'il est valide
} else {
    header("Location: todolist.php");       //Si le code de session n'est pas valide, renvoie vers l'accueil
    exit();
}

//On récupère les données du formulaire
$titre = $_POST['titre'];
$description = $_POST['description'];
$date_limite = $_POST['date_limite'];
$id_categorie = $_POST['id_categorie'];
$id_priorite = $_POST['id_priorite'];
$id_statut = $_POST['id_statut'];

if ($id_tache == 0) {       //Si la tâche n'existe pas on insert les données
    $query = "INSERT INTO tache (titre, description, date_limite, date_creation, date_modification, id_categorie, id_priorite, id_statut) VALUES (?, ?, ?, CURDATE(), CURDATE(), ?, ?, ?)";
    $stmt = mysqli_prepare($link, $query);

    mysqli_stmt_bind_param($stmt, 'sssiii', $titre, $description, $date_limite, $id_categorie, $id_priorite, $id_statut);
    mysqli_stmt_execute($stmt);
} else {        //Si la tâche existe on update les données
    $query = "UPDATE tache SET titre = ?, description = ?, date_limite = ?, date_modification = CURDATE(), id_categorie = ?, id_priorite = ?, id_statut = ? WHERE id_tache = ?";
    $stmt = mysqli_prepare($link, $query);

    mysqli_stmt_bind_param($stmt, 'sssiiii', $titre, $description, $date_limite, $id_categorie, $id_priorite, $id_statut, $id_tache);
    mysqli_stmt_execute($stmt);
}

//Gestion des étiquettes
// Récupérer l'id de la tâche (si création, on prend le dernier id inséré)
if ($id_tache == 0) {
    $id_tache = mysqli_insert_id($link);
}

// Supprimer les anciennes étiquettes
$query = "DELETE FROM est_marquee_par WHERE id_tache = ?";
$stmt = mysqli_prepare($link, $query);

mysqli_stmt_bind_param($stmt, 'i', $id_tache);
mysqli_stmt_execute($stmt);

// Insérer les étiquettes cochées
if (isset($_POST['etiquettes'])) {
    $query = "INSERT INTO est_marquee_par (id_tache, id_etiquette) VALUES (?, ?)";
    $stmt = mysqli_prepare($link, $query);
    foreach ($_POST['etiquettes'] as $id_etiquette) {
        $id_etiquette = intval($id_etiquette);
        mysqli_stmt_bind_param($stmt, 'ii', $id_tache, $id_etiquette);
        mysqli_stmt_execute($stmt);
    }
}

header("Location: todolist.php"); //Redirection vers l'accueil
exit();
