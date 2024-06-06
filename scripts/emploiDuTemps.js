const taille_max_mobile = 1000;
// Affichage mobile
if(window.innerWidth <= taille_max_mobile){
    let edt = document.querySelector(".edtSemaine");
    if(edt != null){
        edt.classList.add("edtJour");
        edt.classList.remove("edtSemaine");
    }
    let navigationEDT = document.querySelector(".semaine-navigation");
    if(navigationEDT != null){
        navigationEDT.classList.add("date-navigation");
        navigationEDT.classList.remove("semaine-navigation");
        affichageAujourdhui = navigationEDT.querySelector("#currentSemaine");
        affichageAujourdhui.id = "currentDate";
        buttonsArrow = navigationEDT.querySelectorAll(".arrow");
        buttonsArrow[0].setAttribute("onclick", "changeDate(-1)");
        buttonsArrow[1].setAttribute("onclick", "changeDate(1)");
    }
}

function afficherDetailsCours(e){
    let dicValeurs = JSON.parse(e.target.parentNode.querySelector(".detailsCoursCache").textContent);
    
    let EDTMatiere = document.querySelector("#EDTMatiere");
    if(EDTMatiere != null){
        EDTMatiere.textContent = dicValeurs["Matière"];
    }
    let EDTJour = document.querySelector("#EDTJour");
    if(EDTJour != null){
        EDTJour.textContent = dicValeurs["Jour"];
    }
    let EDTDebut = document.querySelector("#EDTDebut");
    if(EDTDebut != null){
        EDTDebut.textContent = dicValeurs["Debut"];
    }
    let EDTFin = document.querySelector("#EDTFin");
    if(EDTFin != null){
        EDTFin.textContent = dicValeurs["Fin"];
    }
    let EDTClasse = document.querySelector("#EDTClasse");
    if(EDTClasse != null){
        EDTClasse.textContent = dicValeurs["Classe"];
    }
    let EDTGroupe = document.querySelector("#EDTGroupe");
    if(EDTGroupe != null){
        EDTGroupe.textContent = dicValeurs["Groupe"];
    }
    let EDTSalle = document.querySelector("#EDTSalle");
    if(EDTSalle != null){
        EDTSalle.textContent = dicValeurs["Salle"];
    }

    let boutonsEDT = document.querySelector(".zoneBoutonActionEDT button");
    if(boutonsEDT != null){
        boutonsEDT.disabled = false;
    }
    
}

function supprimerDetailsCours(){
    let EDTMatiere = document.querySelector("#EDTMatiere");
    if(EDTMatiere != null){
        EDTMatiere.textContent = "";
    }
    let EDTJour = document.querySelector("#EDTJour");
    if(EDTJour != null){
        EDTJour.textContent = "";
    }
    let EDTDebut = document.querySelector("#EDTDebut");
    if(EDTDebut != null){
        EDTDebut.textContent = "";
    }
    let EDTFin = document.querySelector("#EDTFin");
    if(EDTFin != null){
        EDTFin.textContent = "";
    }
    let EDTClasse = document.querySelector("#EDTClasse");
    if(EDTClasse != null){
        EDTClasse.textContent = "";
    }
    let EDTGroupe = document.querySelector("#EDTGroupe");
    if(EDTGroupe != null){
        EDTGroupe.textContent = "";
    }
    let EDTSalle = document.querySelector("#EDTSalle");
    if(EDTSalle != null){
        EDTSalle.textContent = "";
    }

    let boutonsEDT = document.querySelector(".zoneBoutonActionEDT button");
    if(boutonsEDT != null){
        boutonsEDT.disabled = true;
    }

    let selectionsEDT = document.querySelectorAll(".cours input");
    for(let i = 0; i < selectionsEDT.length; i++){
        selectionsEDT[i].checked = false;
    }
}

function verifierZoneCliquee(e){ // Pour désélectionner un cours
    if(e.target.className != "coursselection" && e.target.className != "selectionCours"){
        supprimerDetailsCours();
    }
}

$deselectionEDTSemaine = document.querySelector(".edtSemaine");
if($deselectionEDTSemaine != null){
    $deselectionEDTSemaine.addEventListener("click", verifierZoneCliquee);
}

