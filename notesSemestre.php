<?php
include_once('configDB.php');

$semestre = $_SESSION['semestre'];

if (isset($_POST['param'])) {
    $parametre = $_POST['param'];
    $param = ($parametre == 1) ? $semestre[0]['NUM_SEM'] : $semestre[1]['NUM_SEM'];
}
if ($_SESSION['role'] == 'administrateur') {
    // Vérifier si l'ID de l'étudiant est présent dans l'URL
    if (isset($_SESSION['id_etudiant'])) {
        $id_etudiant = $_SESSION['id_etudiant'];

        $stmtNotes = $connexion->prepare("
            SELECT c.NOM_COMP, mr.ID_MATIERE, mr.NOM_MATIERE, e.NOM_EXAM, e.TOTAL_P, e.COEF_EVAL, n.NOTE, n.COMMENTAIRE, Moyenne(e.ID_EXAMEN) as MOYENNENOTE, ObtenirClassementEtudiant(n.ID_ETUDIANT,e.ID_EXAMEN) as RANGELEVE, Mediane(e.ID_EXAMEN) AS MEDIANE, mpc.ID_COMP
            FROM COMPETENCE c
            JOIN MATIERE_PAR_COMP mpc ON c.ID_COMP = mpc.ID_COMP
            JOIN MATIERE_RESSOURCE mr ON mpc.ID_MATIERE = mr.ID_MATIERE
            JOIN EVALUATION e ON e.ID_MATIERE = mr.ID_MATIERE
            LEFT JOIN NOTE n ON n.ID_EXAMEN = e.ID_EXAMEN AND n.NOTE IS NOT NULL
            WHERE c.ID_SEM = ?
            AND n.ID_ETUDIANT = ?;
        ");
        $stmtNotes->bindParam(1, $param);
        $stmtNotes->bindParam(2, $id_etudiant);
    }
} else {
    $stmtNotes = $connexion->prepare("
    SELECT c.NOM_COMP, mr.ID_MATIERE, mr.NOM_MATIERE, e.NOM_EXAM, e.TOTAL_P, e.COEF_EVAL, n.NOTE, n.COMMENTAIRE, Moyenne(e.ID_EXAMEN) as MOYENNENOTE, ObtenirClassementEtudiant(n.ID_ETUDIANT,e.ID_EXAMEN) as RANGELEVE, Mediane(e.ID_EXAMEN) AS MEDIANE, mpc.ID_COMP
    FROM COMPETENCE c
    JOIN MATIERE_PAR_COMP mpc ON c.ID_COMP = mpc.ID_COMP
    JOIN MATIERE_RESSOURCE mr ON mpc.ID_MATIERE = mr.ID_MATIERE
    JOIN EVALUATION e ON e.ID_MATIERE = mr.ID_MATIERE
    LEFT JOIN NOTE n ON n.ID_EXAMEN = e.ID_EXAMEN AND n.NOTE IS NOT NULL
    WHERE c.ID_SEM = ?
    AND n.ID_ETUDIANT = ?;
");

    $stmtNotes->bindParam(1, $param);
    $stmtNotes->bindParam(2, $_SESSION['usercourant']);
}

$stmtNotes->execute();
$notes = $stmtNotes->fetchAll(PDO::FETCH_ASSOC);

$notesParCompetence = groupBy($notes, 'NOM_COMP', 'NOM_MATIERE');


$stmtCoeff = $connexion->prepare("
    SELECT mpc.ID_MATIERE, mpc.COEF_COMP
    FROM MATIERE_PAR_COMP mpc
    WHERE mpc.ID_COMP = ?
    AND mpc.ID_MATIERE = ?;
");

foreach ($notesParCompetence as $competence => $competenceNotes) : ?>
    <div class="competences">
        <h2><?= $competence ?></h2>
        <button class="fleche bas"></button>
        <div class="contenu-competence">
            <?php foreach ($competenceNotes as $matiere => $matiereNotes) : ?>
                <div class="matiere">
                    <?php
                    // Récupérer le coefficient de la matière à partir de la table MATIERE_PAR_COMP
                    $stmtCoeff->bindParam(1, $matiereNotes[0]['ID_COMP']);
                    $stmtCoeff->bindParam(2, $matiereNotes[0]['ID_MATIERE']);
                    $stmtCoeff->execute();
                    $coefficientMatiere = $stmtCoeff->fetch(PDO::FETCH_ASSOC)['COEF_COMP'];
                    ?>
                    <h2><?= $matiere ?> (Coef <?= $coefficientMatiere ?>)</h2>
                    <button class="fleche bas"></button>
                    <div class="contenu-matiere">
                        <?php foreach ($matiereNotes as $evaluation) : ?>
                            <?php generateTable($evaluation); ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endforeach; ?>

<?php
// Fonction pour regrouper les éléments d'un tableau par une clé donnée
function groupBy($array, $key1, $key2)
{
    $result = array();
    foreach ($array as $item) {
        $result[$item[$key1]][$item[$key2]][] = $item;
    }
    return $result;
}

// Fonction pour générer les balises de tableau
function generateTable($evaluation)
{
    echo '<table>';
    echo '<thead>';
    echo '<tr>';
    echo '<th>Note</th>';
    echo '<th>Coefficient</th>';
    echo '<th>Rang</th>';
    echo '<th>Moyenne</th>';
    echo '<th>Médiane</th>';
    echo '<th>Appréciation</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    echo '<tr>';
    echo '<td>' . $evaluation['NOTE'] . '/' . $evaluation['TOTAL_P'] . '</td>';
    echo '<td>' . $evaluation['COEF_EVAL'] . '</td>';
    echo '<td>' . $evaluation['RANGELEVE'] . '</td>';
    echo '<td>' . $evaluation['MOYENNENOTE'] . '</td>';
    echo '<td>' . $evaluation['MEDIANE'] . '</td>';
    echo '<td>' . $evaluation['COMMENTAIRE'] . '</td>';
    echo '</tr>';
    echo '</tbody>';
    echo '</table>';
}
?>