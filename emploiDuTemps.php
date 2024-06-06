<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Emploi du temps</title>
    <link rel="stylesheet" href="styles/style.css">
</head>
<?php
include_once('header.php');
?>
<main class="mainEDT">
    <?php

    if ($_SESSION['role'] == 'administrateur') {

        $requeteProfesseurs = "SELECT ID_UTI, NOM, PRENOM FROM UTILISATEUR WHERE STATUT = 'P' ORDER BY NOM, PRENOM";
        $stmtProfesseurs = $connexion->query($requeteProfesseurs);
        $professeurs = $stmtProfesseurs->fetchAll(PDO::FETCH_ASSOC);

        // Récupérer la liste des classes
        $requeteClasses = "SELECT DISTINCT (ID_GROUPE), NOM_GROUPE, C.ID_CLASSE, NUM_CLASSE, S.NUM_SEM
                                FROM GROUPE G
                                INNER JOIN CLASSE C ON C.ID_CLASSE = G.ID_CLASSE
                                INNER JOIN SEMESTRE S ON S.ID_SEM = C.ID_SEM
                                ORDER BY S.NUM_SEM, NUM_CLASSE" ;
        $stmtClasses = $connexion->query($requeteClasses);
        $classes = $stmtClasses->fetchAll(PDO::FETCH_ASSOC);
        ?>
        <div id="form-choix">
            <!-- Affichage des listes déroulantes pour les professeurs et les classes -->
            <label for="selectProfesseur">Choisir un professeur :</label>
            <select name="selectProfesseur" id="selectProfesseur">
                <option value="">Sélectionner un professeur</option>
                <?php foreach ($professeurs as $professeur) : ?>
                    <option value="<?= $professeur['ID_UTI'] ?>"><?= $professeur['NOM'] . ' ' . $professeur['PRENOM'] ?></option>
                <?php endforeach; ?>
            </select>

            <label for="selectClasse">Choisir un groupe :</label>
            <select name="selectClasse" id="selectClasse">
                <option value="">Sélectionner un groupe</option>
                <?php foreach ($classes as $classe) : ?>
                    <option value="<?= $classe['ID_GROUPE'] ?>"><?= 'G' . $classe['NUM_CLASSE'] . 'S' . $classe["NUM_SEM"] . $classe['NOM_GROUPE']?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <?php
    }
    ?>
    <div class="EDT">
        <div class="semaine-navigation<?php if($_SESSION["role"] == 'professeur'){echo ' navigation-professeur';}?>">
            <button onclick="changeSemaine(-1)" class="arrow">◄</button>
            <h2 id="currentSemaine"></h2>
            <button onclick="changeSemaine(1)" class="arrow">►</button>
        </div>
    </div>
    <form method="POST">
        <div class="edtSemaine">
        </div>
        <?php
        if($_SESSION["role"] == 'professeur'){
            if(isset($_POST["selectionCours"])){
                $_SESSION["id_cours"] = $_POST["selectionCours"];
                header("location: appel.php");
            }
        ?>
        <div class="infosCours">
            <table>
                <tr>
                    <th>Matière</th>
                    <td id="EDTMatiere"></td>
                </tr>
                <tr>
                    <th>Jour</th>
                    <td id="EDTJour"></td>
                </tr>
                <tr>
                    <th>Début</th>
                    <td id="EDTDebut"></td>
                </tr>
                <tr>
                    <th>Fin</th>
                    <td id="EDTFin"></td>
                </tr>
                <tr>
                    <th>Classe</th>
                    <td id="EDTClasse"></td>
                </tr>
                <tr>
                    <th>Groupe</th>
                    <td id="EDTGroupe"></td>
                </tr>
                <tr>
                    <th>Salle</th>
                    <td id="EDTSalle"></td>
                </tr>
                <tr>
                    <th colspan="2" class="zoneBoutonActionEDT"><button>Faire l'appel</button></th>
                </tr>
            </table>
        </div>
        <?php
        }
        ?>


    </form>
    <script src="scripts/emploiDuTemps.js"></script>
</main>
<?php
include_once('footer.php');
?>