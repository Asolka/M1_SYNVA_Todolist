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

if ($id_tache == 0) {
    $query = "INSERT INTO tache (titre, description, date_limite, date_creation, date_modification, id_categorie, id_priorite, id_statut) VALUES (?, ?, ?, CURDATE(), CURDATE(), ?, ?, ?)";
    $stmt = mysqli_prepare($link, $query);

    mysqli_stmt_bind_param($stmt, 'sssiii', $titre, $description, $date_limite, $id_categorie, $id_priorite, $id_statut);
    mysqli_stmt_execute($stmt);
} else {
    $query = "UPDATE tache SET titre = ?, description = ?, date_limite = ?, date_modification = CURDATE(), id_categorie = ?, id_priorite = ?, id_statut = ? WHERE id_tache = ?";
    $stmt = mysqli_prepare($link, $query);

    mysqli_stmt_bind_param($stmt, 'sssiiii', $titre, $description, $date_limite, $id_categorie, $id_priorite, $id_statut, $id_tache);
    mysqli_stmt_execute($stmt);
}

header("Location: todolist.php"); //Redirection vers l'accueil
exit();
?>