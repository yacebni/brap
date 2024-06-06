<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Évaluations</title>
    <link rel="stylesheet" href="styles/style.css">
</head>

<?php
include_once('header.php');

if($_SESSION['role'] == 'eleve') {
    echo '<p>Vous n\'avez pas les autorisations nécessaires pour accéder à cette page.</p>';
    exit(0);
}
else if($_SESSION['role'] == 'administrateur') {
    header('location: notes.php');
    exit(0);
}

$usercourant = $_SESSION['usercourant'];
// Récupérer les évaluations
function getEvaluations($usercourant, $connexion)
{
    $stmtEvaluations = $connexion->prepare(
        "SELECT DISTINCT e.ID_EXAMEN, mpr.NOM_MATIERE, e.NOM_EXAM, e.DATE_EVAL, e.TOTAL_P, e.COEF_EVAL, s.NUM_SEM, cl.ID_CLASSE, cl.NUM_CLASSE, Moyenne(e.ID_EXAMEN) AS MOYENNENOTE, Mediane(e.ID_EXAMEN) AS MEDIANE
    FROM evaluation e
    JOIN classe cl ON cl.ID_CLASSE = e.ID_CLASSE
    JOIN matiere_ressource mpr ON mpr.ID_MATIERE = e.ID_MATIERE
    JOIN matiere_par_comp mpc ON mpc.ID_MATIERE = e.ID_MATIERE
    JOIN competence c ON c.ID_COMP = mpc.ID_COMP
    JOIN semestre s ON s.ID_SEM = (SELECT ID_SEM FROM CLASSE WHERE ID_CLASSE = e.ID_CLASSE)
    WHERE e.ID_PROFESSEUR = ?;
    "
    );
    $stmtEvaluations->bindParam(1, $usercourant);
    $stmtEvaluations->execute();
    return $stmtEvaluations->fetchAll(PDO::FETCH_ASSOC);
}

// Afficher les évaluations
function displayEvaluations($evaluations)
{
    echo '<div class="listeEvalsProf-container"><table><thead><tr><th>Date</th><th>Matière</th><th>Évaluation</th><th>Coefficient</th><th>Total de points</th><th>Classe</th><th>Semestre</th><th>Moyenne</th><th>Médianne</th></tr></thead><tbody>';
    foreach ($evaluations as $evaluation) {
        echo '<tr data-id-exam="' . $evaluation['ID_EXAMEN'] . '">';
        // Détails de chaque évaluation
        echo '<td>' . $evaluation['DATE_EVAL'] . '</td>';
        echo '<td>' . $evaluation['NOM_MATIERE'] . '</td>';
        echo '<td>' . $evaluation['NOM_EXAM'] . '</td>';
        echo '<td>' . $evaluation['COEF_EVAL'] . '</td>';
        echo '<td>' . $evaluation['TOTAL_P'] . '</td>';
        echo '<td> G' . $evaluation['NUM_CLASSE'] . '</td>';
        echo '<td>' . $evaluation['NUM_SEM'] . '</td>';
        echo '<td>' . $evaluation['MOYENNENOTE'] . '</td>';
        echo '<td>' . $evaluation['MEDIANE'] . '</td>';
        echo '</tr>';
    }
    echo '</tbody></table></div>';
}

// Récupérer les informations pour le formulaire
function getFormInfo($usercourant, $connexion)
{
    $stmtInfos = $connexion->prepare(
        "SELECT DISTINCT mr.NOM_MATIERE, cl.NUM_CLASSE, g.NOM_GROUPE, s.NUM_SEM, c.ID_MATIERE
        FROM matiere_ressource mr
        JOIN matiere_par_ens mpe ON mpe.ID_MATIERE = mr.ID_MATIERE
        LEFT JOIN cours c ON c.ID_MATIERE = mr.ID_MATIERE
        LEFT JOIN groupe_par_cours gpc ON gpc.ID_COURS = c.ID_COURS
        LEFT JOIN groupe g ON g.ID_GROUPE = gpc.ID_GROUPE
        LEFT JOIN classe cl ON cl.ID_CLASSE = g.ID_CLASSE
        LEFT JOIN matiere_par_comp mpc ON mpc.ID_MATIERE = mr.ID_MATIERE
        LEFT JOIN competence comp ON comp.ID_COMP = mpc.ID_COMP
        LEFT JOIN semestre s ON s.ID_SEM = cl.ID_SEM
        WHERE mpe.ID_PROFESSEUR = ? AND c.ID_PROFESSEUR = ?;"
    );

    $stmtInfos->bindParam(1, $usercourant);
    $stmtInfos->bindParam(2, $usercourant);
    $stmtInfos->execute();

    return $stmtInfos->fetchAll(PDO::FETCH_ASSOC);
}

