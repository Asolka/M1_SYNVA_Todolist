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
        ORDER BY t.date_limite IS NULL, t.date_limite ASC";

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
<a href="taskForm.php?code=<?php echo $code; ?>" class="btn btn-primary mb-3">Nouvelle tâche</a>

    <!-- Afficher le tableau -->
<table class="table table-striped table-hover">
    <thead>
        <tr>
            <th>Titre</th>
            <th>Description</th>
            <th>Catégorie</th>
            <th>Priorité</th>
            <th>Statut</th>
            <th>Étiquettes</th>
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
//Badge de priorité coloré selon le niveau
$classePriorite = '';
if ($var['libelle_priorite'] == 'Peu important') $classePriorite = 'badge-priorite-1';
elseif ($var['libelle_priorite'] == 'Important') $classePriorite = 'badge-priorite-2';
elseif ($var['libelle_priorite'] == 'Très important') $classePriorite = 'badge-priorite-3';
echo "<td><span class='badge $classePriorite'>" . $var['libelle_priorite'] . "</span></td>";

//Badge de statut coloré
$classeStatut = '';
if ($var['libelle_statut'] == 'En attente') $classeStatut = 'badge-statut-attente';
elseif ($var['libelle_statut'] == 'En cours') $classeStatut = 'badge-statut-cours';
elseif ($var['libelle_statut'] == 'Terminé') $classeStatut = 'badge-statut-termine';
echo "<td><span class='badge $classeStatut'>" . $var['libelle_statut'] . "</span></td>";

// Récupérer les étiquettes de cette tâche
$queryEtiq = "SELECT e.nom_etiquette, e.couleur 
              FROM est_marquee_par emp 
              JOIN etiquette e USING (id_etiquette) 
              WHERE emp.id_tache = " . intval($var['id_tache']);
$resultEtiq = mysqli_query($link, $queryEtiq);

echo "<td>";
while ($etiq = mysqli_fetch_assoc($resultEtiq)) {
    echo "<span class='badge me-1' style='background-color:{$etiq['couleur']}'>{$etiq['nom_etiquette']}</span>";
}
echo "</td>";

echo "<td>" . $var['date_creation'] ?? '-' . "</td>";       //Affiche la date de création de la tâche
echo "<td>" . $var['date_limite'] ?? '-' . "</td>";     //Affiche la date limite de la tâche
echo "<td>";
echo "<a href=\"taskForm.php?code=$code\" class='action-edit'>Éditer</a> | ";       //Bouton d'édition de la tâche
echo "<a href='#' data-bs-toggle='modal' data-bs-target='#modalSupprimer' data-titre='" . htmlspecialchars($var['titre']) . "' data-url='deleteTask.php?code=$code' class='action-delete'>Supprimer</a>";     //Bouton de suppression de la tâche associé au modal Bootstrap
echo "</td>";
echo "</tr>";       //Fin de ligne
}
?>
    </tbody>
</table>

<!-- Modal de confirmation de suppression  issu de la documentation Bootstrap-->
<div class="modal fade" id="modalSupprimer" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Voulez-vous vraiment supprimer la tâche "<strong id="modalTitreTache"></strong>" ?</p>
            </div>
            <div class="modal-footer">
                <a href="#" id="modalBtnOui" class="btn btn-danger">Oui</a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Non</button>
            </div>
        </div>
    </div>
</div>

<script>
var modal = document.getElementById('modalSupprimer');
modal.addEventListener('show.bs.modal', function(event) {
    var bouton = event.relatedTarget;
    document.getElementById('modalTitreTache').textContent = bouton.getAttribute('data-titre');
    document.getElementById('modalBtnOui').setAttribute('href', bouton.getAttribute('data-url'));
});
</script>
<!-- Modal de confirmation de suppression  issu de la documentation Bootstrap -->

<?php
include("includes/footer.inc.php");     //Insertion du HTML footer
?>