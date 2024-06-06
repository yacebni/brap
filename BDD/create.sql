/*==============================================================*/
/* Nom de SGBD :  MySQL 5.0                                     */
/* Créé par BRAP Education                                      */
/*==============================================================*/


drop table if exists ADMINISTRATIF;

drop table if exists CLASSE;

drop table if exists COMPETENCE;

drop table if exists COURS;

drop table if exists DOSSIER_P;

drop table if exists ETUDIANT_PAR_GROUPE;

drop table if exists EVALUATION;

drop table if exists FICHIER;

drop table if exists GROUPE;

drop table if exists GROUPE_PAR_COURS;

drop table if exists MATIERE_PAR_COMP;

drop table if exists MATIERE_PAR_ENS;

drop table if exists MATIERE_RESSOURCE;

drop table if exists NOTE;

drop table if exists PARCOURS;

drop table if exists SALLE;

drop table if exists SEMESTRE;

drop table if exists UTILISATEUR;

drop table if exists UTI_CONSULTE_DOSSIER;

/*==============================================================*/
/* Table : ADMINISTRATIF                                        */
/*==============================================================*/
create table ADMINISTRATIF
(
   ID_ADMINI            int not null AUTO_INCREMENT,
   ID_ETUDIANT          int not null,
   ID_UTI_P_A           int not null,
   REMARQUE             varchar(255),
   H_D_ADM              time,
   H_F_ADM              time,
   J_ADM                date,
   TYPE_ADM             char(1), /*A = absence/R = retard/C = convocation/V = Avertissement*/
   CHEMIN               varchar(255),
   STATUT_ADM           char(1), /*N = non justifié/A = attente/J = justifié/R = refusée*/
   VU_ELEVE             boolean,
   VU_ADMIN             boolean,
   primary key (ID_ADMINI)
);

/*==============================================================*/
/* Table : CLASSE                                               */
/*==============================================================*/
create table CLASSE
(
   ID_CLASSE            int not null AUTO_INCREMENT,
   ID_SEM               int not null,
   NUM_CLASSE           int,
   primary key (ID_CLASSE)
);

/*==============================================================*/
/* Table : COMPETENCE                                           */
/*==============================================================*/
create table COMPETENCE
(
   ID_COMP              int not null AUTO_INCREMENT,
   ID_SEM               int not null,
   NOM_COMP             varchar(255),
   primary key (ID_COMP)
);

/*==============================================================*/
/* Table : COURS                                                */
/*==============================================================*/
create table COURS
(
   ID_COURS             int not null AUTO_INCREMENT,
   ID_MATIERE           int not null,
   ID_SALLE             int not null,
   ID_PROFESSEUR        int not null,
   J_COURS              date,
   H_D_COURS            time,
   H_F_COURS            time,
   primary key (ID_COURS)
);

/*==============================================================*/
/* Table : DOSSIER_P                                            */
/*==============================================================*/
create table DOSSIER_P
(
   ID_DOSSIER           int not null AUTO_INCREMENT,
   ID_CREATEUR          int not null,
   NOM_D                varchar(255),
   primary key (ID_DOSSIER)
);

/*==============================================================*/
/* Table : ETUDIANT_PAR_GROUPE                                  */
/*==============================================================*/
create table ETUDIANT_PAR_GROUPE
(
   ID_GROUPE            int not null,
   ID_ETUDIANT          int not null,
   ANNEE                int,
   primary key (ID_GROUPE, ID_ETUDIANT)
);

/*==============================================================*/
/* Table : EVALUATION                                           */
/*==============================================================*/
create table EVALUATION
(
   ID_EXAMEN            int not null AUTO_INCREMENT,
   ID_PROFESSEUR        int not null,
   ID_MATIERE           int not null,
   NOM_EXAM             varchar(255),
   DATE_EVAL            date,
   TOTAL_P              int,
   COEF_EVAL            int,
   ID_CLASSE            int,
   primary key (ID_EXAMEN)
);

/*==============================================================*/
/* Table : FICHIER                                              */
/*==============================================================*/
create table FICHIER
(
   ID_FICHIER           int not null AUTO_INCREMENT,
   ID_DOSSIER           int not null,
   ACCES                varchar(255),
   primary key (ID_FICHIER)
);

