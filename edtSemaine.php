<?php
    include_once('configDB.php');
    
    function convertirFormatDate($date){
        $tabDate = explode("-", $date);
        $jours = $tabDate[2];
        $mois = $tabDate[1];
        $annees = $tabDate[0];
        $dateNouveauFormat = $annees."/".$mois."/".$jours;
        return $dateNouveauFormat;
    }

    function convertirFormatDateInverse($date){
        $tabDate = explode("-", $date);
        $jours = $tabDate[0];
        $mois = $tabDate[1];
        $annees = $tabDate[2];
        $dateNouveauFormat = $annees."/".$mois."/".$jours;
        return $dateNouveauFormat;
    }

    function jourDeLaSemaine($date){
        /*
        Renvoie : Mon, Tue, Wen, Thu, Fri, Sat, Sun
        */
        $nomJour = date('D',strtotime($date));
        return $nomJour;
    }

    function choisirCouleurTexte($couleur){
        /*
        Renvoie la couleur du texte idéale selon le fond
        */
        $rouge = hexdec($couleur[0] . $couleur[1]);
        $vert = hexdec($couleur[2] . $couleur[3]);
        $bleu = hexdec($couleur[4] . $couleur[5]);

        $luminance = 0.2126 * $rouge + 0.7152 * $vert + 0.0722 * $bleu;
        $seuil = 0.179;

        if($luminance > $seuil){
            return "000000";
        }
        else{
            return "ffffff";
        }
    }

    $aujourdhui = date("d-m-Y");
    $anneeActuelle = date('Y',strtotime(convertirFormatDate($aujourdhui)));
    $semaineActuelle = date('W',strtotime(convertirFormatDate($aujourdhui)));
    if(isset($_POST["semaineSelectionnee"])){
        $semaineActuelle = date('W',strtotime(convertirFormatDate($_POST["semaineSelectionnee"])));
        $anneeActuelle = date('Y',strtotime(convertirFormatDate($_POST["semaineSelectionnee"])));
    }
?>

<span class="ligneJours"></span>
<h2 style="grid-column-start: 2; grid-column-end: 3;" class="ligneJours">Lundi</h2>
<h2 style="grid-column-start: 3; grid-column-end: 4;" class="ligneJours">Mardi</h2>
<h2 style="grid-column-start: 4; grid-column-end: 5;" class="ligneJours">Mercredi</h2>
<h2 style="grid-column-start: 5; grid-column-end: 6;" class="ligneJours">Jeudi</h2>
<h2 style="grid-column-start: 6; grid-column-end: 7;" class="ligneJours">Vendredi</h2>
<?php
// Partie affichage des horaires possibles sur la première colonne

$heures = 8;
$heureMax = 18;

$i = 2;
while ($heures <= $heureMax) {
    echo '<div style="
                    grid-column-start: 1;
                    grid-column-end: 1;
                    grid-row-start: ' . ($i * 4) . ';
                    grid-row-end: ' . ($i * 4) + 4 . ';
                " class="colonneHeures">' . $heures . ':00</div>';
    $heures = $heures + 1;
    $i++;
}

