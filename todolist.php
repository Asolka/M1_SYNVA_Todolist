<?php
include("includes/connectDB.inc.php");      //Connexion à la BDD
include("includes/header.inc.php");     //Insertion du HTML header

$_SESSION['code']=array();      //On initialise le tableau des codes de session pour anonymiser les données

$code=random_pw(10);        //On créer un code d'une longueur de 10 caractères
$_SESSION['code'][$code]=0;     //On établit le code sécurisé de l'id 0 qui sert à créer une nouvelle tâche 

    //Requête SQL pour afficher toutes les tâches ainsi que leurs attributs
$query="SELECT t.id_tache, t.titre, t.description, DATE_FORMAT(t.date_limite, '%d/%m/%Y') AS date_limite, DATE_FORMAT(t.date_creation, '%d/%m/%Y') AS date_creation,
        c.nom_categorie, p.libelle_priorite, s.libelle_statut
        FROM tache t
        JOIN categorie c USING (id_categorie)
        JOIN priorite p USING (id_priorite)
        JOIN statut s USING (id_statut)
        ORDER BY t.date_limite ASC";

    //Test de la requête SQL renvoie une erreur si ne fonctionne pas
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
	$result=mysqli_query($link, $query);
} catch (Exception $e) { 
    echo "MySQLi Error Code: " . $e->getCode() . "<br />";
    echo "Exception Msg: " . $e->getMessage();
exit();
}	
?>

<h1>Liste des tâches</h1>
    <!-- Bouton de nouvelle tâche avec le code 0 sécurisé -->
<a href="taskForm.php?code=<?php echo $code; ?>" class="btn-primary mb-3">Nouvelle tâche</a>

    <!-- Afficher le tableau -->
<table class="table table-striped table-hover">
    <thead>
        <tr>
            <th>Titre</th>
            <th>Description</th>
            <th>Catégorie</th>
            <th>Priorité</th>
            <th>Statut</th>
            <th>Création</th>
            <th>Limite</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
<?php 
while ($var = mysqli_fetch_assoc($result)) {   //Boucle, pour chaque tâche, rajoute une ligne dans le tableau, lui donne un code de session et affiche ses attributs, puis les boutons d'actions
    
$code=random_pw(10);
$_SESSION['code'][$code]=$var['id_tache'];

echo "<tr>";        //Début de ligne
echo "<td>" . $var['titre'] . "</td>";      //Affiche le titre de la tâche
echo "<td>" . $var['description'] . "</td>";        //Affiche la description de la tâche
echo "<td>" . $var['nom_categorie'] . "</td>";      //Affiche la catégorie de la tâche
echo "<td>" . $var['libelle_priorite'] . "</td>";       //Affiche la priorité de la tâche
echo "<td>" . $var['libelle_statut'] . "</td>";     //Affiche le statut de la tâche
echo "<td>" . $var['date_creation'] ?? '-' . "</td>";       //Affiche la date de création de la tâche
echo "<td>" . $var['date_limite'] ?? '-' . "</td>";     //Affiche la date limite de la tâche
echo "<td>";
echo "<a href=\"taskForm.php?code=$code\">Éditer</a> | ";       //Bouton d'édition de la tâche
echo "<a href=\"deleteTask.php?code=$code\">Supprimer</a>";     //Bouton de suppression de la tâche
echo "</td>";
echo "</tr>";       //Fin de ligne
}
?>
    </tbody>
</table>

<?php
include("includes/footer.inc.php");     //Insertion du HTML footer
?>