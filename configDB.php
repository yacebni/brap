<?php
    session_start();
    /* On redirige automatiquement les utilisateurs non connecté dans la page connexion.php 1 seule fois (on ne le redirige pas s'il est déjà dans la page conexion.php ou s'il est dans traitement.php) */
    if((!isset($_SESSION['connecter']) || $_SESSION['connecter'] == false) && !strpos($_SERVER['REQUEST_URI'], "connexion.php") && !strpos($_SERVER['REQUEST_URI'], "traitement.php")){
        header("Location: connexion.php");
        exit(0);
    }
    

    // Connexion
    $chemin='localhost';
    $nom_base_de_donnee='bd_brap';
    $identifiant='root';
    $mot_de_passe='BRAPetudiant12*';
    $pdo="mysql:host=".$chemin.";dbname=".$nom_base_de_donnee;

    try{
        $connexion = new PDO($pdo, $identifiant, $mot_de_passe);
        $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch(PDOException $e){ // Pour que ceux qui ont un autre logiciel de Wamp Server puissent y accéder, on réessaye sans mot de passe.
        try{
            $mot_de_passe='root';
            $connexion = new PDO($pdo, $identifiant, $mot_de_passe);
            $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch(PDOException $e){
            try{
                $mot_de_passe='';
                $connexion = new PDO($pdo, $identifiant, $mot_de_passe);
                $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            catch(PDOException $e){
                die('Erreur PDO : ' . $e->getMessage());
            }
        }
    }
    catch(Exception $e){
        die('Erreur Générale : ' . $e->getMessage());
    }
?>