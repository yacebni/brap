<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Notes élèves</title>
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">
</head>
<?php
include_once('header.php');
?>

<main id="liste_elèves">
    <?php
    if ($_SESSION['role'] == 'administrateur') {
        $stmtStudents = $connexion->prepare("SELECT DISTINCT(ETUDIANT.PRENOM), ETUDIANT.NOM, ETUDIANT.ID_UTI, ETUDIANT.STATUT, G.NOM_GROUPE, CL.NUM_CLASSE, S.NUM_SEM
        FROM UTILISATEUR P
        INNER JOIN COURS C ON C.ID_PROFESSEUR = P.ID_UTI
        INNER JOIN GROUPE_PAR_COURS GPC ON GPC.ID_COURS = C.ID_COURS
        INNER JOIN GROUPE G ON G.ID_GROUPE = GPC.ID_GROUPE
        INNER JOIN CLASSE CL ON CL.ID_CLASSE = G.ID_CLASSE
        INNER JOIN ETUDIANT_PAR_GROUPE EPC ON EPC.ID_GROUPE = GPC.ID_GROUPE
        INNER JOIN UTILISATEUR AS ETUDIANT ON ETUDIANT.ID_UTI = EPC.ID_ETUDIANT
        INNER JOIN MATIERE_PAR_COMP MPC ON MPC.ID_MATIERE = C.ID_MATIERE
        INNER JOIN COMPETENCE CP ON CP.ID_COMP = MPC.ID_COMP
        INNER JOIN SEMESTRE S ON S.ID_SEM = CP.ID_SEM
        WHERE ETUDIANT.STATUT = 'E'
        ORDER BY ETUDIANT.NOM, ETUDIANT.PRENOM, CL.NUM_CLASSE, G.NOM_GROUPE;"); // Limite le résultat aux 20 derniers événements

        $stmtStudents->execute();
        $students = $stmtStudents->fetchAll(PDO::FETCH_ASSOC);

        if ($students) {
            ?>
            <div id="liste_eleves">
                <div class="barre_recherche_eleves">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <form action="" method="GET">
                        <input type="search" name="search" placeholder="Rechercher par nom, prénom, groupe ou type" id="searchInput">
                    </form>
                </div>

                <table class="liste_eleves">
                    <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Groupe</th>
                        <th>Semestre</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($students as $student) { ?>
                        <tr class="student" data-id-etudiant="<?= $student['ID_UTI'] ?>">
                            <td><?= $student['NOM'] ?></td>
                            <td><?= $student['PRENOM'] ?></td>
                            <td>G<?= $student['NUM_CLASSE'] . $student['NOM_GROUPE']?></td>
                            <td><?= $student['NUM_SEM'] ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
            <?php
        } else {
            echo '<p>Aucun élève trouvé.</p>';
        }
    } else {
        echo '<p>Vous n\'avez pas les autorisations nécessaires pour accéder à cette page.</p>';
    }
    ?>
    <script src="scripts/listeEvalsAdmin.js"></script>

</main>

<?php
include_once('footer.php');
?>