function recupererInfosCours($idCours){
    global $connexion;
    $requete = "SELECT MATIERE_RESSOURCE.NOM_MATIERE, COURS.J_COURS, COURS.H_D_COURS, COURS.H_F_COURS, CLASSE.NUM_CLASSE, SEMESTRE.NUM_SEM, GROUPE.NOM_GROUPE, SALLE.NOM_SALLE FROM COURS
                JOIN MATIERE_RESSOURCE ON COURS.ID_MATIERE = MATIERE_RESSOURCE.ID_MATIERE
                JOIN GROUPE_PAR_COURS ON COURS.ID_COURS = GROUPE_PAR_COURS.ID_COURS
                JOIN GROUPE ON GROUPE_PAR_COURS.ID_GROUPE = GROUPE.ID_GROUPE
                JOIN CLASSE ON GROUPE.ID_CLASSE = CLASSE.ID_CLASSE
                JOIN SEMESTRE ON CLASSE.ID_SEM = SEMESTRE.ID_SEM
                JOIN SALLE ON COURS.ID_SALLE = SALLE.ID_SALLE
                WHERE COURS.ID_COURS = ?
                ORDER BY CLASSE.ID_CLASSE";
    $requeteCours = $connexion->prepare($requete);
    $requeteCours->bindParam(1, $idCours);
    $requeteCours->execute();
    $nbLignes = $requeteCours->rowCount();
    $infosCours = $requeteCours->fetchAll();
    $classe = 'G'.$infosCours[0]["NUM_CLASSE"].'S'.$infosCours[0]["NUM_SEM"];
    $groupe = $infosCours[0]["NOM_GROUPE"];
    $tabHeuresDebut = explode(":", $infosCours[0]["H_D_COURS"]);
    $tabHeuresFin = explode(":", $infosCours[0]["H_F_COURS"]);
    $dic = [
        'Matière' => $infosCours[0]["NOM_MATIERE"],
        'Jour' => convertirFormatDate($infosCours[0]["J_COURS"]),
        'Debut' => $tabHeuresDebut[0].':'.$tabHeuresDebut[1],
        'Fin' => $tabHeuresFin[0].':'.$tabHeuresFin[1],
        'Classe' => $classe,
        'Groupe' => $groupe,
        'Salle' => $infosCours[0]["NOM_SALLE"]
    ];
    
    for($i = 1; $i < count($infosCours); $i++){
        $classeAAjouter = 'G'.$infosCours[$i]["NUM_CLASSE"].'S'.$infosCours[$i]["NUM_SEM"];
        $groupeAAjouter = $infosCours[$i]["NOM_GROUPE"];
        if($classeAAjouter != $classe){
            $classe = $classeAAjouter;
            $dic['Classe'] = $dic['Classe'].' + '.$classe;
        }
        if($groupeAAjouter != $groupe){
            $groupe = $groupeAAjouter;
            $dic['Groupe'] = $dic['Groupe'].' + '.$groupe;
        }
    }
    
    
    
    return $dic;
}

function placerEDT($idCours, $jour, $horaireDebut, $horaireFin, $contenu, $couleur){
    /*
    Jour : 'YYYY-MM-DD'
    Horaires  : 'hh:mm'
    */
    global $aujourdhui, $semaineActuelle, $anneeActuelle;

    $dicValeurs = recupererInfosCours($idCours);

    $semaine = date('W',strtotime($jour));
    if($semaine == $semaineActuelle && date('Y',strtotime($jour)) == $anneeActuelle){
        /* Placement dans la bonne colonne */
        // Récupération du jour
        $nomJour = jourDeLaSemaine($jour);
        switch($nomJour){
            case "Mon":
                $colonneJour = 2;
                break;
            case "Tue":
                $colonneJour = 3;
                break;
            case "Wed":
                $colonneJour = 4;
                break;
            case "Thu":
                $colonneJour = 5;
                break;
            case "Fri":
                $colonneJour = 6;
                break;
        }
        $colonneDepart = $colonneJour;
        $colonneArrivee = $colonneJour + 1;

        // Placement dans la bonne ligne
        // On sait que 8h : ligneDepart = 8 et que 8h15 : ligneDepart = 9 et que 9h : ligneDepart = 12
        $tabDepart = explode(":", $horaireDebut);
        $tabArrivee = explode(":", $horaireFin);
        $heureDepart = (int)$tabDepart[0];
        $minuteDepart = (int)$tabDepart[1];
        $heureArrivee = (int)$tabArrivee[0];
        $minuteArrivee = (int)$tabArrivee[1];

        $ligneDepart = 8 + ($heureDepart - 8) * 4 + ($minuteDepart / 15);
        $ligneArrivee = 8 + ($heureArrivee - 8) * 4 + ($minuteArrivee / 15);
        
        echo '<label style="
            grid-column-start: ' . $colonneDepart . ';
            grid-column-end: ' . $colonneArrivee . ';
            grid-row-start: ' . $ligneDepart . ';
            grid-row-end: ' . $ligneArrivee . ';
            background-color: #'. $couleur .';
            color: #'. choisirCouleurTexte($couleur) .';
        " class="cours">' . '<input type="radio" name="selectionCours" class="selectionCours" value="'. $idCours .'"><span class="coursselection"><span class="detailsCoursCache">'.json_encode($dicValeurs).'</span></span>' . $contenu . '</label>';
    }         
}