function getIdClasse($numClasse, $numSemestre, $connexion)
{
    $stmt = $connexion->prepare("SELECT ID_CLASSE FROM CLASSE WHERE NUM_CLASSE = ? AND ID_SEM = ?");
    $stmt->bindParam(1, $numClasse);
    $stmt->bindParam(2, $numSemestre);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result['ID_CLASSE'];
}

// Afficher le formulaire
function displayForm($infos, $connexion)
{
    $classes = array_column($infos, 'NUM_CLASSE');
    $classesUniques = array_unique($classes);
    $matieres = array_column($infos, 'NOM_MATIERE');
    $matieresUniques = array_unique($matieres);

    echo '<div id="pop-up" class="pop-up">
    <div class="pop-up-content">
        <span class="fermer-pop-up" onclick="fermerPopUp()">&times;</span>
        <h2>Ajouter une évaluation</h2>
        <form method="POST">
            <label for="date">Entrez une date* :</label>
            <input type="date" id="date" name="date" required><br>
            <label for="matiere">Sélectionnez une matière* :</label>
            <select name="matiere" id="matiere" required>
                <option value="" disabled selected>Choisir une option</option>';

    foreach ($matieresUniques as $matiere) {
        echo '<option value="' . $infos[array_search($matiere, $matieres)]['ID_MATIERE'] . '">' . $matiere . '</option>';
    }


    echo '</select><br>
    <label for="nomEval">Entrez le nom de l\'évaluation* :</label>
    <input type="text" name="nomEval" placeholder="Nom de l\'évaluation" required><br>
    <label for="total">Entrez un total de points* :</label>
    <input type="text" name="total" placeholder="Total de points" required><br>
    <label for="coefficient">Entrez un coefficient* :</label>
            <input type="text" name="coefficient" placeholder="Coefficient" required><br> <!-- Assurez-vous que le name correspond à la clé attendue dans $_POST -->
            <label for="classe">Sélectionnez une classe :</label>
            <select name="classe" id="classe">
                <option value="" selected>Choisir une option</option>';
    foreach ($classesUniques as $index => $classe) {
        $numSemestre = $infos[$index]['NUM_SEM'];
        $idClasse = getIdClasse($classe, $numSemestre, $connexion);
        echo '<option value="' . $idClasse . '"> G' . $classe . 'S' . $numSemestre . '</option>';
    }

    echo '</select><br>
            <button type="submit">Ajouter</button>
        </form>
    </div>
</div>';
}


function soumettreFormulaire($connexion)
{
    // Vérifie si le formulaire a été soumis
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $matiere = $_POST['matiere'];
        $nomEval = $_POST['nomEval'];
        $date = $_POST['date'];
        $total = $_POST['total'];
        $coefficient = $_POST['coefficient'];
        $classeId = $_POST['classe'];

        $stmt = $connexion->prepare("
            INSERT INTO evaluation (ID_PROFESSEUR, ID_MATIERE, NOM_EXAM, DATE_EVAL, TOTAL_P, COEF_EVAL, ID_CLASSE)
            VALUES (:prof, :matiere, :nomEval, :date, :total, :coefficient, :classe)
            ");

        $stmt->bindParam(':prof', $_SESSION['usercourant']);
        $stmt->bindParam(':matiere', $matiere);
        $stmt->bindParam(':nomEval', $nomEval);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':total', $total);
        $stmt->bindParam(':coefficient', $coefficient);
        $stmt->bindParam(':classe', $classeId);

        $stmt->execute();


        // Enlever l'ancien affichage avant de récupérer les nouvelles évaluations
        echo '<script>document.querySelector(".listeEvalsProf-container").remove();window.history.replaceState({}, document.title, window.location.pathname);</script>';

        // Réafficher les évaluations
        $evaluations = getEvaluations($_SESSION['usercourant'], $connexion);
        displayEvaluations($evaluations);
    }
}

$evaluations = getEvaluations($_SESSION['usercourant'], $connexion);

include_once('header.php');
?>
<main class="notes-page">
    <div class="ajoutEval">Ajouter</div>
    <?php
    displayEvaluations($evaluations);

    $infos = getFormInfo($_SESSION['usercourant'], $connexion);

    displayForm($infos, $connexion);

    soumettreFormulaire($connexion);
    ?>
    <script src="scripts/listeEvalsProf.js"></script>
</main>
<?php
include_once('footer.php');
?>