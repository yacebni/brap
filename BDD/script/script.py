import re, os, requests, mysql.connector
from datetime import datetime, timedelta

# Récupérer la date courante
date_time = datetime.now()

# Calculer le premier (lundi) et le dernier jour de la semaine (dimanche)
premier_jour_sem = datetime.now() - timedelta(days=datetime.now().weekday())
dernier_jour_sem = datetime.now() + timedelta(days=(4 - datetime.now().weekday()))

# Formater les dates au format "YYYY-MM-DD"
premier_jour_semaine = premier_jour_sem.strftime("%Y-%m-%d")
dernier_jour_semaine = dernier_jour_sem.strftime("%Y-%m-%d")

#Constantes
FILE_NAME_RESULTAT = "ADECal.ics"
FILE_NAME_INSERT = 'insert_cours.sql'
FILE_NAME_QUERRY_COURS = 'query_insert_groupes.txt'
FILE_NAME_QUERRY_GRP_COUR = 'query_insert_groupes_cours.txt'
# URL = "https://adelb.univ-lyon1.fr/jsp/custom/modules/plannings/anonymous_cal.jsp?resources=85872&projectId=3&calType=ical&firstDate=01-01-2023&lastDate=2024-04-15"# + premier_jour_semaine + "&lastDate=" + dernier_jour_semaine
URL = "https://adelb.univ-lyon1.fr/jsp/custom/modules/plannings/anonymous_cal.jsp?resources=10083,85876,85875&projectId=3&calType=ical&firstDate=2023-01-01&lastDate=2024-04-15"# + premier_jour_semaine + "&lastDate=" + dernier_jour_semaine
HEADERS = {"User-Agent":"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36"}

# Chemin vers les fichiers
file_path_r = os.path.join(os.path.dirname(__file__), FILE_NAME_RESULTAT)
file_path_w = os.path.join(os.path.dirname(__file__), FILE_NAME_INSERT)
query_path_cours = os.path.join(os.path.dirname(__file__), FILE_NAME_QUERRY_COURS)
query_path_grp_cours = os.path.join(os.path.dirname(__file__), FILE_NAME_QUERRY_GRP_COUR)

#Récupérer le fichier ADECal.ics
try:
    reponse = requests.get(URL,headers=HEADERS)
    reponse.encoding = "latin-1"
    if reponse.status_code == 200:
        with open(file_path_r, "w") as f:
            f.write(reponse.text)
    else:
        print("Erreur", reponse.status_code)
except ValueError:
    print("Erreur", reponse.status_code)

# Fonction pour extraire les données des événements
def extract_event_data(event):
    data = {}
    for line in event.split('\n'):
        if ':' in line:
            key, value = line.split(':', 1)
            data[key] = value
    return data

# Fonction pour convertir l'heure UTC en format HH:MM
def convert_utc_to_hhmm(utc_time):
    return utc_time[9:11] + ':' + utc_time[11:13]

# Ouvrir le fichier .ics et lire son contenu
with open(file_path_r, "r", encoding="utf-8", errors='ignore') as file:
    ics_data = file.read()

# Séparer les événements
events = re.split(r'BEGIN:VEVENT', ics_data)
events.pop(0)  # Retirer le premier élément vide


# Dictionnaire pour mapper les matières et les professeurs aux IDs correspondants
matieres_ids = {
    "JavaScript": 1,
    "PHP": 2,
    "Dev efficace(algo avancé)": 3,
    "Analyse": 4,
    "Qualité de développement": 5,
    "Programmation Système": 6,
    "Architecture Réseaux": 7,
    "SQL et programmation": 8,
    "Probabilité": 9,
    "Cryptographie": 10,
    "Management SI": 11,
    "Droit contrats et numérique": 12,
    "Anglais": 13,
    "Communication professionnelle": 14,
    "PPP": 15,
    "SAE-1": 16,
    "Graphes": 17,
    "Modélisation": 18,
    "Management avancé des SI": 19,
    "Archi. logicielle": 20,
    "Cryptographie et sécurité": 21,
    "Réseau avancé": 22,
    "Communication interne": 23,
    "Qualité & non-relationnel": 24,
    "Méthodes d'optimisation": 25,
    "Saé G4":16,
    "Intro Informatique décisionnelle":26,
    "Développement mobile":27,
    "Automates et langages":28,
    "Virtualisation":29,
}

professeurs_ids = {
    "MONNET ALEXIS": 24,
    "MAS SEBASTIEN": 96,
    "JALOUX CHRISTOPHE": 30,
    "DEBOUTE JOCELYNE": 33,
    "PEYTAVIE ADRIEN": 94,
    "BALDE THIERNO": 97,
    "BESSAC MARIETTE":91,
    "BOSON ERIC":90,
    "LEROUX STEPHANE": 23,
    "MONNET ALEXIS": 24,
    "KHEDDOUCI HAMAMACHE": 25,
    "FACI NOURA": 26,
    "DUCHENE ERIC": 27,
    "MERRHEIM XAVIER": 28,
    "BARON ARIANE": 29,
    "JALOUX CHRISTOPHE": 30,
    "DUFOUR KARLINE": 31,
    "GARACCI DANIEL": 32,
    "DEBOUTE JOCELYNE": 33,
    "PEYTAVIE ADRIEN": 34,
    "YACOUBI AYADI NADIA": 35,
    "GUERIN ESTHER": 36,
    "BONNET CHRISTINE": 37,
    "JOUBERT AUDE": 38,
    "CHAMBADE ADRIEN": 39,
    "MAS MARION": 40,
    "WATRIGANT REMI": 41,
    "GAESSLER GAETAN": 93,
    "FERROUD-PLATTET JULIE": 94,
    "RABUT THEO":95,
    "\nPEYTAVIE ADRIEN\nJALOUX CHRISTOPHE\nJOUBERT AUDE\n":-1,
    "CHASTAGNIER CARLOS":98,
    "BUSSON ANTHONY":99,
    "GIANELLA THEO":100,

}

