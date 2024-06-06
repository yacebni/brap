<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BRAP Éducation</title>
    <link rel="stylesheet" href="styles/style.css">
</head>

<?php
include_once('header.php');
?>
<main>
    <div class="connexion">
        <div class="form">
            <form class="login-form" method="post" action="traitement.php">
                <h2>Connexion</h2>

                <div class="id">
                    <label for="identifiant"></label>
                    <?php
                    if (isset($_SESSION['compte_inex']) && $_SESSION['compte_inex']) {
                        $shakeClass = 'shake-password';
                    } else {
                        $shakeClass = '';
                    } ?>
                    <input id="identifiant" name="identifiant" type="text" required autofocus placeholder="Identifiant" class="<?php echo $shakeClass; ?>">
                    <?php unset($_SESSION['compte_inex']) ?>
                </div>

                <div class="mdp">
                    <label for="mdp"></label>
                    <?php
                    if (isset($_SESSION['mauvais_mdp']) && $_SESSION['mauvais_mdp']) {
                        $shakeClass = 'shake-password';
                    } else {
                        $shakeClass = '';
                    } ?>
                    <input id="mdp" type="password" name="mdp" required placeholder="Mot de passe" class="<?php echo $shakeClass; ?>">
                    <?php unset($_SESSION['mauvais_mdp']) ?>
                </div>

                <button type="submit" value="connexion" name="Action">
                    Valider
                </button>
                <?php
                if (isset($_SESSION['mauvais_mdp']) && $_SESSION['mauvais_mdp']) { ?>
                    <div class="bottom-form">
                        <p>Mauvais mot de passe, réessayer</p>
                    </div><?php }
                        unset($_SESSION['mauvais_mdp']) ?>
                <?php
                if (isset($_SESSION['compte_inex']) && $_SESSION['compte_inex']) { ?>
                    <div class="bottom-form">
                        <p>Compte inexistant, réessayer</p>
                    </div><?php }
                        unset($_SESSION['compte_inex']) ?>
            </form>
        </div>
        <div class="infos">
            <h2>Informations</h2>
            <div class="infos-contenu">
                <h3>Voici les étapes pour se connecter :</h3>
                <ul>
                    <li>
                        Saisir votre identifiant qui vous a été transmis par votre service scolarité
                    </li>
                    <li>
                        Saisir votre mot de passe qui vous a été transmis par votre service scolarité
                    </li>
                    <li>
                        Cliquer sur le bouton valider
                    </li>
                </ul>
            </div>
        </div>
    </div>
</main>
<?php
include_once('footer.php');
?>