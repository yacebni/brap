/*==============================================================*/
/* Nom de SGBD :  MySQL 5.0                                     */
/* Date de cr�ation :  23/11/2023 13:02:22                      */
/*==============================================================*/

/*Suppression des tables pour les recréer*/
drop table if exists ABSENCE;
drop table if exists ADMINISTRATION;
drop table if exists ANNEE;
drop table if exists AVERTISSEMENT;
drop table if exists CLASSE;
drop table if exists CONVOCATION;
drop table if exists COURS;
drop table if exists DOSSIERPARTAGE;
drop table if exists EMPLOIDUTEMPS;
drop table if exists ENSEIGNANT;
drop table if exists ETUDIANT;
drop table if exists EXAMEN;
drop table if exists FICHIER;
drop table if exists FORMATION;
drop table if exists GROUPE;
drop table if exists MATIERE;
drop table if exists NIVEAU;
drop table if exists NOTE;
drop table if exists PARCOURS;
drop table if exists PORTFOLIO;
drop table if exists RENSEIGNANTMATIERE;
drop table if exists RESSOURCE;
drop table if exists RETARD;
drop table if exists RGROUPEETUDIANT;
drop table if exists RPARCOURSFORMATION;
drop table if exists RSEMESTRECLASSE;
drop table if exists RSEMESTREPAROURS;
drop table if exists RUTILISATEURLISDOSSIER;
drop table if exists SAE;
drop table if exists SEMESTRE;
drop table if exists STAGE;
drop table if exists UTILISATEUR;

/*==============================================================*/
/* Table : ABSENCE                                             */
/*==============================================================*/
CREATE TABLE ABSENCE (
   IDABSENCE           INT AUTO_INCREMENT,
   IDETUDIANT          INT NOT NULL,
   IDATTRIBUTEUR       INT NOT NULL,
   ESTJUSTIFIE         BOOLEAN NOT NULL,
   MOTIFABSENCE        VARCHAR(250) NOT NULL,
   primary key(IDABSENCE)
)ENGINE=InnoDB;

/*==============================================================*/
/* Table : ADMINISTRATION                                       */
/*==============================================================*/
create table ADMINISTRATION
(
   IDADMIN              int not null,
   primary key (IDADMIN)
)ENGINE=InnoDB;

/*==============================================================*/
/* Table : ANNEE                                                */
/*==============================================================*/
create table ANNEE
(
   ANNEE                date not null,
   primary key (ANNEE)
)ENGINE=InnoDB;

/*==============================================================*/
/* Table : AVERTISSEMENT                                        */
/*==============================================================*/
create table AVERTISSEMENT
(
   IDAVERTISSEMENT      int AUTO_INCREMENT,
   IDETUDIANT           int not null,
   IDATTRIBUTEUR        int not null,
   MOTIFAVERTISSEMENT   varchar(250) not null,
   primary key (IDAVERTISSEMENT)
)ENGINE=InnoDB;

/*==============================================================*/
/* Table : CLASSE                                               */
/*==============================================================*/
create table CLASSE
(
   IDCLASSE             int AUTO_INCREMENT,
   IDGROUPE             int not null,
   NOMCLASSE            char(32) not null,
   primary key (IDCLASSE)
)ENGINE=InnoDB;

/*==============================================================*/
/* Table : CONVOCATION                                          */
/*==============================================================*/
create table CONVOCATION
(
   IDCONVOCATION        int AUTO_INCREMENT,
   IDETUDIANT           int not null,
   IDATTRIBUTEUR        int not null,
   CONTENU              varchar(500) not null,
   HORAIREDEBUT         datetime not null,
   HORAIREFIN           datetime not null,
   primary key (IDCONVOCATION)
)ENGINE=InnoDB;

/*==============================================================*/
/* Table : COURS                                                */
/*==============================================================*/
create table COURS
(
   IDCOURS              int AUTO_INCREMENT,
   IDEDT                int not null,
   NOMCOURS             varchar(32) not null,
   HORAIREDEBUTCOURS    time not null,
   HORAIREFINCOURS      time not null,
   primary key (IDCOURS)
)ENGINE=InnoDB;

