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

<main>
    <div id="gerer_admin">
        <?php
        // Inclure le code de connexion à la base de données et initialiser la session

        // Vérifier si l'ID de l'administration est présent dans l'URL
        if (isset($_GET['id'])) {
            $id_administratif = $_GET['id'];

            // Récupérer les détails de l'administration en fonction de son ID
            $stmtAdminDetails = $connexion->prepare("
            SELECT ADMINISTRATIF.ID_ADMINI, ADMINISTRATIF.TYPE_ADM, ADMINISTRATIF.J_ADM, ADMINISTRATIF.H_D_ADM, ADMINISTRATIF.H_F_ADM, 
                   ADMINISTRATIF.STATUT_ADM, ADMINISTRATIF.REMARQUE, ADMINISTRATIF.CHEMIN, ADMINISTRATIF.VU_ELEVE, ADMINISTRATIF.VU_ADMIN, 
                   ETUDIANT.NOM AS NOM_ETUDIANT, ETUDIANT.PRENOM AS PRENOM_ETUDIANT, ETUDIANT.ID_UTI AS ID_ETUDIANT, 
                   PROFESSEUR.NOM AS NOM_PROF, PROFESSEUR.PRENOM AS PRENOM_PROF, PROFESSEUR.ID_UTI AS ID_PROFESSEUR
            FROM ADMINISTRATIF
            JOIN UTILISATEUR AS ETUDIANT ON ETUDIANT.ID_UTI = ADMINISTRATIF.ID_ETUDIANT
            JOIN UTILISATEUR AS PROFESSEUR ON PROFESSEUR.ID_UTI = ADMINISTRATIF.ID_UTI_P_A
            WHERE ADMINISTRATIF.ID_ADMINI = ?;");
            $stmtAdminDetails->bindParam(1, $id_administratif);
            $stmtAdminDetails->execute();
            $adminDetails = $stmtAdminDetails->fetch(PDO::FETCH_ASSOC);

            if ($adminDetails) {
                echo '<h2 id="detail">Détails de l\'administration</h2>';
                echo '<div id="contenu">';
                echo '<div id="infos-eleve">';
                echo '<h3>Informations élève</h3>';
                echo '<p><strong>Nom :</strong> ' . $adminDetails['NOM_ETUDIANT'] . '</p>';
                echo '<p><strong>Prénom :</strong> ' . $adminDetails['PRENOM_ETUDIANT'] . '</p>';
                echo '</div>';

                // Gérer le type d'administratif spécifique (par exemple, une absence)
                if ($adminDetails['TYPE_ADM'] == 'A') {

                    echo '<div id="infos-absence">';
                    echo '<h3>Informations absence</h3>';

                    $dateAbsence = new DateTime($adminDetails['J_ADM']);
                    echo '<p><strong>Date de l\'absence :</strong> ' . $dateAbsence->format('d/m/Y') . '</p>';
                    echo '<p><strong>Heure de début :</strong> ' . $adminDetails['H_D_ADM'] . '</p>';
                    echo '<p><strong>Heure de fin :</strong> ' . $adminDetails['H_F_ADM'] . '</p>';
                    echo '<p><strong>Commentaire :</strong> ' . $adminDetails['REMARQUE'] . '</p>';


                    if ($adminDetails['STATUT_ADM'] == 'A') {
                        echo '<p><strong>Statut :</strong> En attente </p>';
                    }
                    if ($adminDetails['STATUT_ADM'] == 'J') {
                        echo '<p><strong>Statut :</strong> Justifiée </p>';
                    }
                    if ($adminDetails['STATUT_ADM'] == 'N') {
                        echo '<p><strong>Statut :</strong> Non justifiée </p>';
                    }

                    if ($adminDetails['STATUT_ADM'] === 'A' && isset($adminDetails['CHEMIN'])) {
                        echo '<p><strong>Justificatif :</strong> <a id="justif" href="' . $adminDetails['CHEMIN'] . '">Voir le justificatif</a></p>';
                        echo '<form id="form-just" method="POST" action="traitement.php">';
                        echo '<input type="hidden" name="id_administratif" value="' . $id_administratif . '">'; // Champ caché pour l'ID administratif

                        // Bouton pour valider le justificatif
                        echo '<button id="valider" type="submit" value="valider_justif" name="Action">Valider le justificatif </button>';

                        // Bouton pour refuser le justificatif
                        echo '<button id="refuser" type="submit" value="refuser_justif" name="Action">Refuser le justificatif </button>';
                        echo '</form>';
                    } else if ($adminDetails['STATUT_ADM'] === 'J') {
                        echo '<p><strong>Justificatif :</strong> Vous avez déjà validé le justificatif.</p>';
                    } else if ($adminDetails['STATUT_ADM'] === 'R') {
                        echo '<p><strong>Justificatif :</strong> Vous avez déjà refusé le justificatif.</p>';
                    }
                    echo '</div>';
                } elseif ($adminDetails['TYPE_ADM'] == 'R') {
                    echo '<div id="infos-retard">';
                    echo '<h3>Informations retard</h3>';

                    $dateRetard = new DateTime($adminDetails['J_ADM']);
                    echo '<p><strong>Date du retard :</strong> ' . $dateRetard->format('d/m/Y') . '</p>';
                    echo '<p><strong>Heure de début :</strong> ' . $adminDetails['H_D_ADM'] . '</p>';

                    // Calculer la durée du retard
                    $heureDebut = new DateTime($adminDetails['J_ADM'] . ' ' . $adminDetails['H_D_ADM']);
                    $heureFin = new DateTime($adminDetails['J_ADM'] . ' ' . $adminDetails['H_F_ADM']);
                    $duree = $heureDebut->diff($heureFin)->format('%H:%I');

                    echo '<p><strong>Durée du retard :</strong> ' . $duree . '</p>';
                    echo '<p><strong>Commentaire :</strong> ' . $adminDetails['REMARQUE'] . '</p>';

                    echo '</div>';
                } elseif ($adminDetails['TYPE_ADM'] == 'V') {
                    echo '<div id="infos-avertissement">';
                    echo '<h3>Informations avertissement</h3>';
                    echo '<p><strong>Professeur :</strong> ' . $adminDetails['NOM_PROF'] . ' ' . $adminDetails['PRENOM_PROF'] . '</p>';

                    $dateAvertissement = new DateTime($adminDetails['J_ADM']);
                    echo '<p><strong>Date de l\'avertissement :</strong> ' . $dateAvertissement->format('d/m/Y') . '</p>';
                    echo '<p><strong>Heure de l\'avertissement :</strong> ' . $adminDetails['H_D_ADM'] . '</p>';
                    echo '<p><strong>Commentaire :</strong> ' . $adminDetails['REMARQUE'] . '</p>';
                    echo '</div>';
                } elseif ($adminDetails['TYPE_ADM'] == 'C') {
                    echo '<div id="infos-convocation">';
                    echo '<h3>Informations convocation</h3>';
                    echo '<p><strong>Professeur :</strong> ' . $adminDetails['NOM_PROF'] . ' ' . $adminDetails['PRENOM_PROF'] . '</p>';
                    $dateConvocation = new DateTime($adminDetails['J_ADM']);
                    echo '<p><strong>Date de la convocation :</strong> ' . $dateConvocation->format('d/m/Y') . '</p>';
                    echo '<p><strong>Heure de la convocation :</strong> ' . $adminDetails['H_D_ADM'] . '</p>';
                    echo '<p><strong>Commentaire :</strong> ' . $adminDetails['REMARQUE'] . '</p>';
                    echo '</div>';
                }


                echo '<form id="form-suppr" method="POST" action="traitement.php">';
                echo '<h3>Supprimer l\'administratif</h3>';
                echo '<input type="hidden" name="id_administratif" value="' . $id_administratif . '">'; // Champ caché pour l'ID administratif
                echo '<button id="suppr" type="submit" value="supprimer_pour_moi" name="Action">Supprimer pour moi </button>';
                echo '<button id="suppr" type="submit" value="supprimer_pour_tous" name="Action">Supprimer pour tous </button>';
                echo '</form>';

                echo '</div>';

            } else {
                echo '<p>Aucun détail trouvé pour cette administration.</p>';
            }
        } else {
            echo '<p>Aucun ID administratif spécifié.</p>';
        }
        ?>
    </div>
</main>

<?php
include_once('footer.php');
?>
