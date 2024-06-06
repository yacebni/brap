/*==============================================================*/
/* Nom de SGBD :  MySQL 5.0                                     */
/* Date de cr�ation :  22/11/2023 11:22:16                      */
/*==============================================================*/

/*Suppression des tables pour les recréer*/
drop table if exists ABSENCE;
drop table if exists ADMINISTRATION;
drop table if exists ANNEE;
drop table if exists APPARTIENT;
drop table if exists APPARTIENT2;
drop table if exists AVERTISSEMENT;
drop table if exists CLASSE;
drop table if exists CONTIENT2;
drop table if exists CONVOCATION;
drop table if exists CORRESPOND_AU;
drop table if exists COURS;
drop table if exists DOSSIERPARTAGE;
drop table if exists EMPLOIDUTEMPS;
drop table if exists ENSEIGNANT;
drop table if exists ENSEIGNE;
drop table if exists ETUDIANT;
drop table if exists EXAMEN;
drop table if exists FICHIER;
drop table if exists FORMATION;
drop table if exists GROUPE;
drop table if exists LECTURE;
drop table if exists MATIERE;
drop table if exists NIVEAU;
drop table if exists NOTE;
drop table if exists PARCOURS;
drop table if exists PORTFOLIO;
drop table if exists RESSOURCE;
drop table if exists RETARD;
drop table if exists SAE;
drop table if exists SEMESTRE;
drop table if exists STAGE;
drop table if exists UTILISATEUR;

/*==============================================================*/
/* Table : ABSENCE                                             */
/*==============================================================*/
create table ABSENCE
(
   IDABSENCE           int not null,
   IDETUDIANT           int not null,
   IDUTILISATEUR        int not null,
   ESTJUSTIFIE          bool not null,
   MOTIFABSENCE        varchar(250) not null,
   primary key (IDABSENCE)
);

/*==============================================================*/
/* Table : ADMINISTRATION                                       */
/*==============================================================*/
create table ADMINISTRATION
(
   IDADMIN              int not null,
   IDUTILISATEUR        int not null,
   primary key (IDADMIN)
);

/*==============================================================*/
/* Table : ANNEE                                                */
/*==============================================================*/
create table ANNEE
(
   ANNEE                date not null,
   primary key (ANNEE)
);

/*==============================================================*/
/* Table : APPARTIENT                                           */
/*==============================================================*/
create table APPARTIENT
(
   IDETUDIANT           int not null,
   IDGROUPE             int not null,
   ANNEE                date not null,
   primary key (IDETUDIANT, IDGROUPE, ANNEE)
);

/*==============================================================*/
/* Table : APPARTIENT2                                          */
/*==============================================================*/
create table APPARTIENT2
(
   IDFORMATION          int not null,
   IDPARCOURS           int not null,
   IDNIVEAU             int not null,
   primary key (IDFORMATION, IDPARCOURS, IDNIVEAU)
);

/*==============================================================*/
/* Table : AVERTISSEMENT                                        */
/*==============================================================*/
create table AVERTISSEMENT
(
   IDAVERTISSEMENT      int not null,
   IDETUDIANT           int not null,
   IDUTILISATEUR        int not null,
   MOTIFAVERTISSEMENT   varchar(250) not null,
   primary key (IDAVERTISSEMENT)
);

/*==============================================================*/
/* Table : CLASSE                                               */
/*==============================================================*/
create table CLASSE
(
   IDCLASSE             int not null,
   IDGROUPE             int not null,
   NOMCLASSE            char(32) not null,
   primary key (IDCLASSE)
);

/*==============================================================*/
/* Table : CONTIENT2                                            */
/*==============================================================*/
create table CONTIENT2
(
   IDPARCOURS           int not null,
   IDSEMESTRE           int not null,
   primary key (IDPARCOURS, IDSEMESTRE)
);

