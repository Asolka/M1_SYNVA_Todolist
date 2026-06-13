<?php
include("includes/connectDB.inc.php");      //Connexion à la BDD

//On vérifie le code de session
if (array_key_exists('code', $_GET) and array_key_exists($_GET['code'], $_SESSION['code'])) {
    $id_tache = intval($_SESSION['code'][$_GET['code']]);       //On récupère le code de session s'il est valide
} else {
    header("Location: todolist.php");       //Si le code de session n'est pas valide, renvoie vers l'accueil
    exit();
}

    //Suppression des étiquettes
$query = "DELETE FROM est_marquee_par WHERE id_tache = ?";
$stmt = mysqli_prepare($link, $query);
mysqli_stmt_bind_param($stmt, 'i', $id_tache);
mysqli_stmt_execute($stmt);

    //Suppression de la tâche
$query = "DELETE FROM tache WHERE id_tache = ?";
$stmt = mysqli_prepare($link, $query);
mysqli_stmt_bind_param($stmt, 'i', $id_tache);
mysqli_stmt_execute($stmt);


header("Location: todolist.php");       //Redirection vers l'accueil
exit();
?>