$deselectionEDTJour = document.querySelector(".edtJour");
if($deselectionEDTJour != null){
    $deselectionEDTJour.addEventListener("click", verifierZoneCliquee);
}

function changeSemaine(n) {
    semaineSelectionnee = semaineSelectionnee + n;
    currentDate.setDate(currentDate.getDate() + n * 7);

    updateSemaine();
}

function recupererDebutEtFinSemaine(){
    let debut = new Date(currentDate);
    // On récupère le lundi
    if(debut.getDay() > 5){ // Si on est le samedi, on affiche la semaine suivante
        while(debut.getDay() != 1){
            debut.setDate(debut.getDate() + 1);
        }
    }
    else{
        while(debut.getDay() != 1){
            debut.setDate(debut.getDate() - 1);
        }
    }
    let fin = new Date(currentDate);
    while(fin.getDay() != 5){
        fin.setDate(fin.getDate() + 1);
    }

    const options = { weekday: 'long', day: 'numeric', month: 'numeric', year: 'numeric' };
    const dateDebut = debut.toLocaleDateString('fr-FR', options);
    const dateFin = fin.toLocaleDateString('fr-FR', options)
    const [day, month, year] = dateDebut.split('/'); // Sépare la date en jour, mois et année
    const [day2, month2, year2] = dateFin.split('/');
    const debutFormatte = `${day.padStart(2, '0')}/${month.padStart(2, '0')}/${year}`; // Format JJ/MM/AAAA
    const finFormatte = `${day2.padStart(2, '0')}/${month2.padStart(2, '0')}/${year2}`; // Format JJ/MM/AAAA
    return [debutFormatte, finFormatte];
}

function obtenirNumeroSemaine(date){
    var premierJour = new Date(date.getFullYear(), 0, 1);
    var nbJours = Math.floor((date - premierJour) / (24 * 60 * 60 * 1000));
    var numSemaine = Math.ceil((date.getDay() + 1 + nbJours) / 7);
    return numSemaine;
}

var semaineSelectionnee = null;
var jourSelectionne = null;
var selectedProf = null;
var selectedClasse = null;
var selProf = document.getElementById('selectProfesseur');
var selClasse = document.getElementById('selectClasse');

if (selProf != null) {
    selProf.addEventListener("change", function() {
        selectedProf = this.value; // Récupérer l'ID du professeur sélectionné
        document.getElementById('selectClasse').selectedIndex = 0;
        selectedClasse = null;

        if (selectedProf !== '') {
            if(document.querySelector('.edtSemaine') != null) {
                updateEdtSemaine();
            } else {
                updateEdtJour();
            }
        }
    });
}

if (selClasse != null) {
    selClasse.addEventListener("change", function() {
        selectedClasse = this.value;
        document.getElementById('selectProfesseur').selectedIndex = 0;
        selectedProf = null;

        if (selectedClasse !== '') {
            if(document.querySelector('.edtSemaine') != null) {
                updateEdtSemaine();
            } else {
                updateEdtJour();
            }
        }
    });
}

function updateSemaine() {
    intervalleDate = recupererDebutEtFinSemaine();
    const options = { weekday: 'long', day: 'numeric', month: 'numeric', year: 'numeric' };
    const options2 = { day: 'numeric', month: 'numeric', year: 'numeric' };
    const date = currentDate.toLocaleDateString('fr-FR', options);
    const date2 = currentDate.toLocaleDateString('fr-FR', options2)
    const [day, month, year] = date.split('/'); // Sépare la date en jour, mois et année
    const [day2, month2, year2] = date2.split('-');
    const formattedDate = `${day.padStart(2, '0')}/${month.padStart(2, '0')}/${year}`; // Format JJ/MM/AAAA
    jourSelectionne = date2.split('/').reverse().join('-');
    document.getElementById('currentSemaine').textContent = "Semaine du " + intervalleDate[0] + " au " + intervalleDate[1];
    updateEdtSemaine();
}