/*==============================================================*/
/* Table : CONVOCATION                                          */
/*==============================================================*/
create table CONVOCATION
(
   IDCONVOCATION        int not null,
   IDETUDIANT           int not null,
   IDUTILISATEUR        int not null,
   CONTENU              varchar(500) not null,
   HORAIREDEBUT         datetime not null,
   HORAIREFIN           datetime not null,
   primary key (IDCONVOCATION)
);

/*==============================================================*/
/* Table : CORRESPOND_AU                                        */
/*==============================================================*/
create table CORRESPOND_AU
(
   IDSEMESTRE           int not null,
   IDCLASSE             int not null,
   primary key (IDSEMESTRE, IDCLASSE)
);

/*==============================================================*/
/* Table : COURS                                                */
/*==============================================================*/
create table COURS
(
   IDCOURS              int not null,
   IDEDT                int not null,
   NOMCOURS             varchar(32) not null,
   HORAIREDEBUTCOURS    time not null,
   HORAIREFINCOURS      time not null,
   primary key (IDCOURS)
);

/*==============================================================*/
/* Table : DOSSIERPARTAGE                                       */
/*==============================================================*/
create table DOSSIERPARTAGE
(
   ODDOSSIERPARTAGE     int not null,
   IDUTILISATEUR        int not null,
   NOMDOSSIER           varchar(32) not null,
   DATELIMITE           datetime,
   primary key (ODDOSSIERPARTAGE)
);

/*==============================================================*/
/* Table : EMPLOIDUTEMPS                                        */
/*==============================================================*/
create table EMPLOIDUTEMPS
(
   IDEDT                int not null,
   IDENSEIGNANT         int,
   IDGROUPE             int,
   HORAIREDEBUTMIN      time not null,
   HORAIREFINMAX        time not null,
   HORAIREPAUSEMIN      time not null,
   HORAIREPAUSEMAX      time not null,
   primary key (IDEDT)
);

/*==============================================================*/
/* Table : ENSEIGNANT                                           */
/*==============================================================*/
create table ENSEIGNANT
(
   IDENSEIGNANT         int not null,
   IDEDT                int not null,
   IDUTILISATEUR        int not null,
   primary key (IDENSEIGNANT)
);

/*==============================================================*/
/* Table : ENSEIGNE                                             */
/*==============================================================*/
create table ENSEIGNE
(
   IDENSEIGNANT         int not null,
   IDMATIERE            int not null,
   primary key (IDENSEIGNANT, IDMATIERE)
);

/*==============================================================*/
/* Table : ETUDIANT                                             */
/*==============================================================*/
create table ETUDIANT
(
   IDETUDIANT           int not null,
   IDUTILISATEUR        int not null,
   primary key (IDETUDIANT)
);

/*==============================================================*/
/* Table : EXAMEN                                               */
/*==============================================================*/
create table EXAMEN
(
   IDEXAMEN             int not null,
   IDENSEIGNANT         int not null,
   INTITULE             varchar(32) not null,
   NBPOINTSTOTAL        int not null,
   DATEEXAMEN           date not null,
   primary key (IDEXAMEN)
);

/*==============================================================*/
/* Table : FICHIER                                              */
/*==============================================================*/
create table FICHIER
(
   IDFICHIER            int not null,
   ODDOSSIERPARTAGE     int not null,
   LIENFICHIER          varchar(250) not null,
   DATEAJOUT            timestamp not null,
   primary key (IDFICHIER)
);

/*==============================================================*/
/* Table : FORMATION                                            */
/*==============================================================*/
create table FORMATION
(
   IDFORMATION          int not null,
   NOMFORMATION         varchar(32) not null,
   NOMDEPARTEMENT       varchar(32) not null,
   primary key (IDFORMATION)
);

/*==============================================================*/
/* Table : GROUPE                                               */
/*==============================================================*/
create table GROUPE
(
   IDGROUPE             int not null,
   IDEDT                int not null,
   NOMGROUPE            varchar(32) not null,
   ABBREVIATIONGROUPE   varchar(32) not null,
   primary key (IDGROUPE)
);