/*==============================================================*/
/* Table : GROUPE                                               */
/*==============================================================*/
create table GROUPE
(
   ID_GROUPE            int not null AUTO_INCREMENT,
   ID_CLASSE            int not null,
   NOM_GROUPE           varchar(255),
   primary key (ID_GROUPE)
);

/*==============================================================*/
/* Table : GROUPE_PAR_COURS                                     */
/*==============================================================*/
create table GROUPE_PAR_COURS
(
   ID_COURS             int not null,
   ID_GROUPE            int not null,
   primary key (ID_COURS, ID_GROUPE)
);

/*==============================================================*/
/* Table : MATIERE_PAR_COMP                                     */
/*==============================================================*/
create table MATIERE_PAR_COMP
(
   ID_MATIERE           int not null,
   ID_COMP              int not null,
   COEF_COMP            int,
   primary key (ID_MATIERE, ID_COMP)
);

/*==============================================================*/
/* Table : MATIERE_PAR_ENS                                      */
/*==============================================================*/
create table MATIERE_PAR_ENS
(
   ID_PROFESSEUR        int not null,
   ID_MATIERE           int not null,
   primary key (ID_PROFESSEUR, ID_MATIERE)
);

/*==============================================================*/
/* Table : MATIERE_RESSOURCE                                    */
/*==============================================================*/
create table MATIERE_RESSOURCE
(
   ID_MATIERE           int not null AUTO_INCREMENT,
   NOM_MATIERE          varchar(255),
   TYPE_M_R             char(1), /*M = matière/R = ressource*/
   COULEUR              char(6),
   primary key (ID_MATIERE)
);

/*==============================================================*/
/* Table : NOTE                                                 */
/*==============================================================*/
create table NOTE
(
   ID_NOTE              int not null AUTO_INCREMENT,
   ID_ETUDIANT          int not null,
   ID_EXAMEN            int not null,
   NOTE                 int,
   COMMENTAIRE          varchar(255),
   primary key (ID_NOTE)
);

/*==============================================================*/
/* Table : PARCOURS                                             */
/*==============================================================*/
create table PARCOURS
(
   ID_PARCOURS          int not null AUTO_INCREMENT,
   NOM_PARCOURS         varchar(255) not null,
   primary key (ID_PARCOURS)
);

/*==============================================================*/
/* Table : SALLE                                                */
/*==============================================================*/
create table SALLE
(
   ID_SALLE             int not null AUTO_INCREMENT,
   NOM_SALLE            varchar(255) not null,
   primary key (ID_SALLE)
);

/*==============================================================*/
/* Table : SEMESTRE                                             */
/*==============================================================*/
create table SEMESTRE
(
   ID_SEM               int not null AUTO_INCREMENT,
   ID_PARCOURS          int not null,
   NUM_SEM              int,
   primary key (ID_SEM)
);

/*==============================================================*/
/* Table : UTILISATEUR                                          */
/*==============================================================*/
create table UTILISATEUR
(
   ID_UTI               int not null AUTO_INCREMENT,
   PRENOM               varchar(255),
   NOM                  varchar(255),
   STATUT               char(1), /*E = étudiant/P = prof/A = admin*/
   IDENTIFIANT          varchar(255),
   MDP                  varchar(255),
   MDP_HASHED           boolean DEFAULT null,
   primary key (ID_UTI)
);

/*==============================================================*/
/* Table : UTI_CONSULTE_DOSSIER                                 */
/*==============================================================*/
create table UTI_CONSULTE_DOSSIER
(
   ID_DOSSIER           int not null,
   ID_CONSULTANT        int not null,
   primary key (ID_DOSSIER, ID_CONSULTANT)
);

alter table ADMINISTRATIF add constraint FK_ENS_ADM_IN_ADMI foreign key (ID_UTI_P_A)
      references UTILISATEUR (ID_UTI) on delete restrict on update restrict;

alter table ADMINISTRATIF add constraint FK_ETU_IN_ADMI foreign key (ID_ETUDIANT)
      references UTILISATEUR (ID_UTI) on delete restrict on update restrict;

alter table CLASSE add constraint FK_SEM_IN_CLASSE foreign key (ID_SEM)
      references SEMESTRE (ID_SEM) on delete restrict on update restrict;

alter table COMPETENCE add constraint FK_SEM_IN_COMP foreign key (ID_SEM)
      references SEMESTRE (ID_SEM) on delete restrict on update restrict;

alter table COURS add constraint FK_ENS_IN_COURS foreign key (ID_PROFESSEUR)
      references UTILISATEUR (ID_UTI) on delete restrict on update restrict;

