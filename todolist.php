<?php
include("includes/connectDB.inc.php");      //Connexion à la BDD
include("includes/header.inc.php");     //Insertion du HTML header

$_SESSION['code'] = array();      //On initialise le tableau des codes de session pour anonymiser les données

$code = random_pw(10);        //On créer un code d'une longueur de 10 caractères
$_SESSION['code'][$code] = 0;     //On établit le code sécurisé de l'id 0 qui sert à créer une nouvelle tâche


// Tableau des colonnes pour le tri
$colonnesAutorisees = [
    'titre' => 't.titre',
    'categorie' => 'c.nom_categorie',
    'priorite' => 'p.niveau',
    'statut' => 's.libelle_statut',
    'date_creation' => 't.date_creation',
    'date_modification' => 't.date_modification',
    'date_limite' => 't.date_limite'
];
$tri = $_GET['tri'] ?? 'date_limite';       //Colonne de tri (par défaut : date limite)
$ordre = ($_GET['ordre'] ?? 'ASC') == 'DESC' ? 'DESC' : 'ASC';     //Ordre de tri (ASC ou DESC, sécurisé)
$ordreInverse = ($ordre == 'ASC') ? 'DESC' : 'ASC';        //Ordre inverse (pour le lien de tri)
$colonneTri = $colonnesAutorisees[$tri] ?? 't.date_limite';     //Colonne SQL correspondante (sécurisé par le tableau)


//Requête SQL pour afficher toutes les tâches ainsi que leurs attributs
$query = "SELECT t.id_tache, t.titre, t.description, DATE_FORMAT(t.date_limite, '%d/%m/%Y') AS date_limite, DATE_FORMAT(t.date_creation, '%d/%m/%Y') AS date_creation, DATE_FORMAT(t.date_modification, '%d/%m/%Y') AS date_modification,
        c.nom_categorie, p.libelle_priorite, s.libelle_statut
        FROM tache t
        JOIN categorie c USING (id_categorie)
        JOIN priorite p USING (id_priorite)
        JOIN statut s USING (id_statut)
        ORDER BY $colonneTri IS NULL, $colonneTri $ordre";

//Test de la requête SQL renvoie une erreur si ne fonctionne pas
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $result = mysqli_query($link, $query);
} catch (Exception $e) {
    echo "MySQLi Error Code: " . $e->getCode() . "<br />";
    echo "Exception Msg: " . $e->getMessage();
    exit();
}
?>

<h1>Liste des tâches</h1>
<!-- Bouton de nouvelle tâche avec le code 0 sécurisé -->
<a href="taskForm.php?code=<?php echo $code; ?>" class="btn btn-primary mb-3"><img src="./assets/images/nouvelle_tache.png" alt="" class="icon-btn">Nouvelle tâche</a>

