let selection = document.querySelector(".select-semestre");
let elements = selection.getElementsByTagName("h3");
let boutons = Array.from(elements);

boutons.forEach((bouton, index) => {
  bouton.addEventListener("click", () => {
    envoyerRequete(index + 1);
  });
});

function envoyerRequete(parametre) {
  const xhr = new XMLHttpRequest();
  const url = "./notesSemestre.php";
  const params = "param=" + parametre;

  xhr.open("POST", url, true);
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

  xhr.onreadystatechange = function () {
    if (xhr.readyState === XMLHttpRequest.DONE) {
      if (xhr.status === 200) {
        document.getElementsByClassName("notes-container")[0].innerHTML =
          xhr.responseText; // Modifier l'emploi du temps à partir de la réponse PHP
        initListenerPuce();
      } else {
        console.error("Une erreur s'est produite lors de la requête AJAX.");
      }
    }
  };
  xhr.send(params);
}

function initListenerPuce() {
  let puceDeroulante = document.querySelectorAll(".competences");

  puceDeroulante.forEach(function (div) {
    div.addEventListener("click", function (ev) {
      let targetElement = ev.target;

      // Vérifier si l'élément cliqué est la flèche ou la matière
      let button = targetElement.classList.contains("fleche")
        ? targetElement
        : targetElement.querySelector(".fleche");

      // Si la cible n'est pas la flèche ou la matière, rechercher le parent avec la classe "competences"
      if (!button) {
        button = targetElement.closest(".competences").querySelector(".fleche");
      }

      // Rechercher le parent "competences" pour le div
      let contenu = targetElement
        .closest(".competences")
        .querySelector(".contenu-competence");

      // Toggle des classes
      contenu.classList.toggle("active");
      button.classList.toggle("droite");
    });
  });

  // Ajouter un gestionnaire d'événements pour les matières
  let matiereElements = document.querySelectorAll(".matiere");
  matiereElements.forEach(function (matiere) {
    matiere.addEventListener("click", function (ev) {
      // Empêcher la propagation de l'événement pour éviter d'activer le gestionnaire d'événements "competences"
      ev.stopPropagation();

      let button = matiere.querySelector(".fleche");
      let contenuMatiere = matiere.querySelector(".contenu-matiere");

      // Toggle des classes
      contenuMatiere.classList.toggle("active");
      button.classList.toggle("droite");
    });
  });
}

function main() {
  envoyerRequete(1);
}

main();
