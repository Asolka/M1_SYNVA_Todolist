<?php
include("includes/connectDB.inc.php");      //Connexion à la BDD
include("includes/header.inc.php");     //Insertion du HTML header


//On compte le nombre de tâches selon leur état
$total = mysqli_fetch_assoc(mysqli_query($link, "SELECT COUNT(*) AS nb FROM tache"))['nb'];      //Nombre total de tâches
$enCours = mysqli_fetch_assoc(mysqli_query($link, "SELECT COUNT(*) AS nb FROM tache WHERE id_statut = 2"))['nb'];       //Nombre de tâches en cours
$terminees = mysqli_fetch_assoc(mysqli_query($link, "SELECT COUNT(*) AS nb FROM tache WHERE id_statut = 3"))['nb'];     //Nombre de tâches terminées
$enRetard = mysqli_fetch_assoc(mysqli_query($link, "SELECT COUNT(*) AS nb FROM tache WHERE date_limite < CURDATE() AND id_statut != 3"))['nb'];     //Nombre de tâches en retard (date limite dépassée et pas terminées)

//Données pour le diagramme camembert : nombre de tâches par statut
$parStatut = mysqli_query($link, "SELECT s.libelle_statut, COUNT(*) AS nb FROM tache t JOIN statut s USING(id_statut) GROUP BY s.libelle_statut");      //Requête SQL avec JOIN et GROUP BY pour compter les tâches par statut
$labelsStatut = [];     //Tableau des noms de statuts (pour les légendes du graphique)
$dataStatut = [];       //Tableau des nombres (pour les valeurs du graphique)
while ($row = mysqli_fetch_assoc($parStatut)) {     //On parcourt les résultats
    $labelsStatut[] = $row['libelle_statut'];        //On ajoute le nom du statut
    $dataStatut[] = $row['nb'];     //On ajoute le nombre de tâches
}

//Données pour le diagramme en bâtons : nombre de tâches par catégorie
$parCategorie = mysqli_query($link, "SELECT c.nom_categorie, COUNT(*) AS nb FROM tache t JOIN categorie c USING(id_categorie) GROUP BY c.nom_categorie");       //Requête SQL avec JOIN et GROUP BY pour compter les tâches par catégorie
$labelsCategorie = [];      //Tableau des noms de catégories
$dataCategorie = [];        //Tableau des nombres
while ($row = mysqli_fetch_assoc($parCategorie)) {      //On parcourt les résultats
    $labelsCategorie[] = $row['nom_categorie'];     //On ajoute le nom de la catégorie
    $dataCategorie[] = $row['nb'];      //On ajoute le nombre de tâches
}

//Données pour le diagramme en bâtons : nombre de tâches par priorité
$parPriorite = mysqli_query($link, "SELECT p.libelle_priorite, COUNT(*) AS nb FROM tache t JOIN priorite p USING(id_priorite) GROUP BY p.libelle_priorite ORDER BY p.niveau");      //Requête SQL avec JOIN, GROUP BY et ORDER BY pour compter les tâches par priorité triées par niveau
$labelsPriorite = [];       //Tableau des noms de priorités
$dataPriorite = [];     //Tableau des nombres
while ($row = mysqli_fetch_assoc($parPriorite)) {       //On parcourt les résultats
    $labelsPriorite[] = $row['libelle_priorite'];       //On ajoute le nom de la priorité
    $dataPriorite[] = $row['nb'];       //On ajoute le nombre de tâches
}
?>

<h1 class="mb-4">Dashboard</h1>

<!-- Compteurs : 4 cartes Bootstrap affichant les chiffres clés -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center p-3">
            <h2><?php echo $total; ?></h2>      <!-- Affiche le nombre total de tâches -->
            <p class="text-muted mb-0">Total</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center p-3">
            <h2 class="text-primary"><?php echo $enCours; ?></h2>       <!-- Affiche le nombre de tâches en cours -->
            <p class="text-muted mb-0">En cours</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center p-3">
            <h2 class="text-success"><?php echo $terminees; ?></h2>     <!-- Affiche le nombre de tâches terminées -->
            <p class="text-muted mb-0">Terminées</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center p-3">
            <h2 class="text-danger"><?php echo $enRetard; ?></h2>       <!-- Affiche le nombre de tâches en retard -->
            <p class="text-muted mb-0">En retard</p>
        </div>
    </div>
</div>

<!-- Graphiques : 3 cartes contenant chacune un canvas pour ChartJS -->
<div class="row">
    <div class="col-md-4">
        <div class="card p-3">
            <h5 class="text-center">Par statut</h5>
            <canvas id="chartStatut"></canvas>      <!-- Zone de dessin pour le camembert des statuts -->
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3">
            <h5 class="text-center">Par catégorie</h5>
            <canvas id="chartCategorie"></canvas>       <!-- Zone de dessin pour le diagramme en bâtons des catégories -->
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3">
            <h5 class="text-center">Par priorité</h5>
            <canvas id="chartPriorite"></canvas>        <!-- Zone de dessin pour le diagramme en bâtons des priorités -->
        </div>
    </div>
</div>

<?php
include("includes/footer.inc.php");     //Insertion du HTML footer (charge les CDN Bootstrap JS, moment.js et ChartJS)
?>

<!-- Scripts ChartJS -->
<!-- json_encode() convertit les tableaux PHP en tableaux JavaScript pour que ChartJS puisse les utiliser -->
<script>
//Diagramme camembert (doughnut) pour la répartition des tâches par statut
new Chart(document.getElementById('chartStatut'), {     //On cible le canvas par son id
    type: 'doughnut',       //Type de graphique : camembert troué
    data: {
        labels: <?php echo json_encode($labelsStatut); ?>,      //Les noms des statuts en légende
        datasets: [{
            data: <?php echo json_encode($dataStatut); ?>,      //Les valeurs numériques
            backgroundColor: ['#e8dff0', '#b3e5fc', '#c8e6c9']     //Couleurs pastel : violet (attente), bleu (en cours), vert (terminé)
        }]
    }
});

//Diagramme en bâtons pour la répartition des tâches par catégorie
new Chart(document.getElementById('chartCategorie'), {
    type: 'bar',        //Type de graphique : barres verticales
    data: {
        labels: <?php echo json_encode($labelsCategorie); ?>,       //Les noms des catégories en axe X
        datasets: [{
            label: 'Tâches',
            data: <?php echo json_encode($dataCategorie); ?>,       //Les valeurs numériques en axe Y
            backgroundColor: '#7bc47f'      //Couleur verte pastel pour toutes les barres
        }]
    },
    options: { plugins: { legend: { display: false } } }        //On masque la légende
});

//Diagramme en bâtons pour la répartition des tâches par priorité
new Chart(document.getElementById('chartPriorite'), {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($labelsPriorite); ?>,        //Les noms des priorités en axe X
        datasets: [{
            label: 'Tâches',
            data: <?php echo json_encode($dataPriorite); ?>,        //Les valeurs numériques en axe Y
            backgroundColor: ['#c8e6c9', '#fff3cd', '#f8bbd0']      //Couleurs pastel : vert (peu important), jaune (important), rose (très important)
        }]
    },
    options: { plugins: { legend: { display: false } } }        //On masque la légende
});
</script>