/*==============================================================*/
/* Table : LECTURE                                              */
/*==============================================================*/
create table LECTURE
(
   IDUTILISATEUR        int not null,
   ODDOSSIERPARTAGE     int not null,
   primary key (IDUTILISATEUR, ODDOSSIERPARTAGE)
);

/*==============================================================*/
/* Table : MATIERE                                              */
/*==============================================================*/
create table MATIERE
(
   IDMATIERE            int not null,
   IDRESSOURCE          int not null,
   NOMMATIERE           varchar(32),
   primary key (IDMATIERE)
);

/*==============================================================*/
/* Table : NIVEAU                                               */
/*==============================================================*/
create table NIVEAU
(
   IDNIVEAU             int not null,
   NOMNIVEAU            varchar(32) not null,
   primary key (IDNIVEAU)
);

/*==============================================================*/
/* Table : NOTE                                                 */
/*==============================================================*/
create table NOTE
(
   IDNOTE               int not null,
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
);

/*==============================================================*/
/* Table : PARCOURS                                             */
/*==============================================================*/
create table PARCOURS
(
   IDPARCOURS           int not null,
   CODEPARCOURS         int not null,
   NOMPARCOURS          varchar(32) not null,
   primary key (IDPARCOURS)
);

/*==============================================================*/
/* Table : PORTFOLIO                                            */
/*==============================================================*/
create table PORTFOLIO
(
   IDPORTFOLIO          int not null,
   IDNOTE               int,
   COEFFICIENTPORTFOLIO int not null,
   primary key (IDPORTFOLIO)
);

/*==============================================================*/
/* Table : RESSOURCE                                            */
/*==============================================================*/
create table RESSOURCE
(
   IDRESSOURCE          int not null,
   NOMRESSOURCE         varchar(32) not null,
   CODERESSOURCE        varchar(32) not null,
   COEFFICIENTRESSOURCE int not null,
   primary key (IDRESSOURCE)
);

/*==============================================================*/
/* Table : RETARD                                               */
/*==============================================================*/
create table RETARD
(
   ID_RETARD            int not null,
   IDETUDIANT           int not null,
   IDUTILISATEUR        int not null,
   NBMINUTES            int not null,
   MOTIFRETARD          varchar(250) not null,
   primary key (ID_RETARD)
);

/*==============================================================*/
/* Table : SAE                                                  */
/*==============================================================*/
create table SAE
(
   IDSAE                int not null,
   NOMSAE               varchar(32) not null,
   CODESAE              varchar(32) not null,
   COEFFICIENTSAE       int not null,
   primary key (IDSAE)
);

/*==============================================================*/
/* Table : SEMESTRE                                             */
/*==============================================================*/
create table SEMESTRE
(
   IDSEMESTRE           int not null,
   NOMSEMESTRE          varchar(32) not null,
   primary key (IDSEMESTRE)
);

/*==============================================================*/
/* Table : STAGE                                                */
/*==============================================================*/
create table STAGE
(
   IDSTAGE              int not null,
   IDNOTE               int,
   NOMSTAGE             varchar(32) not null,
   LIEUSTAGE            varchar(32) not null,
   MAITRESTAGE          varchar(32) not null,
   primary key (IDSTAGE)
);

/*==============================================================*/
/* Table : UTILISATEUR                                          */
/*==============================================================*/
create table UTILISATEUR
(
   IDUTILISATEUR        int not null,
   IDADMIN              int,
   IDETUDIANT           int,
   IDENSEIGNANT         int,
   NOM                  varchar(32) not null,
   PRENOM               varchar(32) not null,
   ADRESSEMAIL          varchar(32),
   IDENTIFIANT          char(32) not null,
   MDP                  varchar(32) not null,
   primary key (IDUTILISATEUR)
);

alter table ABSENCE add constraint FK_ATTRIBUE foreign key (IDUTILISATEUR)
      references UTILISATEUR (IDUTILISATEUR) on delete restrict on update restrict;

alter table ABSENCE add constraint FK_RECOIS3 foreign key (IDETUDIANT)
      references ETUDIANT (IDETUDIANT) on delete restrict on update restrict;

