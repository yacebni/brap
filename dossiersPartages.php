<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Dossiers Partagés</title>
    <link rel="stylesheet" href="styles/style.css">
</head>
<?php
include_once('header.php');
?>
<main>
    <?php
        if($_SESSION['role'] == "administrateur"){
            $requeteDossiersPartages = "SELECT * FROM DOSSIER_P JOIN UTILISATEUR ON DOSSIER_P.ID_CREATEUR = UTILISATEUR.ID_UTI";
        }
        else if($_SESSION['role'] == "professeur"){
            //
        }
        else{
            //
        }
        $stmtDossiersPartages = $connexion->query($requeteDossiersPartages);
        $listeDossiersPartages = $stmtDossiersPartages->fetchAll(PDO::FETCH_ASSOC);
        foreach($listeDossiersPartages as $dossierPartage){
            ?><a href=""><?php echo $dossierPartage["NOM_D"];?> créé par <?php echo $dossierPartage["PRENOM"];?> <?php echo $dossierPartage["NOM"];?></a><?php
        }
    ?>
</main>
<?php
include_once('footer.php');
?>