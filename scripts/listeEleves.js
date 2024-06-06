document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    searchInput.addEventListener('input', function (e) {
        const value = e.target.value.toLowerCase().trim();
        const students = document.querySelectorAll('.student');

        students.forEach(function (student) {
            const nomStudent = student.querySelector('td:nth-child(1)').textContent.toLowerCase();
            const prenomStudent = student.querySelector('td:nth-child(2)').textContent.toLowerCase();
            const classStudent = student.querySelector('td:nth-child(3)').textContent.toLowerCase();

            const isVisible = nomStudent.includes(value) || prenomStudent.includes(value) || classStudent.includes(value);
            student.style.display = isVisible ? '' : 'none';
        });
    });

    const students = document.querySelectorAll('.liste_eleves tbody tr');
    const popup = document.getElementById('pop-up');
    const studentName = document.getElementById('studentName');
    const typeAdm = document.getElementById('typeAdm');
    const dateConvocation = document.getElementById('dateConvocation');
    const labelDateConvocation = document.getElementById('labelDateConvocation');
    const heureDebut = document.getElementById('heureDebut');
    const labelHeureDebut = document.getElementById('labelHeureDebut');
    const heureFin = document.getElementById('heureFin');
    const labelHeureFin = document.getElementById('labelHeureFin');
    const studentIdInput = document.getElementById('studentId'); // Ajout


    students.forEach(student => {
        student.addEventListener('click', function () {
            const nom = student.querySelector('td:nth-child(1)').innerText;
            const prenom = student.querySelector('td:nth-child(2)').innerText;

            studentName.textContent = nom + ' ' + prenom;
            const studentId = student.getAttribute('data-student-id');
            studentIdInput.value = studentId;
            popup.style.display = 'block';
        });

        student.addEventListener('mouseover', () => {
            student.classList.add('ligneSelectionne');
        });

        student.addEventListener('mouseout', () => {
            student.classList.remove('ligneSelectionne');
        });
    });

    typeAdm.addEventListener('change', function () {
        const selectedValue = typeAdm.value;
        const dateConvocation = document.getElementById('dateConvocation');
        const labelDateConvocation = document.getElementById('labelDateConvocation');
        const heureDebut = document.getElementById('heureDebut');
        const labelHeureDebut = document.getElementById('labelHeureDebut');
        const heureFin = document.getElementById('heureFin');
        const labelHeureFin = document.getElementById('labelHeureFin');

        // Vérifie la valeur sélectionnée et active ou désactive l'affichage des champs de date et d'heure en conséquence
        if (selectedValue === 'Convocation') {
            document.getElementById('commentaire-label').classList.remove('avertissement-commentaire');

            labelDateConvocation.style.visibility = 'visible';
            dateConvocation.style.visibility = 'visible';
            dateConvocation.style.position = 'unset';

            labelHeureDebut.style.visibility = 'visible';
            heureDebut.style.visibility = 'visible';
            heureDebut.style.position = 'unset';

            labelHeureFin.style.visibility = 'visible';
            heureFin.style.visibility = 'visible';
            heureFin.style.position = 'unset';

            // Rend les champs de date et d'heure obligatoires
            dateConvocation.setAttribute('required', 'true');
            heureDebut.setAttribute('required', 'true');
            heureFin.setAttribute('required', 'true');

        } else {
            document.getElementById('commentaire-label').classList.add('avertissement-commentaire');

            labelDateConvocation.style.visibility = 'hidden';
            dateConvocation.style.visibility = 'hidden';
            dateConvocation.style.position = 'absolute';

            labelHeureDebut.style.visibility = 'hidden';
            heureDebut.style.visibility = 'hidden';
            heureDebut.style.position = 'absolute';

            labelHeureFin.style.visibility = 'hidden';
            heureFin.style.visibility = 'hidden';
            heureFin.style.position = 'absolute';

            // Supprime l'attribut 'required' des champs de date et d'heure
            dateConvocation.removeAttribute('required');
            heureDebut.removeAttribute('required');
            heureFin.removeAttribute('required');

        }
    });


    const closePopup = document.querySelector('.fermer-pop-up');
    closePopup.addEventListener('click', function () {
        popup.style.display = 'none';
    });

    window.addEventListener('click', function (event) {
        if (event.target === popup) {
            popup.style.display = 'none';
        }
    });

    const popupForm = document.getElementById('popupForm');
    popupForm.addEventListener('submit', function (event) {
        //event.preventDefault();
        popup.style.display = 'none';
        alert('Formulaire soumis');

    });
});