alter table ADMINISTRATION add constraint FK_EST4 foreign key (IDUTILISATEUR)
      references UTILISATEUR (IDUTILISATEUR) on delete restrict on update restrict;

alter table APPARTIENT add constraint FK_APPARTIENT foreign key (IDETUDIANT)
      references ETUDIANT (IDETUDIANT) on delete restrict on update restrict;

alter table APPARTIENT add constraint FK_APPARTIENT4 foreign key (IDGROUPE)
      references GROUPE (IDGROUPE) on delete restrict on update restrict;

alter table APPARTIENT add constraint FK_APPARTIENT5 foreign key (ANNEE)
      references ANNEE (ANNEE) on delete restrict on update restrict;

alter table APPARTIENT2 add constraint FK_APPARTIENT2 foreign key (IDFORMATION)
      references FORMATION (IDFORMATION) on delete restrict on update restrict;

alter table APPARTIENT2 add constraint FK_APPARTIENT3 foreign key (IDPARCOURS)
      references PARCOURS (IDPARCOURS) on delete restrict on update restrict;

alter table APPARTIENT2 add constraint FK_APPARTIENT6 foreign key (IDNIVEAU)
      references NIVEAU (IDNIVEAU) on delete restrict on update restrict;

alter table AVERTISSEMENT add constraint FK_DONNE2 foreign key (IDUTILISATEUR)
      references UTILISATEUR (IDUTILISATEUR) on delete restrict on update restrict;

alter table AVERTISSEMENT add constraint FK_RECOIS foreign key (IDETUDIANT)
      references ETUDIANT (IDETUDIANT) on delete restrict on update restrict;

alter table CLASSE add constraint FK_EST_CONSTITUE_DE foreign key (IDGROUPE)
      references GROUPE (IDGROUPE) on delete restrict on update restrict;

alter table CONTIENT2 add constraint FK_CONTIENT2 foreign key (IDPARCOURS)
      references PARCOURS (IDPARCOURS) on delete restrict on update restrict;

alter table CONTIENT2 add constraint FK_CONTIENT3 foreign key (IDSEMESTRE)
      references SEMESTRE (IDSEMESTRE) on delete restrict on update restrict;

alter table CONVOCATION add constraint FK_DONNE foreign key (IDUTILISATEUR)
      references UTILISATEUR (IDUTILISATEUR) on delete restrict on update restrict;

alter table CONVOCATION add constraint FK_RECOIS2 foreign key (IDETUDIANT)
      references ETUDIANT (IDETUDIANT) on delete restrict on update restrict;

alter table CORRESPOND_AU add constraint FK_CORRESPOND_AU foreign key (IDSEMESTRE)
      references SEMESTRE (IDSEMESTRE) on delete restrict on update restrict;

alter table CORRESPOND_AU add constraint FK_CORRESPOND_AU2 foreign key (IDCLASSE)
      references CLASSE (IDCLASSE) on delete restrict on update restrict;

alter table COURS add constraint FK_EST_PLACE_DANS foreign key (IDEDT)
      references EMPLOIDUTEMPS (IDEDT) on delete restrict on update restrict;

alter table DOSSIERPARTAGE add constraint FK_CREE foreign key (IDUTILISATEUR)
      references UTILISATEUR (IDUTILISATEUR) on delete restrict on update restrict;

alter table EMPLOIDUTEMPS add constraint FK_EST_ATTRIBUE_AU2 foreign key (IDGROUPE)
      references GROUPE (IDGROUPE) on delete restrict on update restrict;

alter table EMPLOIDUTEMPS add constraint FK_POSSEDE foreign key (IDENSEIGNANT)
      references ENSEIGNANT (IDENSEIGNANT) on delete restrict on update restrict;

alter table ENSEIGNANT add constraint FK_EST6 foreign key (IDUTILISATEUR)
      references UTILISATEUR (IDUTILISATEUR) on delete restrict on update restrict;

alter table ENSEIGNANT add constraint FK_POSSEDE2 foreign key (IDEDT)
      references EMPLOIDUTEMPS (IDEDT) on delete restrict on update restrict;

