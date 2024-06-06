function MenuNav() {
    let navBouton = document.querySelector(".menu-icon");
    let menu = document.getElementById("listeNav");
    let categories = menu.querySelectorAll("li");
    let titre = document.querySelector(".titrePage");
    //Adapter le texte du titre selon la page
    function setTitrePage() {
        let catNav = document.querySelector(".menu");
        let header = document.querySelector("header");
        let catUtilisateur = document.querySelector(".utilisateur-categorie");

        //Definir le titre par rapport au titre de la page
        titre.textContent = document.title;

        // Vérifier si la page courante est la page de connexion pour masquer les éléments
        if (document.title == "BRAP Éducation") {
            catNav.remove();
            catUtilisateur.remove();
            header.style.justifyContent = 'center';
        }
    }
    setTitrePage();

    //Afficher le menu aves les pages du site
    function afficherMenu() {
        categories.forEach(element => {
            element.classList.add("slide-in", "visible");
            element.classList.remove("invisible", "slide-out");
            titre.classList.add("invisible");
        });
    }

    //Masquer le menu aves les pages du site
    function masquerMenu() {
        categories.forEach(element => {
            element.classList.add("slide-out");
            setTimeout(() => {
                element.classList.remove("visible");
                element.classList.add("invisible");
                titre.classList.remove("invisible");
            }, 200);
            element.classList.remove("slide-in");
        });
    }

    //Listener du bouton "hamburger" qui affiche ou masque le menu
    let visible = false;
    navBouton.addEventListener("click", function () {
        if (visible) {
            masquerMenu();
            visible = false;
        } else {
            afficherMenu();
            visible = true;
        }
    });
}

function main() {
    MenuNav();
}

main();
