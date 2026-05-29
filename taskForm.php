<?php
include("includes/connectDB.inc.php");      //Connexion à la BDD
include("includes/header.inc.php");     //Insertion du HTML header

//On vérifie le code de session
if (array_key_exists('code', $_GET) and array_key_exists($_GET['code'], $_SESSION['code'])) {
    $id_tache = intval($_SESSION['code'][$_GET['code']]);       //On récupère le code de session s'il est valide
} else {
    header("Location: todolist.php");       //Si le code de session n'est pas valide, renvoie vers l'accueil
    exit();
}

//On charge les attributs des tâches
$categories = mysqli_query($link, "SELECT * FROM categorie");       //On récupère la table "catégorie"
$priorites = mysqli_query($link, "SELECT * FROM priorite");     //On récupère la table "priorite"
$statut = mysqli_query($link, "SELECT * FROM statut");      //On récupère la table "statut"

//Si on édite une tâche (id != 0) on charge la tâche
if ($id_tache != 0) {
    $query = "SELECT * FROM tache WHERE id_tache = ?";      //Requête SQL à préparer
    $stmt = mysqli_prepare($link, $query);      //On prépare la requête SQL pour sécuriser les valeurs

    mysqli_stmt_bind_param($stmt, 'i', $id_tache);      //On lie la valeur du formulaire au "?"
    mysqli_stmt_execute($stmt);     //On exécute la requête
    mysqli_stmt_bind_result($stmt, $id_tache, $titre, $description, $date_limite, $date_creation, $date_modification, $id_categorie, $id_priorite, $id_statut);     //On associe les colonnes du résultats à des variables PHP pour les lire
    mysqli_stmt_fetch($stmt);   //On récupère chaque ligne dans les variables liées
}
?>

<!-- Le design de la page est géré grâce aux class bootstrap placé dans les attributs des éléments HTML -->
<div class="col-md-8 mx-auto">

    <!-- Affichage dynamique du h1, "Ajouter" si id = 0, "Éditer" sinon -->
    <?php if ($id_tache == 0) {
        echo "<h1 class='mb-4'>Ajouter une nouvelle tâche</h1>";
    } else {
        echo "<h1 class='mb-4'>Éditer une tâche</h1>";
    } ?>

    <!-- Les champs marqués d'un * sont obligatoires -->
    <p class="text-muted"><span class="text-danger">*</span> Champs obligatoires</p>


    <!-- Début du formulaire -->
    <form action="saveTask.php" method="post" class="needs-validation" novalidate>

        <!-- On récupère le code de session -->
        <input type="hidden" name="code" value="<?php echo $_GET['code']; ?>">


        <div class="mb-3">
            <label class="form-label fw-bold">Nom<span class="text-danger">*</span></label>
            <!-- Si on édite on affiche le nom de la tâche sinon on affiche rien -->
            <input type="text" name="titre" class="form-control form-control-lg" placeholder="Ex: Faire projet Todolist" required value="<?php if ($id_tache != 0) echo htmlspecialchars($titre ?? ''); ?>">
            <div class="invalid-feedback">
                Veuillez entrez le nom de la tâche
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Description</label>
            <!-- Si on édite on affiche la description de la tâche sinon on affiche rien -->
            <textarea name="description" class="form-control" rows="3" placeholder="Ex: Revoir les cours de PHP et consulter la documentation Bootstrap"><?php if ($id_tache != 0) echo htmlspecialchars($description ?? ''); ?></textarea>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label fw-bold">Date limite</label>
                <!-- Si on édite on affiche la date limite de la tâche sinon on affiche l'affichage par défaut -->
                <input type="date" name="date_limite" class="form-control" value="<?php echo ($id_tache != 0 && $date_limite) ? $date_limite : date('Y-m-d'); ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Catégorie<span class="text-danger">*</span></label>
                <!-- Si on édite on affiche la catégorie de la tâche sinon on affiche "Choisir" -->
                <select name="id_categorie" class="form-select" required>
                    <option value="">-- Choisir --</option>
                    <!-- Boucle qui permet d'afficher ou non la catégorie de chaque tâche -->
                    <?php
                    while ($cat = mysqli_fetch_assoc($categories)) {
                        $selection = ($id_tache != 0 && $id_categorie == $cat['id_categorie']) ? 'selected' : '';
                        echo "<option value='{$cat['id_categorie']}' $selection>{$cat['nom_categorie']}</option>";
                    }
                    ?>
                </select>
                <div class="invalid-feedback">
                    Veuillez choisir sa catégorie
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label fw-bold">Priorité<span class="text-danger">*</span></label>
                <!-- Si on édite on affiche la priorité de la tâche sinon on affiche "Choisir" -->
                <select name="id_priorite" class="form-select" required>
                    <option value="">-- Choisir --</option>
                    <!-- Boucle qui permet d'afficher ou non la priorité de chaque tâche -->
                    <?php while ($prio = mysqli_fetch_assoc($priorites)) {
                        $selected = ($id_tache != 0 && $id_priorite == $prio['id_priorite']) ? 'selected' : '';
                        echo "<option value='{$prio['id_priorite']}' $selected>{$prio['libelle_priorite']}</option>";
                    } ?>
                </select>
                <div class="invalid-feedback">
                    Veuillez choisir sa priorité
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Statut<span class="text-danger">*</span></label>
                <!-- Si on édite on affiche le statut de la tâche sinon on affiche "Choisir" -->
                <select name="id_statut" class="form-select" required>
                    <option value="">-- Choisir --</option>
                    <!-- Boucle qui permet d'afficher ou non le statut de chaque tâche -->
                    <?php while ($stat = mysqli_fetch_assoc($statut)) {
                        $selected = ($id_tache != 0 && $id_statut == $stat['id_statut']) ? 'selected' : '';
                        echo "<option value='{$stat['id_statut']}' $selected>{$stat['libelle_statut']}</option>";
                    } ?>
                </select>
                <div class="invalid-feedback">
                    Veuillez choisir son statut
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-lg mt-3">Enregistrer</button>
    </form>

</div>

<script>
    //Script Javascript issu de la documentation Bootstrap qui permet de vérifier et afficher si des champs sont validés ou non

    // Example starter JavaScript for disabling form submissions if there are invalid fields
    (function() {
        'use strict';
        window.addEventListener('load', function() {
            // Fetch all the forms we want to apply custom Bootstrap validation styles to
            var forms = document.getElementsByClassName('needs-validation');
            // Loop over them and prevent submission
            var validation = Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }, false);
    })();
</script>

<?php
include("includes/footer.inc.php");     //Insertion du HTML footer
?>