alter table ENSEIGNE add constraint FK_ENSEIGNE foreign key (IDENSEIGNANT)
      references ENSEIGNANT (IDENSEIGNANT) on delete restrict on update restrict;

alter table ENSEIGNE add constraint FK_ENSEIGNE2 foreign key (IDMATIERE)
      references MATIERE (IDMATIERE) on delete restrict on update restrict;

alter table ETUDIANT add constraint FK_EST2 foreign key (IDUTILISATEUR)
      references UTILISATEUR (IDUTILISATEUR) on delete restrict on update restrict;

alter table EXAMEN add constraint FK_RESPONSABLE foreign key (IDENSEIGNANT)
      references ENSEIGNANT (IDENSEIGNANT) on delete restrict on update restrict;

alter table FICHIER add constraint FK_COMPREND foreign key (ODDOSSIERPARTAGE)
      references DOSSIERPARTAGE (ODDOSSIERPARTAGE) on delete restrict on update restrict;

alter table GROUPE add constraint FK_EST_ATTRIBUE_AU foreign key (IDEDT)
      references EMPLOIDUTEMPS (IDEDT) on delete restrict on update restrict;

alter table LECTURE add constraint FK_LECTURE foreign key (IDUTILISATEUR)
      references UTILISATEUR (IDUTILISATEUR) on delete restrict on update restrict;

alter table LECTURE add constraint FK_LECTURE2 foreign key (ODDOSSIERPARTAGE)
      references DOSSIERPARTAGE (ODDOSSIERPARTAGE) on delete restrict on update restrict;

alter table MATIERE add constraint FK_CORRESPOND5 foreign key (IDRESSOURCE)
      references RESSOURCE (IDRESSOURCE) on delete restrict on update restrict;

alter table NOTE add constraint FK_CORRESPOND foreign key (IDSAE)
      references SAE (IDSAE) on delete restrict on update restrict;

alter table NOTE add constraint FK_CORRESPOND2 foreign key (IDRESSOURCE)
      references RESSOURCE (IDRESSOURCE) on delete restrict on update restrict;

alter table NOTE add constraint FK_CORRESPOND3 foreign key (IDPORTFOLIO)
      references PORTFOLIO (IDPORTFOLIO) on delete restrict on update restrict;

alter table NOTE add constraint FK_CORRESPOND7 foreign key (IDSTAGE)
      references STAGE (IDSTAGE) on delete restrict on update restrict;

alter table NOTE add constraint FK_RELATION_19 foreign key (IDEXAMEN)
      references EXAMEN (IDEXAMEN) on delete restrict on update restrict;

alter table NOTE add constraint FK_RELATION_28 foreign key (IDETUDIANT)
      references ETUDIANT (IDETUDIANT) on delete restrict on update restrict;

alter table PORTFOLIO add constraint FK_CORRESPOND4 foreign key (IDNOTE)
      references NOTE (IDNOTE) on delete restrict on update restrict;

alter table RETARD add constraint FK_ATTRIBUE2 foreign key (IDUTILISATEUR)
      references UTILISATEUR (IDUTILISATEUR) on delete restrict on update restrict;

alter table RETARD add constraint FK_RECOIS4 foreign key (IDETUDIANT)
      references ETUDIANT (IDETUDIANT) on delete restrict on update restrict;

alter table STAGE add constraint FK_CORRESPOND10 foreign key (IDNOTE)
      references NOTE (IDNOTE) on delete restrict on update restrict;

alter table UTILISATEUR add constraint FK_EST foreign key (IDETUDIANT)
      references ETUDIANT (IDETUDIANT) on delete restrict on update restrict;

alter table UTILISATEUR add constraint FK_EST3 foreign key (IDADMIN)
      references ADMINISTRATION (IDADMIN) on delete restrict on update restrict;

alter table UTILISATEUR add constraint FK_EST5 foreign key (IDENSEIGNANT)
      references ENSEIGNANT (IDENSEIGNANT) on delete restrict on update restrict;