salles_ids = {
    "S01": 1,
    "S03": 2,
    "S10": 3,
    "S11": 4,
    "S12": 5,
    "S13": 6,
    "S14": 7,
    "S15": 8,
    "S16": 9,
    "S17": 10,
    "S18": 11,
    "S21": 12,
    "S24": 13,
    "S25": 14,
    "S26": 15,
    "S27": 16,
    "Amhpi 1": 17,
    "Amphi 2": 18,
    "026-Langues": 19,
    "040": 20,
    "028" : 21,
    "S23 - TP réseau":22,
}

groupes_ids = {
    "G4S4A":37,
    "G4S4B":38,
    "G4S4":38,
}


# Créer le fichier SQL pour les cours
with open(file_path_w, 'w', encoding="latin-1") as sql_file:
    sql_file.write("TRUNCATE `cours`; \nTRUNCATE `groupe_par_cours`;\n")
    with open(query_path_cours, 'w', encoding="latin-1") as query_file_cours:
        with open(query_path_grp_cours, 'w', encoding="latin-1") as query_file_groupe_cours:

            query_file_cours.write("TRUNCATE `cours` \nTRUNCATE `groupe_par_cours`\n")
            query_file_cours.write("INSERT INTO `cours` (`ID_COURS`,`ID_MATIERE`, `ID_SALLE`, `ID_PROFESSEUR`, `J_COURS`, `H_D_COURS`, `H_F_COURS`) VALUES (%s,%s,%s,%s,%s,%s,%s)\n")
            query_file_groupe_cours.write("INSERT INTO `groupe_par_cours` (`ID_COURS`, `ID_GROUPE`) VALUES (%s,%s)\n")

            indice = 1

            for event in events:
                event_data = extract_event_data(event)
                
                # Extraire les informations pertinentes des événements
                sum = event_data.get('SUMMARY', '').split(":", 1)[0].strip()
                id_matiere = next(((matiere_id) for matiere, matiere_id in matieres_ids.items() if matiere in sum), sum)
                
                salle = salles_ids.get((event_data.get('LOCATION', '')),'null')
                
                description = event_data.get('DESCRIPTION', '')
                id_group = next((id_groupe for groupe, id_groupe in groupes_ids.items() if groupe in description), description)
                id_prof = next((id_professeur for professeur, id_professeur in professeurs_ids.items() if professeur in description), description)
                
                jour = datetime.strptime((event_data.get('DTSTART', '')[:8]), '%Y%m%d').strftime('%Y-%m-%d')
                
                heure_debut = convert_utc_to_hhmm(event_data.get('DTSTART', '')) + ":00"
                heure_debut_dt = datetime.strptime(heure_debut, '%H:%M:%S')
                heure_debut_dt += timedelta(hours=1)
                heure_debut = heure_debut_dt.strftime('%H:%M:%S')
                heure_fin = convert_utc_to_hhmm(event_data.get('DTEND', '')) + ":00"
                heure_fin_dt = datetime.strptime(heure_fin, '%H:%M:%S')
                heure_fin_dt += timedelta(hours=1)
                heure_fin = heure_fin_dt.strftime('%H:%M:%S')
                
                if (heure_debut == "07:00:00" and heure_fin == "09:00:00"):
                    heure_debut = "08:00:00"
                    heure_fin = "10:00:00"
                if (heure_debut == "09:00:00" and heure_fin == "11:00:00"):
                    heure_debut = "10:00:00"
                    heure_fin = "12:00:00"
                # Écrire l'insertion SQL
                if(id_matiere != "null" and salle != "null" and id_prof != "null" and id_group != "null" and jour != "2024-04-06"):
                    sql_file.write("INSERT INTO `cours` (`ID_MATIERE`, `ID_SALLE`, `ID_PROFESSEUR`, `J_COURS`, `H_D_COURS`, `H_F_COURS`) VALUES")
                    sql_file.write(f"({id_matiere}, {salle}, {id_prof}, '{jour}', '{heure_debut}', '{heure_fin}');\n")
                    query_file_cours.write(f"({indice}, {id_matiere}, {salle}, {id_prof}, {jour}, {heure_debut}, {heure_fin})\n")

                    sql_file.write("INSERT INTO `groupe_par_cours` (`ID_COURS`, `ID_GROUPE`) VALUES ")
                    sql_file.write(f"({indice}, {id_group});\n")
                    query_file_groupe_cours.write(f"({indice}, {id_group})\n")
                
                    indice += 1

import mysql.connector

# Configuration de la connexion à la base de données
config = {
    'user': 'root',
    'password': '',
    'host': 'localhost',
    'database': 'bd_brap',
}

