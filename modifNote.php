<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Modifier l'évaluation</title>
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

if (isset($_GET['ID_EXAMEN'])) {
    $idExamen = $_GET['ID_EXAMEN'];
    $stmtEval = $connexion->prepare("
    SELECT DISTINCT mr.NOM_MATIERE, e.NOM_EXAM, e.DATE_EVAL, e.TOTAL_P, e.COEF_EVAL,cl.NUM_CLASSE
    FROM matiere_ressource mr
    JOIN matiere_par_comp mpc ON mr.ID_MATIERE = mpc.ID_MATIERE
    JOIN evaluation e ON e.ID_MATIERE = mr.ID_MATIERE
    LEFT JOIN COURS co ON co.ID_MATIERE = mr.ID_MATIERE  
    LEFT JOIN CLASSE cl ON cl.ID_CLASSE = e.ID_CLASSE
    LEFT JOIN SEMESTRE s ON s.ID_SEM = cl.ID_SEM
    WHERE e.ID_EXAMEN = ?;
    ");
    $stmtEval->bindParam(1, $idExamen);
    $stmtEval->execute();
    $eval = $stmtEval->fetchAll(PDO::FETCH_ASSOC);
    $evaluation = $eval[0];

    $stmtNotes = $connexion->prepare("
    SELECT DISTINCT u.PRENOM, u.NOM, n.NOTE, n.COMMENTAIRE, n.ID_NOTE
    FROM UTILISATEUR u
    JOIN NOTE n ON u.ID_UTI = n.ID_ETUDIANT
    WHERE n.ID_EXAMEN = ?;
    ");
    $stmtNotes->bindParam(1, $idExamen);
    $stmtNotes->execute();
    $notes = $stmtNotes->fetchAll(PDO::FETCH_ASSOC);
}
?>

<main class="conteneurModifEval">
    <div class="detailsEval">
        <a class="back" href="listeEvalsProf.php">Retour aux évaluations</a>
        <form method="POST" action="traitement.php">
            <input type="hidden" name="id_evaluation" value="<?php echo $idExamen; ?>">
            <table>
                <tr>
                    <th>Date</th>
                    <td><input type="date" name="date_eval" value="<?php echo $evaluation['DATE_EVAL']; ?>"></td>
                </tr>
                <tr>
                    <th>Matière</th>
                    <td><input type="text" name="nom_matiere" value="<?php echo $evaluation['NOM_MATIERE']; ?>"></td>
                </tr>
                <tr>
                    <th>Évaluation</th>
                    <td><input type="text" name="nom_exam" value="<?php echo $evaluation['NOM_EXAM']; ?>"></td>
                </tr>
                <tr>
                    <th>Coefficient</th>
                    <td><input type="text" name="coef_eval" value="<?php echo $evaluation['COEF_EVAL']; ?>"></td>
                </tr>
                <tr>
                    <th>Classe</th>
                    <td><input type="text" name="num_classe" value="G<?php echo $evaluation['NUM_CLASSE']; ?>"></td>
                </tr>
                <tr>
                    <th>Note sur</th>
                    <td><input type="text" name="total_points" value="<?php echo $evaluation['TOTAL_P']; ?>"></td>
                </tr>
            </table>
            <input type="hidden" name="Action" value="updateEval">
            <button type="submit" class="submitEvalButton" disabled>Modifier</button>
        </form>
    </div>
    <div class="noteParEleve">
        <form method="POST" action="traitement.php">
            <table>
                <thead>
                    <tr>
                        <th>Prénom</th>
                        <th>Nom</th>
                        <th>Note</th>
                        <th>Commentaire</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($evaluation != null) {
                        $stmtStudents = $connexion->prepare("
                        SELECT DISTINCT u.PRENOM, u.NOM, n.NOTE, n.COMMENTAIRE, n.ID_EXAMEN, u.ID_UTI
                        FROM UTILISATEUR u
                        LEFT JOIN NOTE n ON u.ID_UTI = n.ID_ETUDIANT AND n.ID_EXAMEN = ?
                        LEFT JOIN ETUDIANT_PAR_GROUPE epg ON u.ID_UTI = epg.ID_ETUDIANT
                        WHERE epg.ID_GROUPE IN (
                            SELECT g.ID_GROUPE
                            FROM GROUPE g
                            JOIN CLASSE c ON g.ID_CLASSE = c.ID_CLASSE
                            WHERE c.NUM_CLASSE = ?
                        )
                    ");
                        $stmtStudents->bindParam(1, $idExamen);
                        $stmtStudents->bindParam(2, $evaluation['NUM_CLASSE']);
                        $stmtStudents->execute();
                        $students = $stmtStudents->fetchAll(PDO::FETCH_ASSOC);

                        foreach ($students as $student) : ?>
                            <tr>
                                <td><?php echo $student['PRENOM']; ?></td>
                                <td><?php echo $student['NOM']; ?></td>
                                <td><input type="number" name="note[]" min="0" max="<?php echo $evaluation['TOTAL_P']; ?>" value="<?php echo $student['NOTE'] ?? ''; ?>"></td>
                                <td><input type="text" name="commentaire[]" value="<?php echo $student['COMMENTAIRE'] ?? ''; ?>"></td>
                                <input type="hidden" name="id_evaluation[]" value="<?php echo $idExamen; ?>">
                                <input type="hidden" name="id_eleve[]" value="<?php echo $student['ID_UTI']; ?>">
                            </tr>
                    <?php endforeach;
                    } ?>
                </tbody>
            </table>
            <input type="hidden" name="Action" value="updateNote">
            <button type="submit" class="submitNoteButton" disabled>Valider</button>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="scripts/modifNote.js"></script>
</main>

<?php
include_once('footer.php');
?>