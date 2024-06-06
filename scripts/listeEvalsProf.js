function montrerPopUp() {
  document.getElementById("pop-up").style.display = "block";
}

function fermerPopUp() {
  document.getElementById("pop-up").style.display = "none";
}

let btnAjout = document.getElementsByClassName("ajoutEval")[0];
btnAjout.addEventListener("click", () => {
  montrerPopUp();
});

const lignes = document.querySelectorAll(".listeEvalsProf-container tbody tr");
lignes.forEach((ligne) => {
  let idExam = ligne.dataset.idExam;

  ligne.addEventListener("click", () => {
    window.location.href = `modifNote.php?ID_EXAMEN=${idExam}`;
  });

  ligne.addEventListener("mouseover", () => {
    ligne.classList.add("ligneSelectionne");
  });

  ligne.addEventListener("mouseout", () => {
    ligne.classList.remove("ligneSelectionne");
  });
});
