<?php
require_once('configDB.php');

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['Action'])) {
    switch ($_POST['Action']) {
        case 'connexion':
            $_SESSION['identifiant'] = $_POST['identifiant'];
            $stmt = $connexion->prepare("SELECT ID_UTI, MDP, MDP_HASHED FROM UTILISATEUR WHERE IDENTIFIANT=?");
            $stmt->bindParam(1, $_SESSION['identifiant']);
            $stmt->execute();
            $user = $stmt->fetch();

            if (!$user) {
                // L'utilisateur n'existe pas
                $_SESSION["compte_inex"] = true;
                header("Location: connexion.php");
                $shakeClass = 'shake-password';
                exit(); // Arrêter l'exécution pour éviter toute autre action non désirée
            }

            // Vérifier si le mot de passe fourni correspond au mot de passe (hashé ou non) dans la base de données
            if ($user['MDP_HASHED']) {
                // Si le mot de passe est déjà hashé, utilisez password_verify
                if (password_verify($_POST["mdp"], $user['MDP'])) {
                    // Mot de passe correct
                    $_SESSION['connecter'] = true;
                    $_SESSION["usercourant"] = $user["ID_UTI"];
                    // Redirection en fonction du rôle de l'utilisateur
                    $role = determineUserRole($connexion, $_SESSION['identifiant']);
                    if ($role === 'eleve') {
                        $_SESSION['role'] = 'eleve';
                        header("Location: index.php");
                    } elseif ($role === 'professeur') {
                        $_SESSION['role'] = 'professeur';
                        header("Location: emploiDuTemps.php");
                    } elseif ($role === 'administrateur') {
                        $_SESSION['role'] = 'administrateur';
                        header("Location: index.php");
                    } else {
                        header("Location: connexion.php");
                    }
                } else {
                    // Mot de passe incorrect
                    $_SESSION["mauvais_mdp"] = true;
                    $_SESSION['identifiant_valide'] = $_SESSION['identifiant'];
                    header("Location: connexion.php");
                }
            } else {
                // Si le mot de passe n'est pas hashé, hashé-le et mettez à jour la base de données
                $hashedPassword = password_hash($user['MDP'], PASSWORD_DEFAULT);
                $updateStmt = $connexion->prepare("UPDATE UTILISATEUR SET MDP = ?, MDP_HASHED = 1 WHERE ID_UTI = ?");
                $updateStmt->bindParam(1, $hashedPassword);
                $updateStmt->bindParam(2, $user['ID_UTI']);
                $updateStmt->execute();
                // Connectez l'utilisateur après la mise à jour du mot de passe
                $_SESSION['connecter'] = true;
                $_SESSION["usercourant"] = $user["ID_UTI"];
                // Redirection en fonction du rôle de l'utilisateur
                $role = determineUserRole($connexion, $_SESSION['identifiant']);
                if ($role === 'eleve') {
                    $_SESSION['role'] = 'eleve';
                    header("Location: index.php");
                } elseif ($role === 'professeur') {
                    $_SESSION['role'] = 'professeur';
                    header("Location: emploiDuTemps.php");
                } elseif ($role === 'administrateur') {
                    $_SESSION['role'] = 'administrateur';
                    header("Location: index.php");
                } else {
                    header("Location: connexion.php");
                }
            }
            break;



        case 'justifier':
            if (isset($_FILES["justificatif"]) && $_FILES["justificatif"]["error"] === UPLOAD_ERR_OK) {
                // Récupération des informations du fichier
                $file_name = $_FILES["justificatif"]["name"];
                $temp_file = $_FILES["justificatif"]["tmp_name"];

                // Vérification de l'existence de la session absence_id
                if (!empty($_SESSION['absence_id'])) {
                    $absence_id = $_SESSION['absence_id'];

                    // Obtention de l'ID de l'étudiant concerné par l'absence
                    $stmt = $connexion->prepare("SELECT ID_ETUDIANT FROM ADMINISTRATIF WHERE ID_ADMINI = ?");
                    $stmt->bindParam(1, $absence_id);
                    $stmt->execute();
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($row) {
                        $student_id = $row['ID_ETUDIANT'];

                        // Création du dossier de l'étudiant s'il n'existe pas
                        $student_folder = "justificatifs/" . $student_id . "/";
                        if (!is_dir($student_folder)) {
                            mkdir($student_folder, 0777, true);
                        }

                        // Déplacement du fichier téléchargé dans le dossier de l'étudiant avec le nom de l'absence
                        $absence_file = $student_folder . $absence_id . "." . pathinfo($file_name, PATHINFO_EXTENSION);
                        if (move_uploaded_file($temp_file, $absence_file)) {
                            // Mise à jour du chemin dans la base de données
                            $stmt = $connexion->prepare("UPDATE ADMINISTRATIF SET CHEMIN = ?, STATUT_ADM = 'A' WHERE ID_ADMINI = ?");
                            $stmt->bindParam(1, $absence_file);
                            $stmt->bindParam(2, $absence_id);
                            $stmt->execute();

                            if ($stmt->rowCount() > 0) {
                                header("Location: administratif.php");
                            } else {
                                header('Location: administratif.php');
                            }
                        } else {
                            echo "Une erreur s'est produite lors du déplacement du fichier vers le dossier de l'étudiant.";
                        }
                    } else {
                        echo "ID de l'étudiant non trouvé pour cette absence.";
                    }
                    if ($stmt->rowCount() > 0) {
                        if ($row['STATUT_ADM'] === 'J') {
                            // Suppression de l'absence une fois qu'elle est justifiée avec succès
                            $stmtDeleteAbsence = $connexion->prepare("DELETE FROM ADMINISTRATIF WHERE ID_ADMINI = ? AND STATUT_ADM = 'J'");
                            $stmtDeleteAbsence->bindParam(1, $_SESSION['absence_id']);
                            $stmtDeleteAbsence->execute();

                            // Redirection vers la page administrative
                            header("Location: administratif.php");
                        } else {
                            header("Location: administratif.php");
                        }
                    } else {
                        echo "La sauvegarde du fichier a échoué.";
                    }
                } else {
                    echo "ID d'absence non défini.";
                }
            } else {
                header("Location: administratif.php");
            }

            break;
        case 'appel':
            $stmtJ_D_Fcours = $connexion->prepare("SELECT J_COURS, H_D_COURS, H_F_COURS
                                                         FROM COURS 
                                                         WHERE ID_COURS = ?;");
            $stmtJ_D_Fcours->bindParam(1, $_SESSION['id_cours']);
            $stmtJ_D_Fcours->execute();
            $infoscours = $stmtJ_D_Fcours->fetchAll(PDO::FETCH_ASSOC);

            $jour_adm = $infoscours[0]['J_COURS'];
            $heure_debut_cours = $infoscours[0]['H_D_COURS'];

            foreach ($_POST['retard'] as $id_etudiant => $retard) {
                $absence = isset($_POST['absence'][$id_etudiant]) ? 1 : 0;

                if ($retard > 0) {
                    // Si l'élève est en retard, calculez l'heure de fin et insérez les valeurs pour un retard
                    $heure_retard = date('H:i:s', strtotime("+$retard minutes", strtotime($heure_debut_cours)));
                    $type_adm = 'R'; // Marquez comme retard
                    $remarque = 'Retard de ' . $retard . ' minutes';

                    // Préparez et exécutez votre requête SQL pour insérer les données dans la table ADMINISTRATIF
                    $stmt = $connexion->prepare("INSERT INTO ADMINISTRATIF (ID_ETUDIANT, ID_UTI_P_A, TYPE_ADM, J_ADM, H_D_ADM, H_F_ADM, REMARQUE) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bindParam(1, $id_etudiant);
                    $stmt->bindParam(2, $_SESSION["usercourant"]);
                    $stmt->bindParam(3, $type_adm);
                    $stmt->bindParam(4, $jour_adm);
                    $stmt->bindParam(5, $heure_debut_cours);
                    $stmt->bindParam(6, $heure_retard);
                    $stmt->bindParam(7, $remarque);
                } elseif ($absence === 1) {
                    // Si l'élève est absent, insérez les valeurs pour une absence
                    $heure_fin_cours = $infoscours[0]['H_F_COURS'];
                    $type_adm = 'A'; // Marquez comme absence

                    // Préparez et exécutez votre requête SQL pour insérer les données dans la table ADMINISTRATIF
                    $stmt = $connexion->prepare("INSERT INTO ADMINISTRATIF (ID_ETUDIANT, ID_UTI_P_A, TYPE_ADM, STATUT_ADM, J_ADM, H_D_ADM, H_F_ADM, REMARQUE) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bindParam(1, $id_etudiant);
                    $stmt->bindParam(2, $_SESSION["usercourant"]);
                    $stmt->bindParam(3, $type_adm);
                    $stmt->bindValue(4, 'N'); // Marquez comme absence non justifiée
                    $stmt->bindParam(5, $jour_adm);
                    $stmt->bindParam(6, $heure_debut_cours);
                    $stmt->bindParam(7, $heure_fin_cours);
                    $stmt->bindValue(8, 'Absence');
                }

                // Exécuter la requête uniquement si l'absence ou le retard est supérieur à zéro
                if ($retard > 0 || $absence === 1) {
                    $stmt->execute();
                }
            }

            // Redirection vers une page appropriée après avoir enregistré les données
            header("Location: emploiduTemps.php");
            break;

        case 'updateEval':

            $id_evaluation = $_POST['id_evaluation'];
            $date_eval = $_POST['date_eval'];
            //$nom_matiere = $_POST['nom_matiere'];
            $nom_exam = $_POST['nom_exam'];
            $coef_eval = $_POST['coef_eval'];
            $inputValue = $_POST['num_classe'];
            // Exclure le premier caractère de la valeur
            $num_classe = substr($inputValue, 1);
            //$num_semestre = $_POST['num_semestre'];
            $total_points = $_POST['total_points'];

            $stmtUpdate = $connexion->prepare("
            UPDATE EVALUATION 
            SET DATE_EVAL = ?, NOM_EXAM = ?, COEF_EVAL = ?, TOTAL_P = ?, ID_CLASSE = ?
            WHERE ID_EXAMEN = ?
            ");
            $stmtUpdate->execute([$date_eval, $nom_exam, $coef_eval, $total_points, $num_classe, $id_evaluation]);

            $redirectionPage = "modifNote.php?ID_EXAMEN=" . $id_evaluation;
            header("Location: $redirectionPage");
            exit();

            break;

        case 'updateNote':
            // Fonction pour obtenir l'ID de la note existante pour un étudiant donné dans un examen donné
            function getExistingNoteId($connexion, $idEleve, $idEvaluation)
            {
                $stmtExisting = $connexion->prepare("SELECT ID_NOTE FROM NOTE WHERE ID_ETUDIANT = ? AND ID_EXAMEN = ?");
                $stmtExisting->bindParam(1, $idEleve);
                $stmtExisting->bindParam(2, $idEvaluation);
                $stmtExisting->execute();
                $existingNote = $stmtExisting->fetch(PDO::FETCH_ASSOC);

                return $existingNote ? $existingNote['ID_NOTE'] : null;
            }

            if (isset($_POST['id_evaluation'], $_POST['note'], $_POST['commentaire'], $_POST['id_eleve'])) {
                $ids = $_POST['id_evaluation'];
                $notes = $_POST['note'];
                $commentaires = $_POST['commentaire'];
                $idEleves = $_POST['id_eleve'];

                try {
                    $stmt = $connexion->prepare("UPDATE NOTE SET NOTE = ?, COMMENTAIRE = ? WHERE ID_NOTE = ?");
                    $stmtInsert = $connexion->prepare("INSERT INTO NOTE (ID_ETUDIANT, ID_EXAMEN, NOTE, COMMENTAIRE) VALUES (?, ?, ?, ?)");

                    for ($i = 0; $i < count($ids); $i++) {
                        $note = $notes[$i];
                        $commentaire = $commentaires[$i];
                        $idEleve = $idEleves[$i];
                        $idEvaluation = $ids[$i];

                        // Vérifier si la note ou le commentaire est non vide avant d'effectuer l'insertion ou la mise à jour
                        if (!empty($note) || !empty($commentaire)) {
                            $existingNote = getExistingNoteId($connexion, $idEleve, $idEvaluation);

                            if ($existingNote !== null) {
                                // Mettre à jour la note existante
                                $stmt->bindParam(1, $note);
                                $stmt->bindParam(2, $commentaire);
                                $stmt->bindParam(3, $existingNote);
                                $stmt->execute();
                            } else {
                                // Insérer une nouvelle note
                                $stmtInsert->bindParam(1, $idEleve);
                                $stmtInsert->bindParam(2, $idEvaluation);
                                $stmtInsert->bindParam(3, $note);
                                $stmtInsert->bindParam(4, $commentaire);
                                $stmtInsert->execute();
                            }
                        }
                    }

                    $redirectionPage = "modifNote.php?ID_EXAMEN=" . $ids[0];
                    header("Location: $redirectionPage");
                    exit();
                } catch (PDOException $e) {
                    echo "Erreur lors de la mise à jour : " . $e->getMessage();
                }
            } else {
                echo "Données manquantes pour la mise à jour.";
            }

            break;

        case 'add_adm':
            // Assurez-vous de valider et d'assainir les données reçues avant de les utiliser dans votre requête SQL pour éviter les attaques par injection SQL
            $studentId = $_POST['studentId'];
            $teacherId =  $_SESSION["usercourant"];
            $typeAdm = $_POST['typeAdm'];
            $commentaire = $_POST['commentaire'];

            // Déterminez la valeur pour TYPE_ADM en fonction du type sélectionné
            $typeAdmValue = '';
            if ($typeAdm === 'Avertissement') {
                $typeAdmValue = 'V'; // 'V' pour Avertissement
                $dateConvocation = date("Y-m-d"); // Date actuelle pour l'avertissement
                $heureDebut = date("H:i:s"); // Heure actuelle pour l'avertissement
                $heureFin = date("H:i:s"); // Heure actuelle pour l'avertissement
            } elseif ($typeAdm === 'Convocation') {
                $typeAdmValue = 'C'; // 'C' pour Convocation
                $dateConvocation = $_POST['dateConvocation'];
                $heureDebut = $_POST['heureDebut'];
                $heureFin = $_POST['heureFin'];
            }

            // Connectez-vous à votre base de données (vous devez déjà avoir une connexion établie à la base de données)

            // Préparez et exécutez votre requête d'insertion dans la table ADMINISTRATIF
            $stmt = $connexion->prepare("INSERT INTO ADMINISTRATIF (ID_ETUDIANT, ID_UTI_P_A, REMARQUE, H_D_ADM, H_F_ADM, J_ADM, TYPE_ADM) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bindParam(1, $studentId);
            $stmt->bindParam(2, $teacherId);
            $stmt->bindParam(3, $commentaire);
            $stmt->bindParam(4, $heureDebut);
            $stmt->bindParam(5, $heureFin);
            $stmt->bindParam(6, $dateConvocation);
            $stmt->bindParam(7, $typeAdmValue);
            $stmt->execute();

            header('Location: listeEleves.php');

            break;

        case 'valider_justif':
            // Vérification de la soumission du formulaire pour valider le justificatif
            // Vérification de la présence de l'ID administratif dans la requête POST
            if (isset($_POST['id_administratif'])) {
                $id_administratif = $_POST['id_administratif'];

                var_dump($id_administratif);
                $stmt = $connexion->prepare("UPDATE ADMINISTRATIF SET STATUT_ADM = 'J' WHERE ID_ADMINI = ?");
                $stmt->bindParam(1, $id_administratif);
                $stmt->execute();

                // Redirection vers la page gérer_administratif ou une autre page
                header("Location: gerer_administratif.php?id=" . $id_administratif);
            } else {
                // Si l'ID administratif n'est pas défini dans la requête POST
                echo "ID administratif non défini.";
                // Gérer l'erreur ou rediriger vers une page d'erreur appropriée
            }
            break;

        case 'refuser_justif':
            if (isset($_POST['id_administratif'])) {
                $id_administratif = $_POST['id_administratif'];

                var_dump($id_administratif);
                $stmt = $connexion->prepare("UPDATE ADMINISTRATIF SET STATUT_ADM = 'R' WHERE ID_ADMINI = ?");
                $stmt->bindParam(1, $id_administratif);
                $stmt->execute();

                // Redirection vers la page gérer_administratif ou une autre page
                header("Location: gerer_administratif.php?id=" . $id_administratif);
            } else {
                // Si l'ID administratif n'est pas défini dans la requête POST
                echo "ID administratif non défini.";
            }
            break;

        case 'supprimer_pour_moi':
            if (isset($_POST['id_administratif'])) {
                $id_administratif = $_POST['id_administratif'];

                // Mettre à jour la colonne VU_ADMIN à 1
                $stmtUpdateVUAdmin = $connexion->prepare("UPDATE ADMINISTRATIF SET VU_ADMIN = 1 WHERE ID_ADMINI = ?");
                $stmtUpdateVUAdmin->bindParam(1, $id_administratif);
                $stmtUpdateVUAdmin->execute();

                // Rediriger ou effectuer d'autres actions si nécessaire
                header("Location: index.php");
                exit();
            }
            break;

        case 'supprimer_pour_tous':
            if (isset($_POST['id_administratif'])) {
                $id_administratif = $_POST['id_administratif'];

                // Mettre à jour la colonne VU_ADMIN à 1
                $stmtUpdateVUAdmin = $connexion->prepare("DELETE FROM ADMINISTRATIF WHERE ID_ADMINI = ?");
                $stmtUpdateVUAdmin->bindParam(1, $id_administratif);
                $stmtUpdateVUAdmin->execute();

                // Rediriger ou effectuer d'autres actions si nécessaire
                header("Location: index.php");
                exit();
            }
            break;

        case 'export_notes':
            if ($_SESSION['role'] == 'administrateur') {
                // Vérifier si l'ID de l'étudiant est présent dans l'URL
                if (isset($_SESSION['id_etudiant'])) {
                    $id_etudiant = $_SESSION['id_etudiant'];

                    // Requête pour récupérer les informations de l'étudiant
                    $stmtUserInfo = $connexion->prepare("SELECT NOM, PRENOM FROM UTILISATEUR WHERE ID_UTI = ?");
                    $stmtUserInfo->execute([$id_etudiant]);
                    $userInfo = $stmtUserInfo->fetch(PDO::FETCH_ASSOC);

                    // Requête pour récupérer les notes de l'étudiant avec le nom de la matière, les compétences et les coefficients
                    $stmtNotes = $connexion->prepare("
                                SELECT
                    C.NOM_COMP AS Competence,
                    MR.NOM_MATIERE AS Matiere,
                    S.NUM_SEM AS Semestre,
                    GROUP_CONCAT(N.NOTE SEPARATOR ', ') AS Notes,
                    MPC.COEF_COMP AS Coefficient
                        FROM NOTE N
                        JOIN EVALUATION E ON N.ID_EXAMEN = E.ID_EXAMEN
                        JOIN MATIERE_RESSOURCE MR ON E.ID_MATIERE = MR.ID_MATIERE
                        JOIN MATIERE_PAR_COMP MPC ON MR.ID_MATIERE = MPC.ID_MATIERE
                        JOIN COMPETENCE C ON MPC.ID_COMP = C.ID_COMP
                        JOIN SEMESTRE S ON C.ID_SEM = S.ID_SEM
                        WHERE N.ID_ETUDIANT = ?
                        GROUP BY C.ID_COMP, MR.ID_MATIERE, MPC.COEF_COMP
                    ");

                    $stmtNotes->execute([$id_etudiant]);
                    $notes = $stmtNotes->fetchAll(PDO::FETCH_ASSOC);

                    // Générer le contenu du fichier Excel au format HTML
                    $export = '<tr><td colspan="4">Relevé de notes de ' . $userInfo['NOM'] . ' ' . $userInfo['PRENOM'] . ' </td></tr>';

                    $export = '<table>';
                    $export .= '<tr><th>Competence</th><th>Matiere</th><th>Coefficients</th><th>Notes</th></tr>';

                    $lastCompetence = null;

                    foreach ($notes as $note) {
                        if ($lastCompetence !== $note['Competence']) {
                            $export .= '<tr><td colspan="5">' . $note['Competence'] . '</td></tr>';
                            $lastCompetence = $note['Competence'];
                        }
                        $export .= '<tr>';
                        $export .= '<td></td>';
                        $export .= '<td>' . $note['Matiere'] . '</td>';
                        $export .= '<td>' . $note['Coefficient'] . '</td>';
                        $export .= '<td>' . $note['Notes'] . '</td>';
                        $export .= '</tr>';
                    }

                    $export .= '</table>';


                    // Nom du fichier Excel
                    $excelFileName = 'notes_' . $userInfo['NOM'] . '_' . $userInfo['PRENOM'] . '.xls';
                    // En-têtes pour le téléchargement du fichier Excel
                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename="' . $excelFileName . '"');
                    header('Cache-Control: max-age=0');
                    // Sortie du contenu
                    echo $export;
                    exit;
                } else {
                    echo "ID étudiant non spécifié.";
                }
            } else {
                echo "Vous n'avez pas les autorisations nécessaires pour exporter les notes.";
            }
            break;
        default:
            echo "Action non reconnue.";
            break;
    }
} else {
    echo "Requête invalide.";
}

// Fonction pour déterminer le rôle de l'utilisateur en fonction des tables associées
function determineUserRole($connexion, $identifiant)
{
    // Récupérer le STATUT de l'utilisateur
    $stmt = $connexion->prepare("SELECT STATUT FROM UTILISATEUR WHERE IDENTIFIANT = ?");
    $stmt->bindParam(1, $identifiant);
    $stmt->execute();
    $result = $stmt->fetch();

    // Vérifier si un résultat est retourné
    if ($result) {
        $statut = $result['STATUT'];

        // Stocker le STATUT dans le tableau $roles
        $roles = array($statut);

        // Vérifier le STATUT pour déterminer le rôle
        if ($statut === 'E') {
            return 'eleve';
        } elseif ($statut === 'P') {
            return 'professeur';
        } elseif ($statut === 'A') {
            return 'administrateur';
        } else {
            return 'autre'; // Si l'utilisateur a un STATUT non reconnu
        }
    } else {
        return 'autre'; // Si aucun résultat n'est retourné (utilisateur non trouvé)
    }
}
