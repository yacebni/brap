<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Administratif</title>
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">
</head>
<?php
include_once('header.php');

?>

<main class="administratif-page">
    <?php
        if ($_SESSION['role'] === 'eleve') {

            // Partie marquer comme lu
            if(isset($_POST["MarquerCommeLu"])){
                $stmtRechercheAdmini = $connexion->query("
                    UPDATE ADMINISTRATIF
                    SET VU_ELEVE = 1
                    WHERE ID_ADMINI = ".$_POST["MarquerCommeLu"]." AND ID_ETUDIANT = ".$_SESSION['usercourant']."
                ");
                $stmtRechercheAdmini->execute();
            }

            $stmtAdmi = $connexion->prepare("
                    SELECT ADMINISTRATIF.ID_ADMINI, ADMINISTRATIF.TYPE_ADM, ADMINISTRATIF.J_ADM, 
                        PROF_ADMIN.NOM, PROF_ADMIN.PRENOM, ADMINISTRATIF.H_D_ADM, ADMINISTRATIF.REMARQUE,
                        ADMINISTRATIF.H_F_ADM, ADMINISTRATIF.STATUT_ADM, ADMINISTRATIF.VU_ELEVE
                    FROM ADMINISTRATIF
                    JOIN UTILISATEUR  AS ETUDIANT ON ETUDIANT.ID_UTI = ADMINISTRATIF.ID_ETUDIANT
                    JOIN UTILISATEUR AS PROF_ADMIN ON PROF_ADMIN.ID_UTI = ADMINISTRATIF.ID_UTI_P_A
                    WHERE ETUDIANT.ID_UTI = ?;");
            $stmtAdmi->bindParam(1, $_SESSION['usercourant']);
            $stmtAdmi->execute();
            $administratifEleve = $stmtAdmi->fetchAll(PDO::FETCH_ASSOC);

            ?>
            <div class="menus">
                <?php
                $categories = [
                    'R' => 'Retard',
                    'A' => 'Absence',
                    'C' => 'Convocation',
                    'V' => 'Avertissement'
                ];

                if (isset($administratifEleve)) {
                    foreach ($categories as $categorie => $nomCategorie) {
                        $evenements = array_filter($administratifEleve, function ($adm) use ($categorie) {
                            return $adm['TYPE_ADM'] === $categorie;
                        });

                        if (!empty($evenements)) {
                            echo '<div class="' . strtolower($nomCategorie) . '">';
                            echo '<h2>' . $nomCategorie . 's</h2>'; // Ajout du "s" pour les événements multiples
                            echo '<button class="fleche bas"></button>';
                            echo '<div class="contenu-' . strtolower($nomCategorie) . '">';

                            $dates = [];
                            foreach ($evenements as $evenement) {
                                $date = date('d/m/Y', strtotime($evenement['J_ADM']));
                                $dates[$date][] = $evenement;
                            }

                            foreach ($dates as $date => $infos) {
                                echo '<ul>';
                                echo '<li><h3>' . $date . '</h3></li>';

                                echo '<table>';
                                echo '<thead>';
                                echo '<tr>';
                                if ($categorie === 'R') {
                                    echo '<th>Heure retard</th>';
                                    echo '<th>Durée</th>';
                                    echo '<th>Professeur</th>';
                                    echo '<th>Commentaire</th>';
                                } elseif ($categorie === 'A') {
                                    echo '<th>Professeur</th>';
                                    echo '<th>Commentaire</th>';
                                    echo '<th>Statut justification</th>';
                                } elseif ($categorie === 'C') {
                                    echo '<th>Heure de début</th>';
                                    echo '<th>Heure de fin</th>';
                                    echo '<th>Professeur</th>';
                                    echo '<th>Commentaire</th>';
                                } elseif ($categorie === 'V') {
                                    echo '<th>Professeur</th>';
                                    echo '<th>Commentaire</th>';
                                }
                                echo '<th width="100px">Vu</th>';
                                echo '</tr>';
                                echo '</thead>';
                                echo '<tbody>';
                                foreach ($infos as $info) {
                                    echo '<tr id="admini_'.$info["ID_ADMINI"].'">';

                                    if ($categorie === 'R') {
                                        $heureDebut = new DateTime($info['H_D_ADM']);
                                        $heureFin = new DateTime($info['H_F_ADM']);
                                        $diff = $heureDebut->diff($heureFin);
                                        $dureeRetard = $diff->format('%H:%I');

                                        echo '<td>' . date('H:i', strtotime($info['H_D_ADM'])) . '</td>';
                                        echo '<td>' . $dureeRetard . '</td>';
                                        echo '<td>' . $info['PRENOM'] . ' ' . $info['NOM'] . '</td>';
                                        echo '<td>' . $info['REMARQUE'] . '</td>';
                                    } elseif ($categorie === 'A') {
                                        echo '<td>' . $info['PRENOM'] . ' ' . $info['NOM'] . '</td>';
                                        echo '<td>' . $info['REMARQUE'] . '</td>';
                                        $_SESSION['absence_id'] = $info['ID_ADMINI'];

                                        // Vérifier le statut pour afficher les différentes conditions et gérer l'affichage du formulaire
                                        if ($info['STATUT_ADM'] === 'N') {
                                            echo "<td><i class='fa-solid fa-xmark' id='justification'></i>Non justifiée</td>";
                                            echo '<form action="traitement.php" method="post" enctype="multipart/form-data" class="form-justifier-absence">';
                                            echo '<input type="file" name="justificatif" class="input-justificatif" accept="image/*, application/pdf" id="justificatifField" required>';
                                            echo '<button type="submit" value="justifier" class="submit-justificatif" name="Action">Envoyer</button>';
                                            echo '</form>';
                                            echo '</td>';
                                        } elseif ($info['STATUT_ADM'] === 'A') {
                                            echo "<td><i class='fa-solid fa-clock-rotate-left' id='justification'></i>En attente</td>";
                                            // Ne pas afficher le formulaire si le statut est 'En attente'
                                        } elseif ($info['STATUT_ADM'] === 'J') {
                                            echo "<td><i class='fa-solid fa-check' id='justification'></i>Justifiée</td>";
                                        } elseif ($info['STATUT_ADM'] === 'R') {
                                            echo "<td><i class='fa-solid fa-xmark' id='justification'></i>Refusée</td>";
                                        }
                                    }
                                    elseif ($categorie === 'C') {
                                        echo '<td>' . date('H:i', strtotime($info['H_D_ADM'])) . '</td>';
                                        echo '<td>' . date('H:i', strtotime($info['H_F_ADM'])) . '</td>';
                                        echo '<td>' . $info['PRENOM'] . ' ' . $info['NOM'] . '</td>';
                                        echo '<td>' . $info['REMARQUE'] . '</td>';
                                    } elseif ($categorie === 'V') {
                                        echo '<td>' . $info['PRENOM'] . ' ' . $info['NOM'] . '</td>';
                                        echo '<td>' . $info['REMARQUE'] . '</td>';
                                    }
                                    echo '<td class="'.(($info["VU_ELEVE"] == 1) ? 'zoneDejaLu' : 'zoneMarquerCommeLu').'"><form method="post"><button type="submit" name="MarquerCommeLu" value="'. $info["ID_ADMINI"] .'" '. (($info["VU_ELEVE"] == 1) ? 'disabled>Déjà lu</button></form></td>' : '>Marquer comme lu</button></form></td>');
                                    echo '</tr>';
                                }
                                echo '</tbody>';
                                echo '</table>';
                            }

                            echo '</div>';
                            echo '</div>';
                        } else {
                            echo '<div class="' . strtolower($nomCategorie) . '">';
                            echo '<h2>' . $nomCategorie . '</h2>';
                            echo '<button class="fleche bas"></button>';
                            echo '<div class="contenu-' . strtolower($nomCategorie) . '">';
                            echo '<div class="rien">';
                            echo '<h3>Pas ' . (($nomCategorie === 'Convocation' || $nomCategorie === 'Retard') ? "de ". strtolower($nomCategorie) : "d'".strtolower($nomCategorie)). '</h3>';
                            echo '<p>Il n\'y a pas ' . (($nomCategorie === 'Convocation' || $nomCategorie === 'Retard') ? "de ". strtolower($nomCategorie) : "d'".strtolower($nomCategorie)) . ' pour le moment.</p>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                        }
                    }
                }
                ?>
            </div>
        <?php
        }else{
            header('Location: emploiduTemps.php');
        }?>

    <script src="scripts/administratif.js"></script>
</main>

<?php

// Partie redirection
if(isset($_POST["administratif_selection"]) || isset($_POST["MarquerCommeLu"])){
    $idAdmini = $_POST["administratif_selection"] ?? $_POST["MarquerCommeLu"];
    
    $stmtRechercheAdmini = $connexion->query("
        SELECT TYPE_ADM FROM ADMINISTRATIF
        WHERE ID_ADMINI = ".$idAdmini." AND ID_ETUDIANT = ".$_SESSION['usercourant']."    
    ");
    $stmtRechercheAdmini->execute();
    $adminiSelectionne = $stmtRechercheAdmini->fetch();
?>
<script>
    var idAdmini = '#admini_<?php echo $idAdmini ?>';

    function rediriger(){
        window.location.href = idAdmini;
        selection = idAdmini + " td";
        let adminiSelectionne = document.querySelectorAll(selection);
        for(let i = 0; i < adminiSelectionne.length; i++){
            adminiSelectionne[i].classList.add("contenuRedirige");
        }
        setTimeout(() => {
            for(let i = 0; i < adminiSelectionne.length; i++){
                adminiSelectionne[i].classList.remove("contenuRedirige");
            }
        }, 3000);
    }

    <?php
    if(isset($adminiSelectionne["TYPE_ADM"])){
        switch($adminiSelectionne["TYPE_ADM"]){
            case "A": //Absence
                ?>
                let contenuAbsence = document.querySelector(".contenu-absence");
                setTimeout(() => {
                    contenuAbsence.classList.add("active");
                    let fleche = document.querySelector(".absence .fleche");
                    fleche.classList.add("droite");
                    rediriger();
                }, 200);
                <?php
                break;
            case "R": //Retard
                ?>
                let contenuRetard = document.querySelector(".contenu-retard");
                setTimeout(() => {
                    contenuRetard.classList.add("active");
                    let fleche = document.querySelector(".retard .fleche");
                    fleche.classList.add("droite");
                    rediriger();
                }, 200);
                <?php
                break;
            case "C": //Convocation
                ?>
                let contenuConvocation = document.querySelector(".contenu-convocation");
                setTimeout(() => {
                    contenuConvocation.classList.add("active");
                    let fleche = document.querySelector(".convocation .fleche");
                    fleche.classList.add("droite");
                    rediriger();
                }, 200);
                <?php
                break;
            case "V": //Avertissement
                ?>
                let contenuAvertissement = document.querySelector(".contenu-avertissement");
                setTimeout(() => {
                    contenuAvertissement.classList.add("active");
                    let fleche = document.querySelector(".avertissement .fleche");
                    fleche.classList.add("droite");
                    rediriger();
                }, 200);
                <?php
                break;
        }
    }
}
    ?>

</script>

<?php
include_once('footer.php');
?>