<!-- div pour le scroll horizontal sur mobile -->
<div class="table-responsive-wrapper">
<table class="table table-striped table-hover">
    
    <thead>
        <tr>
            <!-- Ajout de la fonction de tri, la flèche ▲ ou ▼ indique le tri et son sens -->
            <!-- Ajout des icônes associées aux entêtes -->
            <th><a href="?tri=titre&ordre=<?php echo ($tri=='titre') ? $ordreInverse : 'ASC'; ?>"><img src="./assets/images/titre.png" alt="" class="icon-th">Titre <?php if($tri=='titre') echo ($ordre=='ASC') ? '▲' : '▼'; ?></a></th>
            <th><img src="./assets/images/description.png" alt="" class="icon-th">Description</th>
            <th><a href="?tri=categorie&ordre=<?php echo ($tri=='categorie') ? $ordreInverse : 'ASC'; ?>"><img src="./assets/images/categorie.png" alt="" class="icon-th">Catégorie <?php if($tri=='categorie') echo ($ordre=='ASC') ? '▲' : '▼'; ?></a></th>
            <th><a href="?tri=priorite&ordre=<?php echo ($tri=='priorite') ? $ordreInverse : 'ASC'; ?>"><img src="./assets/images/priorite.png" alt="" class="icon-th">Priorité <?php if($tri=='priorite') echo ($ordre=='ASC') ? '▲' : '▼'; ?></a></th>
            <th><a href="?tri=statut&ordre=<?php echo ($tri=='statut') ? $ordreInverse : 'ASC'; ?>"><img src="./assets/images/statut.png" alt="" class="icon-th">Statut <?php if($tri=='statut') echo ($ordre=='ASC') ? '▲' : '▼'; ?></a></th>
            <th><img src="./assets/images/etiquette.png" alt="" class="icon-th">Étiquettes</th>
            <th><a href="?tri=date_creation&ordre=<?php echo ($tri=='date_creation') ? $ordreInverse : 'ASC'; ?>"><img src="./assets/images/creation.png" alt="" class="icon-th">Création <?php if($tri=='date_creation') echo ($ordre=='ASC') ? '▲' : '▼'; ?></a></th>
            <th><a href="?tri=date_modification&ordre=<?php echo ($tri=='date_modification') ? $ordreInverse : 'ASC'; ?>"><img src="./assets/images/modification.png" alt="" class="icon-th">Modification <?php if($tri=='date_modification') echo ($ordre=='ASC') ? '▲' : '▼'; ?></a></th>
            <th><a href="?tri=date_limite&ordre=<?php echo ($tri=='date_limite') ? $ordreInverse : 'ASC'; ?>"><img src="./assets/images/limite.png" alt="" class="icon-th">Limite <?php if($tri=='date_limite') echo ($ordre=='ASC') ? '▲' : '▼'; ?></a></th>
            <th><img src="./assets/images/actions.png" alt="" class="icon-th">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        while ($var = mysqli_fetch_assoc($result)) {   //Boucle, pour chaque tâche, rajoute une ligne dans le tableau, lui donne un code de session et affiche ses attributs, puis les boutons d'actions

            $code = random_pw(10);       //On créer un code d'une longueur de 10 caractères
            $_SESSION['code'][$code] = $var['id_tache'];        //On attribut les codes de session à chaque tâche

            echo "<tr>";        //Début de ligne
            echo "<td>" . $var['titre'] . "</td>";      //Affiche le titre de la tâche
            echo "<td>" . $var['description'] . "</td>";        //Affiche la description de la tâche
            echo "<td>" . $var['nom_categorie'] . "</td>";      //Affiche la catégorie de la tâche

            //Badge de priorité coloré selon le niveau
            $classePriorite = '';       //On établit la variable
            if ($var['libelle_priorite'] == 'Peu important') $classePriorite = 'badge-priorite-1';      //Si "peu important" on attribue le badge correspondant
            elseif ($var['libelle_priorite'] == 'Important') $classePriorite = 'badge-priorite-2';      //Si "important" on attribue le badge correspondant
            elseif ($var['libelle_priorite'] == 'Très important') $classePriorite = 'badge-priorite-3';     //Si "très important" on attribue le badge correspondant
            echo "<td><span class='badge $classePriorite'>" . $var['libelle_priorite'] . "</span></td>";        //On affiche le badge

            //Badge de statut coloré
            $classeStatut = '';     //On établit la variable
            if ($var['libelle_statut'] == 'En attente') $classeStatut = 'badge-statut-attente';     //Si "en attente" on attribue le badge correspondant
            elseif ($var['libelle_statut'] == 'En cours') $classeStatut = 'badge-statut-cours';     //Si "en cours" on attribue le badge correspondant
            elseif ($var['libelle_statut'] == 'Terminé') $classeStatut = 'badge-statut-termine';        //Si "terminé" on attribue le badge correspondant
            echo "<td><span class='badge $classeStatut'>" . $var['libelle_statut'] . "</span></td>";        //On affiche le badge

            // Récupérer les étiquettes de cette tâche
            $queryEtiq = "SELECT e.nom_etiquette, e.couleur FROM est_marquee_par emp JOIN etiquette e USING (id_etiquette) WHERE emp.id_tache = " . intval($var['id_tache']);       //Requête SQL pour récupérer les étiquettes
            $resultEtiq = mysqli_query($link, $queryEtiq);

            //On affiche chaque étiquette
            echo "<td>";
            while ($etiq = mysqli_fetch_assoc($resultEtiq)) {
                echo "<span class='badge me-1' style='background-color:{$etiq['couleur']}'>{$etiq['nom_etiquette']}</span>";        //On affiche chaque étiquette
            }
            echo "</td>";

            echo "<td>" . $var['date_creation'] ?? '-' . "</td>";       //Affiche la date de création de la tâche
            echo "<td>" . $var['date_modification'] ?? '-' . "</td>";       //Affiche la date de modification de la tâche
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
</div>

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