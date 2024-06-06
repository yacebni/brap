<?php
include_once('configDB.php');
// Vérifier si le bouton déconnexion a été cliqué
if (isset($_GET['finSession'])) {
    session_destroy();
    header("Location: connexion.php");
    exit(0);
}

//Récupérer nom/prenom de l'utilisateur
$stmt = $connexion->prepare("SELECT PRENOM, NOM FROM UTILISATEUR WHERE IDENTIFIANT=?");
$stmt->bindParam(1, $_SESSION['identifiant']);
$stmt->execute();
$data = $stmt->fetch();
?>

<head>
    <link rel="icon" href="img/BRAP_WHITE.png" type="image/x-icon">
</head>

<body>
    <header>
        <nav class="menu">
            <label class="menu-icon">&#9776;</label>
            <ul id="listeNav">
                <?php
                if (isset($_SESSION['role'])) {
                    if ($_SESSION['role'] == "eleve") {
                        echo '<li class="invisible"><a href="index.php">Accueil</a></li>';
                        echo '<li class="invisible"><a href="notes.php">Notes</a></li>';
                        echo '<li class="invisible"><a href="emploiDuTemps.php">Emploi du temps</a></li>';
                        //echo '<li class="invisible"><a href="dossiersPartages.php">Dossiers Partagés</a></li>';
                        echo '<li class="invisible"><a href="administratif.php">Administratif</a></li>';
                    } else if ($_SESSION['role'] == "professeur") {
                        echo '<li class="invisible"><a href="emploiDuTemps.php">Emploi du temps</a></li>';
                        echo '<li class="invisible"><a href="listeEvalsProf.php">Évaluations</a></li>';
                        //echo '<li class="invisible"><a href="dossiersPartages.php">Dossiers Partagés</a></li>';
                        echo '<li class="invisible"><a href="listeEleves.php">Liste des élèves</a></li>';
                    } else if ($_SESSION['role'] == "administrateur") {
                        echo '<li class="invisible"><a href="index.php">Accueil</a></li>';
                        echo '<li class="invisible"><a href="emploiDuTemps.php">Emploi du temps</a></li>';
                        echo '<li class="invisible"><a href="listeEvalsAdmin.php">Évaluations</a></li>';
                        //echo '<li class="invisible"><a href="dossiersPartages.php">Dossiers Partagés</a></li>';
                        echo '<li class="invisible"><a href="listeEleves.php">Liste des élèves</a></li>';
                    }
                }
                ?>
            </ul>
        </nav>
        <div class="titre-container">
            <h1 class="titrePage"></h1>
        </div>
        <div class="utilisateur-categorie">
            <form>
                <button class="logoutBouton" name="finSession" type="submit">Se déconnecter</button>
            </form>
            <div class="nomUtilisateur">
                <?php
                if (isset($data[0]) && isset($data[1])) {
                    echo $data[0] . " " . $data[1];
                } ?>
            </div>
            <ion-icon class="utilisateur" src="img/utilisateurLogo.svg"></ion-icon>
        </div>
    </header>
    <script src="scripts/header.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>