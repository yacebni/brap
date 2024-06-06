/*function main() {
    let puceDeroulante = document.querySelectorAll(".menus .fleche");
    puceDeroulante.forEach(function (button) {
        button.addEventListener('click', function () {
            let contenu = this.parentElement.querySelector(".contenu-retards");
            contenu.classList.toggle("active");
            button.classList.toggle("droite");
        });
    });
}

main();*/

function main() {
  let menus = document.querySelectorAll(".menus");
  menus.forEach(function (menu) {
    let fleches = menu.querySelectorAll(".fleche");
    fleches.forEach(function (fleche) {
      fleche.addEventListener("click", function () {
        let contenu = this.parentElement.querySelector("div[class^='contenu']");
        contenu.classList.toggle("active");
        fleche.classList.toggle("droite");
      });
    });
  });
}
main();
