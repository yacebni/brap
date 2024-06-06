function validateForm() {
    var retards = document.getElementsByName('retard[]');
    var absences = document.getElementsByName('absence[]');

    for (var i = 0; i < retards.length; i++) {
        if (retards[i].checked && absences[i].checked) {
            alert('Vous ne pouvez pas sélectionner à la fois un retard et une absence pour un élève.');
            return false;
        }
    }
    return true;
}

function toggleSelection(index, type) {
    var retards = document.getElementsByName('retard[]');
    var absences = document.getElementsByName('absence[]');

    if (type === 'absence') {
        if (absences[index].checked) {
            retards[index].checked = false;
        }
    }else if (type === 'retard') {
        if (retards[index].checked) {
            absences[index].checked = false;
        }
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const formAppel = document.querySelector('.form-appel');

    formAppel.addEventListener('submit', function (event) {
        const confirmation = confirm("Êtes-vous sûr de vouloir valider l'appel ?");

        if (!confirmation) {
            event.preventDefault(); // Annuler l'événement de soumission du formulaire si l'utilisateur clique sur Annuler dans la boîte de dialogue de confirmation
        }
    });
});