/*==============================================================*/
/* Table : DOSSIERPARTAGE                                       */
/*==============================================================*/
create table DOSSIERPARTAGE
(
   ODDOSSIERPARTAGE     int AUTO_INCREMENT,
   IDUTILISATEUR        int not null,
   NOMDOSSIER           varchar(32) not null,
   DATELIMITE           datetime,
   primary key (ODDOSSIERPARTAGE)
)ENGINE=InnoDB;

/*==============================================================*/
/* Table : EMPLOIDUTEMPS                                        */
/*==============================================================*/
create table EMPLOIDUTEMPS
(
   IDEDT                int AUTO_INCREMENT,
   HORAIREDEBUTMIN      time not null,
   HORAIREFINMAX        time not null,
   HORAIREPAUSEMIN      time not null,
   HORAIREPAUSEMAX      time not null,
   primary key (IDEDT)
)ENGINE=InnoDB;

/*==============================================================*/
/* Table : ENSEIGNANT                                           */
/*==============================================================*/
create table ENSEIGNANT
(
   IDENSEIGNANT         int not null,
   IDEDT                int not null,
   primary key (IDENSEIGNANT)
)ENGINE=InnoDB;

/*==============================================================*/
/* Table : ETUDIANT                                             */
/*==============================================================*/
create table ETUDIANT
(
   IDETUDIANT           int not null,
   primary key (IDETUDIANT)
)ENGINE=InnoDB;

/*==============================================================*/
/* Table : EXAMEN                                               */
/*==============================================================*/
create table EXAMEN
(
   IDEXAMEN             int AUTO_INCREMENT,
   IDENSEIGNANT         int not null,
   INTITULE             varchar(32) not null,
   NBPOINTSTOTAL        int not null,
   DATEEXAMEN           date not null,
   primary key (IDEXAMEN)
)ENGINE=InnoDB;

/*==============================================================*/
/* Table : FICHIER                                              */
/*==============================================================*/
create table FICHIER
(
   IDFICHIER            int AUTO_INCREMENT,
   ODDOSSIERPARTAGE     int not null,
   LIENFICHIER          varchar(250) not null,
   DATEAJOUT            timestamp not null,
   primary key (IDFICHIER)
)ENGINE=InnoDB;

/*==============================================================*/
/* Table : FORMATION                                            */
/*==============================================================*/
create table FORMATION
(
   IDFORMATION          int AUTO_INCREMENT,
   NOMFORMATION         varchar(32) not null,
   NOMDEPARTEMENT       varchar(32) not null,
   primary key (IDFORMATION)
)ENGINE=InnoDB;

/*==============================================================*/
/* Table : GROUPE                                               */
/*==============================================================*/
create table GROUPE
(
   IDGROUPE             int AUTO_INCREMENT,
   IDEDT                int not null,
   NOMGROUPE            varchar(32) not null,
   ABBREVIATIONGROUPE   varchar(32) not null,
   primary key (IDGROUPE)
)ENGINE=InnoDB;

/*==============================================================*/
/* Table : MATIERE                                              */
/*==============================================================*/
create table MATIERE
(
   IDMATIERE            int AUTO_INCREMENT,
   IDRESSOURCE          int not null,
   NOMMATIERE           varchar(32),
   primary key (IDMATIERE)
)ENGINE=InnoDB;

/*==============================================================*/
/* Table : NIVEAU                                               */
/*==============================================================*/
create table NIVEAU
(
   IDNIVEAU             int AUTO_INCREMENT,
   NOMNIVEAU            varchar(32) not null,
   primary key (IDNIVEAU)
)ENGINE=InnoDB;

/*==============================================================*/
/* Table : NOTE                                                 */
/*==============================================================*/
create table NOTE
(
   IDNOTE               int AUTO_INCREMENT,
   IDETUDIANT           int not null,
   IDRESSOURCE          int,
   IDPORTFOLIO          int,
   IDEXAMEN             int not null,
   IDSTAGE              int,
   IDSAE                int,
   NOTEOBTENUE          int not null,
   APPRECIATION         varchar(500),
   DATESAISIE           date not null,
   primary key (IDNOTE)
)ENGINE=InnoDB;

/*==============================================================*/
/* Table : PARCOURS                                             */
/*==============================================================*/
create table PARCOURS
(
   IDPARCOURS           int AUTO_INCREMENT,
   CODEPARCOURS         int not null,
   NOMPARCOURS          varchar(32) not null,
   primary key (IDPARCOURS)
)ENGINE=InnoDB;

