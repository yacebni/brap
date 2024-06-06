<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Faire l'appel</title>
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">
</head>
<?php
include_once('header.php');
?>
<main id="appel">
    <?php
    if(empty($_SESSION['id_cours'])){
        echo "<h2>Aucun cours n'a été choisi.</h2>";
        echo '<a class="back" href="emploiDuTemps.php">Choisir un cours</a>';
        exit(0);
    }
    $stmtcours = $connexion->prepare("
            SELECT U.NOM, U.PRENOM, U.ID_UTI, C.J_COURS, C.H_D_COURS, C.H_F_COURS, MR.NOM_MATIERE FROM COURS C
            JOIN GROUPE_PAR_COURS GPC ON GPC.ID_COURS = C.ID_COURS
            JOIN ETUDIANT_PAR_GROUPE EPC ON EPC.ID_GROUPE = GPC.ID_GROUPE
            JOIN UTILISATEUR U ON U.ID_UTI = EPC.ID_ETUDIANT
            JOIN MATIERE_RESSOURCE MR ON MR.ID_MATIERE = C.ID_MATIERE
            WHERE C.ID_COURS = ?
            ORDER BY U.NOM, U.PRENOM;");
    $stmtcours->bindParam(1, $_SESSION['id_cours']);
    $stmtcours->execute();
    $infoseleves = $stmtcours->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <?php
    if ($_SESSION['role'] == 'professeur') {

        $detailsCours = $infoseleves[0]; // Supposons que tous les détails du cours sont les mêmes pour chaque élève, donc on peut prendre le premier élève

        $date = new DateTime($detailsCours['J_COURS']);
        $dateFrancaise = $date->format('d/m/Y'); // Formatage de la date en JJ/MM/YYYY

        $heure = date('G\hi', strtotime($detailsCours['H_D_COURS']));

        echo '<h2>Appel du cours de ' . $detailsCours['NOM_MATIERE'] . ' du ' . $dateFrancaise . ' à ' . $heure . '</h2>';

        ?>
        <form action="traitement.php" method="post" class="form-appel">
            <button type="submit" value="appel" class="submit-appel" name="Action">Valider l'appel</button>
            <div class="tab-appel">
                <table>
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Retard</th>
                            <th>Absence</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($infoseleves as $eleve) {
                            echo '<tr>';
                            echo '<td>' . $eleve["NOM"] . '</td>';
                            echo '<td>' . $eleve["PRENOM"] . '</td>';
                            echo '<td>';
                            echo '<select name="retard[' . $eleve['ID_UTI'] . ']">'; // Utilisation de l'ID de l'élève dans le nom du select
                            echo '<option value="0">0 minute</option>';
                            echo '<option value="5">5 minutes</option>';
                            echo '<option value="10">10 minutes</option>';
                            echo '<option value="15">15 minutes</option>';
                            echo '<option value="20">20 minutes</option>';
                            echo '</select>';
                            echo '</td>';
                            echo '<td>';
                            echo '<input type="checkbox" name="absence[' . $eleve['ID_UTI'] . ']">'; // Utilisation de l'ID de l'élève dans le nom de la checkbox
                            echo '</td>';
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>

        </form>
    <?php
    }
    if ($_SESSION["role"] == 'eleve') {
        header("Location: index.php");
    } ?>
    <script src="scripts/appel.js"></script>
</main>
<?php
include_once('footer.php');
?>