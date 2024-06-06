<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Notes</title>
    <link rel="stylesheet" href="styles/style.css">
</head>

<?php
include_once('header.php');
// Vérifier le rôle de l'utilisateur
if($_SESSION['role'] == 'professeur') {
    header('location: listeEvalsProf.php');
    exit(0);
}
else if ($_SESSION['role'] == 'administrateur') {
    // Vérifier si l'ID de l'étudiant est présent dans l'URL
    if (isset($_GET['id'])) {
        $id_etudiant = $_GET['id'];
        $_SESSION['id_etudiant'] = $id_etudiant;

        // Requête pour un administrateur avec l'ID de l'étudiant fourni
        $stmtUserInfo = $connexion->prepare("
            SELECT NOM, PRENOM
            FROM UTILISATEUR
            WHERE ID_UTI = ?;");
        $stmtUserInfo->bindParam(1, $id_etudiant);
        $stmtUserInfo->execute();
        $userInfo = $stmtUserInfo->fetch(PDO::FETCH_ASSOC);

        echo '<body class="notes_admin">';
        echo '<h1 class="title_notes">Notes de ' . $userInfo['NOM'] . ' ' . $userInfo['PRENOM'] . '</h1>';
    } else {
        echo "ID étudiant non spécifié.";
        exit; // Arrêter l'exécution si l'ID étudiant n'est pas spécifié
    }
} else {
    // Requête pour un élève
    $stmtUserInfo = $connexion->prepare("
        SELECT NOM, PRENOM
        FROM UTILISATEUR
        WHERE ID_UTI = ?;");
    $stmtUserInfo->bindParam(1, $_SESSION['usercourant']);
    $stmtUserInfo->execute();
    $userInfo = $stmtUserInfo->fetch(PDO::FETCH_ASSOC);
}

// Requête pour obtenir les semestres
$stmtSemestre = $connexion->prepare("
    SELECT s.NUM_SEM
    FROM SEMESTRE s
    JOIN CLASSE c ON s.ID_SEM = c.ID_SEM
    JOIN GROUPE g ON c.ID_CLASSE = g.ID_CLASSE
    JOIN ETUDIANT_PAR_GROUPE epg ON g.ID_GROUPE = epg.ID_GROUPE
    WHERE epg.ID_ETUDIANT = ?;");
$stmtSemestre->bindValue(1, ($_SESSION['role'] == 'administrateur') ? $id_etudiant : $_SESSION['usercourant']);
$stmtSemestre->execute();
$semestre = $stmtSemestre->fetchAll(PDO::FETCH_ASSOC);
$_SESSION['semestre'] = $semestre;
?>

<main class="notes-page">
    <div class="select-semestre">
        <?php
        foreach ($semestre as $sem) {
            echo '<h3>S' . $sem['NUM_SEM'] . '</h3>';
        };
        ?>
    </div>

    <div class="notes-container"></div>

    <?php
    // Ajoutez le bouton d'exportation pour les administrateurs
    if ($_SESSION['role'] == 'administrateur') {
        echo '<form action="traitement.php" method="post">';
        echo '<input type="hidden" name="id_etudiant" value="' . $id_etudiant . '">';
        echo '<button class="export" type="submit" value="export_notes" name="Action">Exporter les notes vers Excel</button>';
        echo '</form>';
    }
    ?>

    <script src="scripts/notes.js"></script>
</main>

<?php
include_once('footer.php');
?>
