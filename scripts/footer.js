function supprLiens() {
    let liens = document.querySelector(".liens");
    // Vérifier si la page courante est la page de connexion pour masquer les éléments
    if (document.title == "BRAP Éducation") {
        liens.remove();
    }
}

function main() {
    supprLiens();
}
main();