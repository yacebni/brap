/*return moyenne pour un 'ID_EXAMEN'*/
DELIMITER //
CREATE FUNCTION Moyenne(exam_id INT) RETURNS DECIMAL(10,2)
BEGIN
    DECLARE total_notes INT;
    DECLARE moyenne DECIMAL(10,2);

    -- Calculer la somme des notes
    SELECT SUM(NOTE) INTO total_notes
    FROM NOTE
    WHERE ID_EXAMEN = exam_id;

    -- Calculer la moyenne
    IF total_notes IS NOT NULL THEN
        SET moyenne = total_notes / (SELECT COUNT(*) FROM NOTE WHERE ID_EXAMEN = exam_id);
    ELSE
        SET moyenne = NULL;
    END IF;

    RETURN moyenne;
END // DELIMITER ;

/*Exemple utilisation
SELECT Moyenne(ID_EXAMEN) AS MoyenneNotes;
*/



/*return médianne pour un 'ID_EXAMEN'*/
DELIMITER //
CREATE FUNCTION Mediane(exam_id INT) RETURNS DECIMAL(10,2)
BEGIN
    DECLARE mid INT;
    DECLARE mediane DECIMAL(10,2);

    -- Obtenir le nombre de notes pour l'examen donné et calculer la position médiane
    SELECT CEIL(COUNT(*) / 2) INTO mid
    FROM NOTE
    WHERE ID_EXAMEN = exam_id;

    -- Récupérer la note médiane pour l'examen donné
    SELECT AVG(NOTE) INTO mediane
    FROM (
        SELECT NOTE, RANK() OVER (ORDER BY NOTE ASC) AS note_rank
        FROM NOTE
        WHERE ID_EXAMEN = exam_id
    ) AS ranked_notes
    WHERE note_rank = mid OR note_rank = mid + 1;

    RETURN mediane;
END // DELIMITER ;

/*Exemple utilisation
SELECT Mediane(ID_EXAMEN) AS MedianeNotes;
*/



/*return rang pour un 'ID_ETUDIANT' dans un 'ID_EXAMEN'*/
DELIMITER //

CREATE FUNCTION ObtenirClassementEtudiant(p_ID_ETUDIANT INT, p_ID_EXAMEN INT) RETURNS VARCHAR(20)
BEGIN
    DECLARE v_classement VARCHAR(20);

    -- Utiliser une sous-requête pour calculer le classement pour l'examen spécifié
SELECT CONCAT(
               COUNT(*) + 1,
               '/',
               (SELECT COUNT(*) FROM NOTE n3 WHERE n3.ID_EXAMEN = p_ID_EXAMEN)
       ) INTO v_classement
FROM NOTE n1
WHERE NOTE > (SELECT NOTE FROM NOTE n2 WHERE n2.ID_ETUDIANT = p_ID_ETUDIANT AND n2.ID_EXAMEN = p_ID_EXAMEN)
  AND n1.ID_EXAMEN = p_ID_EXAMEN;

-- Retourner le classement
RETURN v_classement;
END //

DELIMITER ;