/*==============================================================*/
/* Table : PORTFOLIO                                            */
/*==============================================================*/
create table PORTFOLIO
(
   IDPORTFOLIO          int AUTO_INCREMENT,
   IDNOTE               int,
   COEFFICIENTPORTFOLIO int not null,
   primary key (IDPORTFOLIO)
)ENGINE=InnoDB;

/*==============================================================*/
/* Table : RENSEIGNANTMATIERE                                   */
/*==============================================================*/
create table RENSEIGNANTMATIERE
(
   IDENSEIGNANT         int not null,
   IDMATIERE            int not null,
   primary key (IDENSEIGNANT, IDMATIERE)
)ENGINE=InnoDB;

/*==============================================================*/
/* Table : RESSOURCE                                            */
/*==============================================================*/
create table RESSOURCE
(
   IDRESSOURCE          int AUTO_INCREMENT,
   NOMRESSOURCE         varchar(32) not null,
   CODERESSOURCE        varchar(32) not null,
   COEFFICIENTRESSOURCE int not null,
   primary key (IDRESSOURCE)
)ENGINE=InnoDB;

/*==============================================================*/
/* Table : RETARD                                               */
/*==============================================================*/
create table RETARD
(
   ID_RETARD            int AUTO_INCREMENT,
   IDETUDIANT           int not null,
   IDUTILISATEUR        int not null,
   NBMINUTES            int not null,
   MOTIFRETARD          varchar(250) not null,
   primary key (ID_RETARD)
)ENGINE=InnoDB;

/*==============================================================*/
/* Table : RGROUPEETUDIANT                                      */
/*==============================================================*/
create table RGROUPEETUDIANT
(
   IDETUDIANT           int not null,
   IDGROUPE             int not null,
   ANNEE                date not null,
   primary key (IDETUDIANT, IDGROUPE, ANNEE)
)ENGINE=InnoDB;

/*==============================================================*/
/* Table : RPARCOURSFORMATION                                   */
/*==============================================================*/
create table RPARCOURSFORMATION
(
   IDFORMATION          int not null,
   IDPARCOURS           int not null,
   IDNIVEAU             int not null,
   primary key (IDFORMATION, IDPARCOURS, IDNIVEAU)
)ENGINE=InnoDB;

/*==============================================================*/
/* Table : RSEMESTRECLASSE                                      */
/*==============================================================*/
create table RSEMESTRECLASSE
(
   IDSEMESTRE           int not null,
   IDCLASSE             int not null,
   primary key (IDSEMESTRE, IDCLASSE)
)ENGINE=InnoDB;

/*==============================================================*/
/* Table : RSEMESTREPAROURS                                     */
/*==============================================================*/
create table RSEMESTREPAROURS
(
   IDPARCOURS           int not null,
   IDSEMESTRE           int not null,
   primary key (IDPARCOURS, IDSEMESTRE)
)ENGINE=InnoDB;

/*==============================================================*/
/* Table : RUTILISATEURLISDOSSIER                               */
/*==============================================================*/
create table RUTILISATEURLISDOSSIER
(
   IDUTILISATEUR        int not null,
   ODDOSSIERPARTAGE     int not null,
   primary key (IDUTILISATEUR, ODDOSSIERPARTAGE)
)ENGINE=InnoDB;

/*==============================================================*/
/* Table : SAE                                                  */
/*==============================================================*/
create table SAE
(
   IDSAE                int AUTO_INCREMENT,
   NOMSAE               varchar(32) not null,
   CODESAE              varchar(32) not null,
   COEFFICIENTSAE       int not null,
   primary key (IDSAE)
)ENGINE=InnoDB;

/*==============================================================*/
/* Table : SEMESTRE                                             */
/*==============================================================*/
create table SEMESTRE
(
   IDSEMESTRE           int AUTO_INCREMENT,
   NOMSEMESTRE          varchar(32) not null,
   primary key (IDSEMESTRE)
)ENGINE=InnoDB;

/*==============================================================*/
/* Table : STAGE                                                */
/*==============================================================*/
create table STAGE
(
   IDSTAGE              int AUTO_INCREMENT,
   IDNOTE               int,
   NOMSTAGE             varchar(32) not null,
   LIEUSTAGE            varchar(32) not null,
   MAITRESTAGE          varchar(32) not null,
   primary key (IDSTAGE)
)ENGINE=InnoDB;

