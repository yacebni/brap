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

    const studentsRows = document.querySelectorAll('.liste_eleves tbody tr');
    studentsRows.forEach(studentRow => {
        studentRow.addEventListener('click', () => {
            const idEtudiant = studentRow.dataset.idEtudiant;
            window.location.href = 'notes.php?id=' + idEtudiant;
            studentRow.classList.remove('ligneSelectionne');

        });

        studentRow.addEventListener('mouseover', () => {
            studentRow.classList.add('ligneSelectionne');
        });

        studentRow.addEventListener('mouseout', () => {
            studentRow.classList.remove('ligneSelectionne');
        });
    });
});
