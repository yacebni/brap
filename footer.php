<footer class="footer">
    <div class="container">
        <div class="footer-colones">
            <div class="footer-colonne liens">
                <h4>Liens Utiles</h4>
                <ul>
                    <?php
                    if ($_SESSION['role'] == "eleve") {
                        echo '<li><a href="index.php">Accueil</a></li>';
                        echo '<li><a href="notes.php">Notes</a></li>';
                        echo '<li><a href="emploiDuTemps.php">Emploi du temps</a></li>';
                        //echo '<li><a href="dossiersPartages.php">Dossiers Partagés</a></li>';
                        echo '<li><a href="administratif.php">Administratif</a></li>';
                    } else if ($_SESSION['role'] == "professeur") {
                        echo '<li><a href="emploiDuTemps.php">Emploi du temps</a></li>';
                        echo '<li><a href="listeEvalsProf.php">Évaluations</a></li>';
                        //echo '<li><a href="dossiersPartages.php">Dossiers Partagés</a></li>';
                        echo '<li><a href="listeEleves.php">Liste des élèves</a></li>';
                    } elseif ($_SESSION['role'] == "administrateur") {
                        echo '<li><a href="index.php">Accueil</a></li>';
                        echo '<li><a href="emploiDuTemps.php">Emploi du temps</a></li>';
                        echo '<li><a href="listeEvalsAdmin.php">Évaluations</a></li=>';
                        //echo '<li><a href="dossiersPartages.php">Dossiers Partagés</a></li=>';
                        echo '<li><a href="listeEleves.php">Liste des élèves</a></li>';
                    }
                    ?>
                </ul>
            </div>
            <div class="footer-colonne contact">
                <h4>Coordonnées</h4>
                <p>43 bd du 11 Novembre 1918</p>
                <p>69100, Villeurbanne</p>
                <p>Téléphone: +33 4 72 43 44 80 00</p>
            </div>
            <div class="footer-colonne mentionsLegales">
                <h4>Informations Légales</h4>
                <p>&copy; 2023 Université Claude Bernard Lyon 1. Tous droits réservés.</p>
            </div>
            <div class="footer-colonne credits">
                <h4>Crédits et Mentions</h4>
                <p>Développé par la BRAP Team</p>
                <p>Membres : Loric Audin, Yacine Bouanani, Lucas Perez, Mylan Robinet</p>
            </div>
        </div>
    </div>
    <script src="scripts/footer.js"></script>
</footer>

</body>