alter table COURS add constraint FK_MATIERE_IN_COURS foreign key (ID_MATIERE)
      references MATIERE_RESSOURCE (ID_MATIERE) on delete restrict on update restrict;

alter table COURS add constraint FK_SALLE_IN_COURS foreign key (ID_SALLE)
      references SALLE (ID_SALLE) on delete restrict on update restrict;

alter table DOSSIER_P add constraint FK_UTI_FAIT_DOSSIER foreign key (ID_CREATEUR)
      references UTILISATEUR (ID_UTI) on delete restrict on update restrict;

alter table ETUDIANT_PAR_GROUPE add constraint FK_ETUDIANT_PAR_GROUPE foreign key (ID_GROUPE)
      references GROUPE (ID_GROUPE) on delete restrict on update restrict;

alter table ETUDIANT_PAR_GROUPE add constraint FK_ETUDIANT_PAR_GROUPE2 foreign key (ID_ETUDIANT)
      references UTILISATEUR (ID_UTI) on delete restrict on update restrict;

alter table EVALUATION add constraint FK_ENS_IN_EVAL foreign key (ID_PROFESSEUR)
      references UTILISATEUR (ID_UTI) on delete restrict on update restrict;

alter table EVALUATION add constraint FK_MATIERE_IN_EXAMEN foreign key (ID_MATIERE)
      references MATIERE_RESSOURCE (ID_MATIERE) on delete restrict on update restrict;

alter table EVALUATION add constraint FK_CLASSE_IN_EVAL foreign key (ID_CLASSE)
      references CLASSE (ID_CLASSE) on delete restrict on update restrict;

alter table FICHIER add constraint FK_DOSSIER_IN_FICHIER foreign key (ID_DOSSIER)
      references DOSSIER_P (ID_DOSSIER) on delete restrict on update restrict;

alter table GROUPE add constraint FK_CLASSE_IN_GROUPE foreign key (ID_CLASSE)
      references CLASSE (ID_CLASSE) on delete restrict on update restrict;

alter table GROUPE_PAR_COURS add constraint FK_GROUPE_PAR_COURS foreign key (ID_COURS)
      references COURS (ID_COURS) on delete restrict on update restrict;

alter table GROUPE_PAR_COURS add constraint FK_GROUPE_PAR_COURS2 foreign key (ID_GROUPE)
      references GROUPE (ID_GROUPE) on delete restrict on update restrict;

alter table MATIERE_PAR_COMP add constraint FK_MATIERE_PAR_COMP foreign key (ID_MATIERE)
      references MATIERE_RESSOURCE (ID_MATIERE) on delete restrict on update restrict;

alter table MATIERE_PAR_COMP add constraint FK_MATIERE_PAR_COMP2 foreign key (ID_COMP)
      references COMPETENCE (ID_COMP) on delete restrict on update restrict;

alter table MATIERE_PAR_ENS add constraint FK_MATIERE_PAR_ENS foreign key (ID_PROFESSEUR)
      references UTILISATEUR (ID_UTI) on delete restrict on update restrict;

alter table MATIERE_PAR_ENS add constraint FK_MATIERE_PAR_ENS2 foreign key (ID_MATIERE)
      references MATIERE_RESSOURCE (ID_MATIERE) on delete restrict on update restrict;

alter table NOTE add constraint FK_ETU_IN_NOTE foreign key (ID_ETUDIANT)
      references UTILISATEUR (ID_UTI) on delete restrict on update restrict;

alter table NOTE add constraint FK_EVAL_IN_NOTE foreign key (ID_EXAMEN)
      references EVALUATION (ID_EXAMEN) on delete restrict on update restrict;

alter table SEMESTRE add constraint FK_PARCOURS_IN_SEM foreign key (ID_PARCOURS)
      references PARCOURS (ID_PARCOURS) on delete restrict on update restrict;

alter table UTI_CONSULTE_DOSSIER add constraint FK_UTI_CONSULTE_DOSSIER foreign key (ID_DOSSIER)
      references DOSSIER_P (ID_DOSSIER) on delete restrict on update restrict;

alter table UTI_CONSULTE_DOSSIER add constraint FK_UTI_CONSULTE_DOSSIER2 foreign key (ID_CONSULTANT)
      references UTILISATEUR (ID_UTI) on delete restrict on update restrict;