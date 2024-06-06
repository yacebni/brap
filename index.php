<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Accueil</title>
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">
</head>
<?php
include_once('header.php');
?>
<main>

    <?php
    $aujourdhui = date("Y-m-d"); // Obtient la date actuelle au format ISO 8601 (année-mois-jour)
    $debut_semaine = date("Y-m-d", strtotime('last sunday', strtotime($aujourdhui)));
    $fin_semaine = date("Y-m-d", strtotime('next saturday', strtotime($aujourdhui)));


    // Récupérer les notes de l'utilisateur pour la semaine actuelle en utilisant son ID
    $stmtNotesSemaine = $connexion->prepare("
        SELECT EVALUATION.NOM_EXAM, NOTE.NOTE, EVALUATION.TOTAL_P
        FROM NOTE 
        INNER JOIN EVALUATION ON NOTE.ID_EXAMEN = EVALUATION.ID_EXAMEN 
        INNER JOIN UTILISATEUR ON NOTE.ID_ETUDIANT = UTILISATEUR.ID_UTI 
        INNER JOIN MATIERE_RESSOURCE ON EVALUATION.ID_MATIERE = MATIERE_RESSOURCE.ID_MATIERE
        WHERE UTILISATEUR.ID_UTI = ? AND EVALUATION.DATE_EVAL BETWEEN ? AND ?
        ORDER BY EVALUATION.DATE_EVAL DESC;");
    $stmtNotesSemaine->bindParam(1, $_SESSION['usercourant']);
    $stmtNotesSemaine->bindParam(2, $debut_semaine);
    $stmtNotesSemaine->bindParam(3, $fin_semaine);
    $stmtNotesSemaine->execute();
    $notesSemaine = $stmtNotesSemaine->fetchAll(PDO::FETCH_ASSOC);

    $stmtAdmi = $connexion->prepare("
        SELECT ADMINISTRATIF.ID_ADMINI, ADMINISTRATIF.TYPE_ADM, ADMINISTRATIF.J_ADM, PROF_ADMIN.NOM, PROF_ADMIN.PRENOM, ADMINISTRATIF.H_D_ADM, ADMINISTRATIF.STATUT_ADM, ADMINISTRATIF.VU_ELEVE
        FROM ADMINISTRATIF
        JOIN UTILISATEUR AS ETUDIANT ON ETUDIANT.ID_UTI = ADMINISTRATIF.ID_ETUDIANT
        JOIN UTILISATEUR AS PROF_ADMIN ON PROF_ADMIN.ID_UTI = ADMINISTRATIF.ID_UTI_P_A
        WHERE ETUDIANT.ID_UTI = ?
        ORDER BY ADMINISTRATIF.J_ADM DESC;");
    $stmtAdmi->bindParam(1, $_SESSION['usercourant']);
    $stmtAdmi->execute();
    $administratifEleve = $stmtAdmi->fetchAll(PDO::FETCH_ASSOC);

    $stmtAdmiAll = $connexion->prepare("SELECT ADMINISTRATIF.ID_ADMINI, ADMINISTRATIF.TYPE_ADM, ADMINISTRATIF.J_ADM, PROF_ADMIN.NOM, PROF_ADMIN.PRENOM, ADMINISTRATIF.H_D_ADM, ADMINISTRATIF.STATUT_ADM
        FROM ADMINISTRATIF
        JOIN UTILISATEUR  AS ETUDIANT ON ETUDIANT.ID_UTI = ADMINISTRATIF.ID_ETUDIANT
        JOIN UTILISATEUR AS PROF_ADMIN ON PROF_ADMIN.ID_UTI = ADMINISTRATIF.ID_UTI_P_A
        ORDER BY ADMINISTRATIF.J_ADM DESC;")
    ?>

    <?php
    if ($_SESSION['role'] == 'eleve') {
    ?>
        <div class="column" id="edt">
            <div class="date-navigation">
                <button onclick="changeDate(-1)" class="arrow">◄</button>
                <h2 id="currentDate"></h2>
                <button onclick="changeDate(1)" class="arrow">►</button>
            </div>
            <div class="edtJour">

            </div>
        </div>
        <script src="scripts/emploiDuTemps.js"></script>
        <form class="column" method="POST" action="notes.php">
            <h2>Notes de la semaine</h2>
            <ul>
                <?php
                if ($notesSemaine) {
                    foreach ($notesSemaine as $row) {
                        echo "<li><button class='buttonAccueil'><strong class='exam'>" . $row["NOM_EXAM"] . "</strong> ";
                        echo "<span class='note'>" . $row["NOTE"] . " / " . $row["TOTAL_P"] . " </span></button></li>";
                    }
                } else {
                    echo "<li class='columnliVide'>Aucune note trouvée pour cette semaine.</li>";
                }
                ?>
            </ul>
        </form>


        <form class="column" method="POST" action="administratif.php">
            <h2>Administratif</h2>
            <?php
            if ($administratifEleve) {
                foreach ($administratifEleve as $row) {
                    if ($row["TYPE_ADM"] == 'A' && $row["STATUT_ADM"] == 'N') {
                        $date_formattee = date_create_from_format('Y-m-d', $row["J_ADM"]);
                        if ($date_formattee) {
                            $date_formattee = date_format($date_formattee, 'd/m/Y');
                            echo "<li " . (($row['VU_ELEVE'] != 1) ? "class='nonLu'" : "") . "><button class='buttonAccueil' name='administratif_selection' value='" . $row["ID_ADMINI"] . "'><i class='fa-solid fa-calendar-xmark' id='calendrier'></i> Absence le " . $date_formattee . " à justifier </button></li>";
                        }
                    }
                    if ($row["TYPE_ADM"] == 'R') {
                        $date_formattee = date_create_from_format('Y-m-d', $row["J_ADM"]);
                        $heure_formattee = date_create_from_format('H:i:s', $row["H_D_ADM"]);
                        if ($date_formattee && $heure_formattee) {
                            $date_formattee = date_format($date_formattee, 'd/m/Y');
                            $heure_formattee = date_format($heure_formattee, 'H:i');
                            echo "<li " . (($row['VU_ELEVE'] != 1) ? "class='nonLu'" : "") . "><button class='buttonAccueil' name='administratif_selection' value='" . $row["ID_ADMINI"] . "'><i class='fa-regular fa-hourglass-half' id='retard'></i> Retard le " . $date_formattee . " à " . $heure_formattee . "</button></li>";
                        }
                    }
                    if ($row["TYPE_ADM"] == 'C') {
                        $convocationDate = strtotime($row["J_ADM"]);
                        $convocationDateTime = strtotime($row["J_ADM"] . ' ' . $row["H_D_ADM"]);

                        if ($convocationDate >= strtotime('today') || $row['VU_ELEVE'] != 1) {
                            $date_formattee = date('d/m/Y', $convocationDate);
                            $heure_formattee = date('H:i', $convocationDateTime);
                            echo "<li " . (($row['VU_ELEVE'] != 1) ? "class='nonLu'" : "") . "><button class='buttonAccueil' name='administratif_selection' value='" . $row["ID_ADMINI"] . "'><i class='fa-solid fa-calendar' id='convocation'></i> Vous êtes convoqué par " . $row["PRENOM"] . " " . $row["NOM"] . " le " . $date_formattee . " à " . $heure_formattee . "</button></li>";
                        }
                    }

                    if ($row["TYPE_ADM"] == 'V') {
                        echo "<li " . (($row['VU_ELEVE'] != 1) ? "class='nonLu'" : "") . "><button class='buttonAccueil' name='administratif_selection' value='" . $row["ID_ADMINI"] . "'><i class='fa-solid fa-triangle-exclamation' id='avertissement'></i> Vous avez reçu un avertissement par " . $row["PRENOM"] . " " . $row["NOM"] . "</button></li>";
                    }
                }
            } else {
                echo "<li class='columnliVide'>Aucun évènement administratif à régulariser.</li>";
            }
            ?>
        </form>

    <?php
    }
    if ($_SESSION["role"] == 'professeur') {
        header("Location: emploiDuTemps.php");
    } ?>
    <?php
    if ($_SESSION['role'] == 'administrateur') {
        $stmtLastAdminEvents = $connexion->prepare("
    SELECT A.ID_ADMINI, A.TYPE_ADM, A.J_ADM, A.VU_ADMIN, G.NOM_GROUPE, C.NUM_CLASSE, S.NUM_SEM, PROF_ADMIN.NOM AS NOM_PROF, PROF_ADMIN.PRENOM AS PRENOM_PROF, ETUDIANT.NOM AS NOM_ETUDIANT, ETUDIANT.PRENOM AS PRENOM_ETUDIANT
    FROM ADMINISTRATIF A
    JOIN UTILISATEUR AS ETUDIANT ON ETUDIANT.ID_UTI = A.ID_ETUDIANT  
    JOIN ETUDIANT_PAR_GROUPE EPG ON ETUDIANT.ID_UTI = EPG.ID_ETUDIANT
    JOIN GROUPE G ON G.ID_GROUPE = EPG.ID_GROUPE
    JOIN CLASSE C ON C.ID_CLASSE = G.ID_CLASSE
    JOIN SEMESTRE S ON S.ID_SEM = C.ID_SEM
    JOIN UTILISATEUR AS PROF_ADMIN ON PROF_ADMIN.ID_UTI = A.ID_UTI_P_A
    WHERE A.VU_ADMIN IS NULL
    ORDER BY A.J_ADM DESC
    LIMIT 20;"); // Limite le résultat aux 20 derniers événements

        $stmtLastAdminEvents->execute();
        $lastAdminEvents = $stmtLastAdminEvents->fetchAll(PDO::FETCH_ASSOC);

        if ($lastAdminEvents) {
    ?>
            <div id="liste_elèves">
                <div class="barre_recherche_eleves">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <form action="" method="GET">
                        <input type="search" name="search" placeholder="Rechercher par nom, prénom, groupe ou type" id="searchInput">
                    </form>
                </div>

                <table class="liste_eleves">
                    <tr>
                        <th>Nom de l'élève</th>
                        <th>Prénom de l'élève</th>
                        <th>Groupe</th>
                        <th>Type administratif</th>
                        <th>Date</th>
                        <th>Semestre</th>
                    </tr>
            <?php
            foreach ($lastAdminEvents as $event) {
                echo '<tr class="student" data-id-administratif="' . $event['ID_ADMINI'] . '">';
                echo '<td>' . $event['NOM_ETUDIANT'] . '</td>';
                echo '<td>' . $event['PRENOM_ETUDIANT'] . '</td>';
                echo '<td> G' . $event['NUM_CLASSE'] .  $event['NOM_GROUPE'] . '</td>';
                if ($event['TYPE_ADM'] == 'A') {
                    echo '<td> Absence</td>';
                } else if ($event['TYPE_ADM'] == 'R') {
                    echo '<td>Retard</td>';
                } else if ($event['TYPE_ADM'] == 'C') {
                    echo '<td>Convocation</td>';
                } else if ($event['TYPE_ADM'] == 'V') {
                    echo '<td>Avertissement</td>';
                }
                // Formater la date JJ/MM/YYYY
                $dateFormatted = date('d/m/Y', strtotime($event['J_ADM']));
                echo '<td>' . $dateFormatted . '</td>';
                echo '<td>' . $event['NUM_SEM'] . '</td>';
                echo '</tr>';
            }
            echo '</table>';
            echo '</div>';
        } else {
            echo '<p>Aucun événement administratif récent.</p>';
        }
    }
            ?>





            <script src="scripts/main.js"></script>
</main>
<?php
include_once('footer.php');
?>