function updateEdtSemaine() {
    const xhr = new XMLHttpRequest();
    const url = './edtSemaine.php';
    params = 'semaineSelectionnee=' + encodeURIComponent(jourSelectionne);

    if(selectedProf != null) {
        selectedClasse = null;
        params2 = '&idProfesseur=' + selectedProf;
        params += params2;
    } else if (selectedClasse != null) {
        selectedProf = null;
        params2 = '&idClasse=' + selectedClasse;
        params += params2;
    }
    xhr.open('POST', url, true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                edtSemaine.innerHTML = xhr.responseText; // Modifier l'emploi du temps à partir de la réponse PHP
                supprimerDetailsCours()
                // Ajouter un listener pour chaque cours
                let listeCours = document.querySelectorAll(".selectionCours");
                for(let i = 0; i < listeCours.length; i++){
                    listeCours[i].addEventListener("click", afficherDetailsCours);
                }
            } else {
                console.error('Une erreur s\'est produite lors de la requête AJAX.');
            }
        }
    };
    xhr.send(params);
}

let currentDate = new Date();
// On va faire sauter les samedis
if(currentDate.getDay() == 6){
    currentDate.setDate(currentDate.getDate() + 1);
}
// On va faire sauter également les dimanches
if(currentDate.getDay() == 7 || currentDate.getDay() == 0){
    currentDate.setDate(currentDate.getDate() + 1);
}

let edtSemaine = document.querySelector(".edtSemaine");

if(edtSemaine != null){
    updateSemaine();
}

function changeDate(days) {
    currentDate.setDate(currentDate.getDate() + days);
    // On va faire sauter les samedis et les dimanches
    if(currentDate.getDay() > 5 || currentDate.getDay() == 0){
        currentDate.setDate(currentDate.getDate() + (days * 2));
    }
    updateDate();
}
function updateDate() {
    const options = { weekday: 'long', day: 'numeric', month: 'numeric', year: 'numeric' };
    const options2 = { day: 'numeric', month: 'numeric', year: 'numeric' };
    const date = currentDate.toLocaleDateString('fr-FR', options);
    const date2 = currentDate.toLocaleDateString('fr-FR', options2)
    const [day, month, year] = date.split('/'); // Sépare la date en jour, mois et année
    const [day2, month2, year2] = date2.split('-');
    const formattedDate = `${day.padStart(2, '0')}/${month.padStart(2, '0')}/${year}`; // Format JJ/MM/AAAA
    jourSelectionne = date2.split('/').reverse().join('-');
    document.getElementById('currentDate').textContent = formattedDate;
    updateEdtJour();
}


function updateEdtJour() {
    const xhr = new XMLHttpRequest();
    const url = './edtJour.php';
    params = 'formattedDate=' + encodeURIComponent(jourSelectionne);

    if(selectedProf != null) {
        selectedClasse = null;
        params2 = '&idProfesseur=' + selectedProf;
        params += params2;
    } else if (selectedClasse != null) {
        selectedProf = null;
        params2 = '&idClasse=' + selectedClasse;
        params += params2;
    }

    xhr.open('POST', url, true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                edtJour.innerHTML = xhr.responseText; // Réponse du serveur
                supprimerDetailsCours()
                // Ajouter un listener pour chaque cours
                let listeCours = document.querySelectorAll(".selectionCours");
                for(let i = 0; i < listeCours.length; i++){
                    listeCours[i].addEventListener("click", afficherDetailsCours);
                }
            } else {
                console.error('Une erreur s\'est produite lors de la requête AJAX.');
            }
        }
    };

    xhr.send(params);
}
function sendDataToPHP(formattedDate) {
    const xhr = new XMLHttpRequest();
    const url = './edtJour.php';
    const params = 'formattedDate=' + encodeURIComponent(formattedDate);

    xhr.open('POST', url, true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                edtJour.innerHTML = xhr.responseText; // Réponse du serveur
                supprimerDetailsCours()
                // Ajouter un listener pour chaque cours
                let listeCours = document.querySelectorAll(".selectionCours");
                for(let i = 0; i < listeCours.length; i++){
                    listeCours[i].addEventListener("click", afficherDetailsCours);
                }
            } else {
                console.error('Une erreur s\'est produite lors de la requête AJAX.');
            }
        }
    };

    xhr.send(params);
}

let edtJour = document.querySelector(".edtJour");
if(edtJour != null){
    updateDate();
}