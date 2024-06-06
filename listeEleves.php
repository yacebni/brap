<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Liste des élèves</title>
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">
</head>

<?php
include_once('header.php');
    if (isset($_SESSION['role']) && ($_SESSION['role'] === 'professeur')) {
        $stmtStudents = $connexion->prepare("SELECT DISTINCT(ETUDIANT.PRENOM), ETUDIANT.NOM, ETUDIANT.ID_UTI, G.NOM_GROUPE, CL.NUM_CLASSE, S.NUM_SEM
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
        WHERE P.ID_UTI = ?
        ORDER BY ETUDIANT.NOM, ETUDIANT.PRENOM, CL.NUM_CLASSE, G.NOM_GROUPE;");
        $stmtStudents->bindParam(1, $_SESSION['usercourant']);
        $stmtStudents->execute();
        $students = $stmtStudents->fetchAll(PDO::FETCH_ASSOC);
    } elseif (isset($_SESSION['role']) && ($_SESSION['role'] === 'administrateur')) {
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
        ORDER BY ETUDIANT.NOM, ETUDIANT.PRENOM, CL.NUM_CLASSE, G.NOM_GROUPE;");
        $stmtStudents->execute();
        $students = $stmtStudents->fetchAll(PDO::FETCH_ASSOC);
        }





    if (isset($_SESSION['role']) && ($_SESSION['role'] === 'professeur' || $_SESSION['role'] === 'administrateur')) {

    ?>
    <main id="liste_elèves">
        <div class="barre_recherche_eleves">
            <i class="fa-solid fa-magnifying-glass"></i>
            <form action="" method="GET">
                <input type="search" name="search" placeholder="Rechercher par nom, prénom ou groupe" id="searchInput">
            </form>
        </div>

        <table class="liste_eleves">
            <thead>
            <tr>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Classe</th>
                <th>Groupe</th>
                <th>Semestre</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($students as $student) : ?>
                <tr class="student" data-student-id="<?= $student['ID_UTI'] ?>">
                    <td><?= $student['NOM'] ?></td>
                    <td><?= $student['PRENOM'] ?></td>
                    <td>G<?= $student['NUM_CLASSE'] ?></td>
                    <td><?= $student['NOM_GROUPE'] ?></td>
                    <td><?= $student['NUM_SEM'] ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>


        <div id="pop-up" class="pop-up" style="display: none">
            <div class="pop-up-content">
                <span class="fermer-pop-up">&times;</span>
                <h2>Ajouter avertissement/convocation</h2>
                <h3 id="studentName"></h3>


                <form id="popupForm" method="post" action="traitement.php">
                    <!-- Champ caché pour stocker l'ID de l'étudiant -->
                    <input type="hidden" id="studentId" name="studentId">

                    <label for="typeAdm">Type d'administratif :</label>
                    <select id="typeAdm" name="typeAdm" required>
                        <option value="Avertissement">Avertissement</option>
                        <option value="Convocation">Convocation</option>
                    </select>

                    <label for="dateConvocation" id="labelDateConvocation" style="visibility: hidden;">Date convocation :</label>
                    <input type="date" id="dateConvocation" name="dateConvocation" style="visibility: hidden; position: absolute;">

                    <label for="heureDebut" id="labelHeureDebut" style="visibility: hidden;">Heure de début :</label>
                    <input type="time" id="heureDebut" name="heureDebut" style="visibility: hidden; position: absolute;">

                    <label for="heureFin" id="labelHeureFin" style="visibility: hidden;">Heure de fin :</label>
                    <input type="time" id="heureFin" name="heureFin" style="visibility: hidden; position: absolute;">

                    <label for="commentaire" id="commentaire-label">Commentaire :</label>
                    <input type="text" id="commentaire" name="commentaire" rows="4" cols="50" required>

                    <button type="submit" value="add_adm" name="Action">Valider</button>
                </form>

            </div>
        </div>



        <script src="scripts/listeEleves.js"></script>
    </main>
    <?php
} else {
    header('Location: index.php');
}
include_once('footer.php');
?>