/*==============================================================*/
/* Table : UTILISATEUR                                          */
/*==============================================================*/
create table UTILISATEUR
(
   IDUTILISATEUR        int AUTO_INCREMENT,
   NOM                  varchar(32) not null,
   PRENOM               varchar(32) not null,
   ADRESSEMAIL          varchar(32),
   IDENTIFIANT          char(32) not null,
   MDP                  varchar(32) not null,
   primary key (IDUTILISATEUR)
)ENGINE=InnoDB;

/*Initialisation des contraintes*/

alter table ABSENCE add constraint FK_RETUDIANTABSENCE foreign key (IDETUDIANT)
      references ETUDIANT (IDETUDIANT) on delete restrict on update restrict;

alter table ABSENCE add constraint FK_RUTILLISATEURABSENCE foreign key (IDATTRIBUTEUR)
      references UTILISATEUR (IDUTILISATEUR) on delete restrict on update restrict;

alter table ADMINISTRATION add constraint FK_RUTILISATEURADMINISTRATION2 foreign key (IDADMIN)
      references UTILISATEUR (IDUTILISATEUR) on delete restrict on update restrict;

alter table AVERTISSEMENT add constraint FK_RETUDIANTAVERTISSEMENT foreign key (IDETUDIANT)
      references ETUDIANT (IDETUDIANT) on delete restrict on update restrict;

alter table AVERTISSEMENT add constraint FK_RUTILISATEURAVERTISSEMENT foreign key (IDATTRIBUTEUR)
      references UTILISATEUR (IDUTILISATEUR) on delete restrict on update restrict;

alter table CLASSE add constraint FK_RCLASSEGROUPE foreign key (IDGROUPE)
      references GROUPE (IDGROUPE) on delete restrict on update restrict;

alter table CONVOCATION add constraint FK_RETUDIANTCONVOCATION foreign key (IDETUDIANT)
      references ETUDIANT (IDETUDIANT) on delete restrict on update restrict;

alter table CONVOCATION add constraint FK_RUTILISATEURCONVOCATION foreign key (IDATTRIBUTEUR)
      references UTILISATEUR (IDUTILISATEUR) on delete restrict on update restrict;

alter table COURS add constraint FK_REDTCOURS foreign key (IDEDT)
      references EMPLOIDUTEMPS (IDEDT) on delete restrict on update restrict;

alter table DOSSIERPARTAGE add constraint FK_RUTILISATEURCREEDOSSIER foreign key (IDUTILISATEUR)
      references UTILISATEUR (IDUTILISATEUR) on delete restrict on update restrict;

alter table ENSEIGNANT add constraint FK_RENSEIGNANTEDT2 foreign key (IDEDT)
      references EMPLOIDUTEMPS (IDEDT) on delete restrict on update restrict;

alter table ENSEIGNANT add constraint FK_RUTILISATEURENSEIGNANT2 foreign key (IDENSEIGNANT)
      references UTILISATEUR (IDUTILISATEUR) on delete restrict on update restrict;

alter table ETUDIANT add constraint FK_RUTILISATEURETUDIANT2 foreign key (IDETUDIANT)
      references UTILISATEUR (IDUTILISATEUR) on delete restrict on update restrict;

alter table EXAMEN add constraint FK_RENSEIGNANTEXAMEN foreign key (IDENSEIGNANT)
      references ENSEIGNANT (IDENSEIGNANT) on delete restrict on update restrict;

alter table FICHIER add constraint FK_RDOSSIERFICHIER foreign key (ODDOSSIERPARTAGE)
      references DOSSIERPARTAGE (ODDOSSIERPARTAGE) on delete restrict on update restrict;

alter table GROUPE add constraint FK_RCLASSEEDT foreign key (IDEDT)
      references EMPLOIDUTEMPS (IDEDT) on delete restrict on update restrict;

alter table MATIERE add constraint FK_RRESSOURCEMATIERE foreign key (IDRESSOURCE)
      references RESSOURCE (IDRESSOURCE) on delete restrict on update restrict;

alter table NOTE add constraint FK_RELATION_28 foreign key (IDETUDIANT)
      references ETUDIANT (IDETUDIANT) on delete restrict on update restrict;

alter table NOTE add constraint FK_RNOTEEXAMEN foreign key (IDEXAMEN)
      references EXAMEN (IDEXAMEN) on delete restrict on update restrict;