function creerCadrillage(){
    /*
    Permet de faire un cadrillage gris
    */
    for($i = 8; $i < 52; $i++){
        for($j = 2; $j < 7; $j++){
            echo '<div style="
                grid-column-start: ' . $j . ';
                grid-column-end: ' . $j . ';
                grid-row-start: ' . $i . ';
                grid-row-end: ' . $i . ';
            " class="trouQuartHeure"></div>';
        }
    }
    /*
    Faire un cadrillage noir par dessus le gris
    */
    for($i = 8; $i < 52; $i+=4){
        for($j = 2; $j < 7; $j++){
            echo '<div style="
                grid-column-start: ' . $j . ';
                grid-column-end: ' . $j . ';
                grid-row-start: ' . $i . ';
                grid-row-end: ' . ($i + 4) . ';
            " class="trouHeure"></div>';
        }
    }
}

creerCadrillage();

// Récupération de tous les cours associés à l'utilisateur
if($_SESSION["role"] == 'eleve'){
    $requete = "SELECT COURS.ID_COURS, MATIERE_RESSOURCE.NOM_MATIERE, PROFESSEUR.NOM, PROFESSEUR.PRENOM, COURS.J_COURS, COURS.H_D_COURS, COURS.H_F_COURS, SALLE.NOM_SALLE, MATIERE_RESSOURCE.COULEUR
        FROM COURS
        INNER JOIN UTILISATEUR AS PROFESSEUR ON PROFESSEUR.ID_UTI = COURS.ID_PROFESSEUR
        INNER JOIN SALLE ON SALLE.ID_SALLE = COURS.ID_SALLE
        INNER JOIN GROUPE_PAR_COURS ON COURS.ID_COURS = GROUPE_PAR_COURS.ID_COURS
        INNER JOIN GROUPE ON GROUPE_PAR_COURS.ID_GROUPE = GROUPE.ID_GROUPE
        INNER JOIN ETUDIANT_PAR_GROUPE ON GROUPE.ID_GROUPE = ETUDIANT_PAR_GROUPE.ID_GROUPE
        INNER JOIN UTILISATEUR AS ETUDIANT ON ETUDIANT.ID_UTI = ETUDIANT_PAR_GROUPE.ID_ETUDIANT
        INNER JOIN MATIERE_RESSOURCE ON COURS.ID_MATIERE = MATIERE_RESSOURCE.ID_MATIERE 
        WHERE ETUDIANT.ID_UTI = ?";
}
else if($_SESSION["role"] == 'professeur'){
    $requete = "SELECT COURS.ID_COURS, MATIERE_RESSOURCE.NOM_MATIERE, PROFESSEUR.NOM, PROFESSEUR.PRENOM, COURS.J_COURS, COURS.H_D_COURS, COURS.H_F_COURS, SALLE.NOM_SALLE, MATIERE_RESSOURCE.COULEUR
    FROM COURS
    INNER JOIN UTILISATEUR AS PROFESSEUR ON PROFESSEUR.ID_UTI = COURS.ID_PROFESSEUR
    INNER JOIN SALLE ON SALLE.ID_SALLE = COURS.ID_SALLE
    INNER JOIN MATIERE_RESSOURCE ON COURS.ID_MATIERE = MATIERE_RESSOURCE.ID_MATIERE
    WHERE PROFESSEUR.ID_UTI = ?";
}
else if($_SESSION["role"] == 'administrateur'){
    $idProfesseur = $_POST["idProfesseur"] ?? null; //si $_POST n'existe pas,on affecte null
    $idClasse = $_POST["idClasse"] ?? null;



    if($idProfesseur !== null) {
        $_SESSION['idProfesseur'] = $idProfesseur;
        $_SESSION['idClasse'] = null;
        $requete = "SELECT COURS.ID_COURS, MATIERE_RESSOURCE.NOM_MATIERE, PROFESSEUR.NOM, PROFESSEUR.PRENOM, COURS.J_COURS, COURS.H_D_COURS, COURS.H_F_COURS, SALLE.NOM_SALLE, MATIERE_RESSOURCE.COULEUR
    FROM COURS
    INNER JOIN UTILISATEUR AS PROFESSEUR ON PROFESSEUR.ID_UTI = COURS.ID_PROFESSEUR
    INNER JOIN SALLE ON SALLE.ID_SALLE = COURS.ID_SALLE
    INNER JOIN MATIERE_RESSOURCE ON COURS.ID_MATIERE = MATIERE_RESSOURCE.ID_MATIERE
    WHERE PROFESSEUR.ID_UTI = ?";

    } else if($idClasse !== null) {
        $_SESSION['idProfesseur'] = null;
        $_SESSION['idClasse'] = $idClasse;
        $requete = "SELECT COURS.ID_COURS, MATIERE_RESSOURCE.NOM_MATIERE, PROFESSEUR.NOM, PROFESSEUR.PRENOM, COURS.J_COURS, COURS.H_D_COURS, COURS.H_F_COURS, SALLE.NOM_SALLE, MATIERE_RESSOURCE.COULEUR
        FROM COURS
        INNER JOIN UTILISATEUR AS PROFESSEUR ON PROFESSEUR.ID_UTI = COURS.ID_PROFESSEUR
        INNER JOIN SALLE ON SALLE.ID_SALLE = COURS.ID_SALLE
        INNER JOIN GROUPE_PAR_COURS ON COURS.ID_COURS = GROUPE_PAR_COURS.ID_COURS
        INNER JOIN GROUPE ON GROUPE_PAR_COURS.ID_GROUPE = GROUPE.ID_GROUPE
        INNER JOIN MATIERE_RESSOURCE ON COURS.ID_MATIERE = MATIERE_RESSOURCE.ID_MATIERE 
        WHERE GROUPE.ID_GROUPE = ?";
    } else {
        $_SESSION['idProfesseur'] = null;
        $_SESSION['idClasse'] = null;
        $requete = "SELECT COURS.ID_COURS, MATIERE_RESSOURCE.NOM_MATIERE, PROFESSEUR.NOM, PROFESSEUR.PRENOM, COURS.J_COURS, COURS.H_D_COURS, COURS.H_F_COURS, SALLE.NOM_SALLE, MATIERE_RESSOURCE.COULEUR
        FROM COURS
        INNER JOIN UTILISATEUR AS PROFESSEUR ON PROFESSEUR.ID_UTI = COURS.ID_PROFESSEUR
        INNER JOIN SALLE ON SALLE.ID_SALLE = COURS.ID_SALLE
        INNER JOIN GROUPE_PAR_COURS ON COURS.ID_COURS = GROUPE_PAR_COURS.ID_COURS
        INNER JOIN GROUPE ON GROUPE_PAR_COURS.ID_GROUPE = GROUPE.ID_GROUPE
        INNER JOIN MATIERE_RESSOURCE ON COURS.ID_MATIERE = MATIERE_RESSOURCE.ID_MATIERE 
        WHERE GROUPE.ID_GROUPE = 0";
    }

}
$requeteEdt = $connexion->prepare($requete); // TODO : Sélectionner uniquement la semaine souhaitée
if($_SESSION['role'] == 'professeur' || $_SESSION['role'] == 'eleve') {
    $requeteEdt->bindParam(1, $_SESSION['usercourant']);
} elseif(($_SESSION['role'] == 'administrateur') && ($_SESSION['idProfesseur'] != null )) {
    $requeteEdt->bindParam(1, $_SESSION['idProfesseur']);
} elseif(($_SESSION['role'] == 'administrateur') && ($_SESSION['idClasse'] != null )) {
    $requeteEdt->bindParam(1, $_SESSION['idClasse']);
}
$requeteEdt->execute();

foreach($requeteEdt as $cours){
    $contenu = "<b>" . $cours["NOM_MATIERE"] . "</b><p>" . $cours["NOM"] . " ". $cours["PRENOM"] . "</p><p>" . $cours["NOM_SALLE"] . "</p>";
    placerEDT($cours["ID_COURS"], $cours["J_COURS"], $cours["H_D_COURS"], $cours["H_F_COURS"], $contenu, $cours["COULEUR"]);
}

?>