alter table NOTE add constraint FK_RPORTFOLIONOTE foreign key (IDPORTFOLIO)
      references PORTFOLIO (IDPORTFOLIO) on delete restrict on update restrict;

alter table NOTE add constraint FK_RRESSOURCENOTE foreign key (IDRESSOURCE)
      references RESSOURCE (IDRESSOURCE) on delete restrict on update restrict;

alter table NOTE add constraint FK_RSAENOTE foreign key (IDSAE)
      references SAE (IDSAE) on delete restrict on update restrict;

alter table NOTE add constraint FK_RSTAGENOTE foreign key (IDSTAGE)
      references STAGE (IDSTAGE) on delete restrict on update restrict;

alter table PORTFOLIO add constraint FK_RPORTFOLIONOTE2 foreign key (IDNOTE)
      references NOTE (IDNOTE) on delete restrict on update restrict;

alter table RENSEIGNANTMATIERE add constraint FK_RENSEIGNANTMATIERE foreign key (IDENSEIGNANT)
      references ENSEIGNANT (IDENSEIGNANT) on delete restrict on update restrict;

alter table RENSEIGNANTMATIERE add constraint FK_RENSEIGNANTMATIERE2 foreign key (IDMATIERE)
      references MATIERE (IDMATIERE) on delete restrict on update restrict;

alter table RETARD add constraint FK_RETUDIANTRETARD foreign key (IDETUDIANT)
      references ETUDIANT (IDETUDIANT) on delete restrict on update restrict;

alter table RETARD add constraint FK_RUTILISATEURRETARD foreign key (IDUTILISATEUR)
      references UTILISATEUR (IDUTILISATEUR) on delete restrict on update restrict;

alter table RGROUPEETUDIANT add constraint FK_RGROUPEETUDIANT foreign key (IDETUDIANT)
      references ETUDIANT (IDETUDIANT) on delete restrict on update restrict;

alter table RGROUPEETUDIANT add constraint FK_RGROUPEETUDIANT2 foreign key (IDGROUPE)
      references GROUPE (IDGROUPE) on delete restrict on update restrict;

alter table RGROUPEETUDIANT add constraint FK_RGROUPEETUDIANT3 foreign key (ANNEE)
      references ANNEE (ANNEE) on delete restrict on update restrict;

alter table RPARCOURSFORMATION add constraint FK_RPARCOURSFORMATION foreign key (IDFORMATION)
      references FORMATION (IDFORMATION) on delete restrict on update restrict;

alter table RPARCOURSFORMATION add constraint FK_RPARCOURSFORMATION2 foreign key (IDPARCOURS)
      references PARCOURS (IDPARCOURS) on delete restrict on update restrict;

alter table RPARCOURSFORMATION add constraint FK_RPARCOURSFORMATION3 foreign key (IDNIVEAU)
      references NIVEAU (IDNIVEAU) on delete restrict on update restrict;

alter table RSEMESTRECLASSE add constraint FK_RSEMESTRECLASSE foreign key (IDSEMESTRE)
      references SEMESTRE (IDSEMESTRE) on delete restrict on update restrict;

alter table RSEMESTRECLASSE add constraint FK_RSEMESTRECLASSE2 foreign key (IDCLASSE)
      references CLASSE (IDCLASSE) on delete restrict on update restrict;

alter table RSEMESTREPAROURS add constraint FK_RSEMESTREPAROURS foreign key (IDPARCOURS)
      references PARCOURS (IDPARCOURS) on delete restrict on update restrict;

alter table RSEMESTREPAROURS add constraint FK_RSEMESTREPAROURS2 foreign key (IDSEMESTRE)
      references SEMESTRE (IDSEMESTRE) on delete restrict on update restrict;

alter table RUTILISATEURLISDOSSIER add constraint FK_RUTILISATEURLISDOSSIER foreign key (IDUTILISATEUR)
      references UTILISATEUR (IDUTILISATEUR) on delete restrict on update restrict;

alter table RUTILISATEURLISDOSSIER add constraint FK_RUTILISATEURLISDOSSIER2 foreign key (ODDOSSIERPARTAGE)
      references DOSSIERPARTAGE (ODDOSSIERPARTAGE) on delete restrict on update restrict;

alter table STAGE add constraint FK_RSTAGENOTE2 foreign key (IDNOTE)
      references NOTE (IDNOTE) on delete restrict on update restrict;