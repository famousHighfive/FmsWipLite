# CAHIER DES CHARGES - SYSTÈME DE GESTION DES RESSOURCES HUMAINES

---

## 1. PRÉSENTATION GÉNÉRALE

### 1.1 Contexte du projet
L'entreprise souhaite développer un système de gestion des ressources humaines pour optimiser la gestion de son personnel et les affectations aux campagnes.

### 1.2 Objectifs
- Centraliser la gestion des informations du personnel
- Optimiser les processus d'affectation des ressources aux campagnes
- Améliorer la traçabilité des actions RH
- Fournir des outils de recherche et de reporting

### 1.3 Périmètre du projet
Le système couvre :
- La gestion des employés et de leurs informations personnelles
- La gestion des campagnes commerciales
- Le système d'affectation hiérarchique (Chef de Plateau > Superviseur > Téléconseiller)
- L'administration des utilisateurs et des droits d'accès

---

## 2. ACTEURS ET RÔLES

### 2.1 Administrateur Système
**Responsabilités :**
- Création et gestion des employés, des comptes utilisateurs et des campagnes 
- Attribution des rôles et permissions
- Configuration générale du système
- Supervision de toutes les activités

**Accès :** Complet sur toutes les fonctionnalités

### 2.2 Chef de Plateau (CP)
**Responsabilités :**
- Gestion des superviseurs de ses campagnes
- Validation des affectations de téléconseilleurs
- Création et gestion des modèles de planning
- Validation des plannings affectés
- Affectation des plannings aux superviseurs et téléconseilleurs
- Saisie des heures de ses superviseurs


**Accès :** Limité aux campagnes qui lui sont assignées

### 2.3 Superviseur (SUP)
**Responsabilités :**
- Gestion directe des téléconseilleurs
- Saisie des heures travaillées par ses téléconseilleurs
- Suivi quotidien des activités et du respect des plannings
- Remontée d'informations au Chef de Plateau


**Accès :** Limité à son équipe de téléconseilleurs

### 2.4 Téléconseiller (TC)
**Responsabilités :**
- Consultation de ses propres informations
- Consultation de son planning assigné
- Consultation de ses heures saisies et validées
- Mise à jour de certaines données personnelles

**Accès :** Consultation uniquement de ses données personnelles et de planning

---

## 3. FONCTIONNALITÉS PRINCIPALES

### 3.1 Gestion des Employés

#### 3.1.1 Création d'un employé
**Objectif :** Enregistrer un nouveau collaborateur dans le système

**Informations requises :**
- Identité : Nom, prénom, date de naissance
- Contact : Email, téléphone, adresse
- Professionnel : Date d'embauche, poste, salaire de base
- Statut : Actif, inactif, suspendu

**Processus :**
1. Saisie des informations personnelles
2. Attribution automatique d'un matricule unique
3. Définition du statut d'affectation (non affecté par défaut)
4. Validation et enregistrement

**Résultat :** L'employé est créé avec le statut "non affecté"

#### 3.1.2 Modification des informations
**Objectif :** Mettre à jour les données d'un employé existant

**Éléments modifiables :**
- Informations de contact
- Poste et salaire
- Statut (actif/inactif/suspendu)
- Adresse

**Restrictions :** 
- Le matricule ne peut pas être modifié

#### 3.1.3 Recherche et consultation
**Objectif :** Retrouver rapidement un employé et consulter ses informations

**Critères de recherche :**
- Nom, prénom
- Matricule
- Email
- Poste
- Statut d'affectation
- Campagne actuelle

**Filtres disponibles :**
- Par rôle (CP, SUP, TC)
- Par statut (actif, inactif, suspendu)
- Par statut d'affectation (affecté, non affecté)
- Par campagne

### 3.2 Gestion des Campagnes

#### 3.2.1 Création d'une campagne
**Objectif :** Définir une nouvelle campagne commerciale

**Informations requises :**
- Nom de la campagne
- Description détaillée
- Dates de début et fin prévues
- Statut (active, inactive, terminée)

**Processus :**
1. Saisie des informations de la campagne
2. Définition des dates de validité
3. Activation de la campagne
4. Possibilité d'affectation de ressources

#### 3.2.2 Gestion du cycle de vie
**États possibles :**
- **Active :** Campagne en cours, peut recevoir des affectations
- **Inactive :** Campagne suspendue temporairement
- **Terminée :** Campagne clôturée définitivement

**Transitions :**
- Activation/désactivation selon les besoins
- Clôture définitive avec archivage des données

### 3.3 Système d'Affectation Hiérarchique

#### 3.3.1 Principe général
Le système respecte une hiérarchie à trois niveaux :
1. **Chef de Plateau (CP)** ← affecté à une ou plusieurs campagnes
2. **Superviseur (SUP)** ← affecté à un CP sur une campagne spécifique
3. **Téléconseiller (TC)** ← affecté à un superviseur

#### 3.3.2 Affectation Chef de Plateau → Campagne
**Objectif :** Assigner un Chef de Plateau à une ou plusieurs campagnes

**Processus :**
1. Sélection du Chef de Plateau (statut : non affecté)
2. Choix de la ou des campagnes actives
3. Définition de la date de début d'affectation
4. Validation de l'affectation

**Règles métier :**
- Un CP peut être affecté à plusieurs campagnes simultanément
- Seules les campagnes actives peuvent recevoir des affectations
- L'affectation change le statut du CP en "affecté"

#### 3.3.3 Affectation Superviseur → Chef de Plateau/Campagne
**Objectif :** Assigner un Superviseur à un Chef de Plateau sur une campagne

**Processus :**
1. Sélection du Superviseur (statut : non affecté)
2. Choix du Chef de Plateau
3. Sélection de la campagne (parmi celles du CP)
4. Validation de l'affectation

**Règles métier :**
- Un superviseur ne peut être affecté qu'à une seule campagne à la fois
- Le CP doit être préalablement affecté à la campagne choisie
- L'affectation change le statut du superviseur en "affecté"

#### 3.3.4 Affectation Téléconseiller → Superviseur
**Objectif :** Assigner un ou plusieurs Téléconseilleurs à un Superviseur

**Processus :**
1. Sélection du Superviseur (déjà affecté)
2. Choix des Téléconseilleurs (statut : non affecté)
3. Affectation automatique à la campagne du superviseur
4. Validation des affectations

**Règles métier :**
- Plusieurs TC peuvent être affectés au même superviseur
- Un TC ne peut être affecté qu'à un seul superviseur à la fois
- L'affectation hérite automatiquement de la campagne du superviseur

#### 3.3.5 Libération et réaffectation
**Libération d'une ressource :**
- Possibilité de libérer une ressource de son affectation actuelle
- Retour au statut "non affecté"
- Impact en cascade (libérer un SUP libère automatiquement ses TC)

**Réaffectation :**
- Changement d'affectation sans passer par l'état "non affecté"
- Traçabilité des changements avec dates et motifs

### 3.4 Gestion des Ressources

#### 3.4.1 Vue des ressources non affectées
**Objectif :** Visualiser tous les employés disponibles pour affectation

**Informations affichées :**
- Identité complète
- Poste et rôle
- Date d'embauche
- Dernière affectation (si applicable)

**Actions possibles :**
- Affectation directe selon le rôle
- Consultation du détail
- Modification des informations

#### 3.4.2 Vue des ressources affectées
**Objectif :** Visualiser l'organisation hiérarchique actuelle

**Présentation :**
- Vue arborescente par campagne
- Affichage de la hiérarchie CP > SUP > TC

**Actions possibles :**
- Réaffectation
- Libération
- Consultation des détails d'affectation


### 3.5 Gestion des Plannings

#### 3.5.1 Création de modèles de planning
**Objectif :** Définir des modèles de planning réutilisables pour organiser le travail

**Informations requises :**
* Nom du modèle de planning
* Description (optionnelle)
* Heures de travail prévues pour chaque jour de la semaine :

  * Lundi
  * Mardi
  * Mercredi
  * Jeudi
  * Vendredi
  * Samedi
  * Dimanche
* Nombre total d’heures hebdomadaires (calculé automatiquement)

**Processus de création :**
1. Saisie du nom du modèle
2. Définition des heures de travail pour chaque jour de la semaine
3. Calcul automatique du total hebdomadaire
4. Vérification des données
5. Enregistrement du modèle

**Règles métier** :

* Un modèle de planning représente **une semaine type**
* Les heures sont exprimées en valeur numérique (ex : 8, 7.5, 0)
* Le total hebdomadaire est calculé automatiquement à partir des jours
* Un modèle peut être réutilisé pour plusieurs employés

#### 3.5.2 Affectation de planning aux superviseurs
**Objectif :** Assigner un modèle de planning à un superviseur pour une période donnée

**Processus d'affectation :**
1. Sélection du superviseur (parmi ceux affectés au CP)
2. Choix du modèle de planning
3. Définition de la période d'application (dates début/fin)
4. Validation de l'affectation

**Règles métier :**
- Seul le Chef de Plateau peut affecter des plannings
- Un superviseur ne peut avoir qu'un seul planning actif par période
- Le planning doit être validé avant d'être effectif
- Les périodes de planning ne peuvent pas se chevaucher

#### 3.5.3 Affectation de planning aux téléconseilleurs
**Objectif :** Assigner un modèle de planning à un téléconseiller

**Processus d'affectation :**
1. Sélection du téléconseiller (parmi ceux de l'équipe du CP)
2. Choix du modèle de planning
3. Définition de la période d'application
4. Validation de l'affectation

**Règles métier :**
- Seul le Chef de Plateau peut affecter des plannings aux TC
- Un téléconseiller ne peut avoir qu'un seul planning actif par période
- Le planning doit être validé avant d'être effectif

#### 3.5.4 Validation des plannings
**Objectif :** Valider les plannings affectés pour les rendre effectifs

**Processus de validation :**
1. Consultation de la liste des plannings en attente
2. Vérification de la cohérence des affectations
3. Validation individuelle ou en lot
4. Notification automatique aux concernés

**États des plannings :**
- **En attente :** Planning affecté mais non validé
- **Validé :** Planning approuvé et effectif
- **Suspendu :** Planning temporairement désactivé
- **Terminé :** Planning arrivé à échéance

**Effets de la validation :**
- Le planning devient visible pour les superviseurs et téléconseilleurs
- La saisie des heures devient possible
- Les notifications sont envoyées aux utilisateurs concernés

### 3.6 Gestion de la Saisie des Heures

#### 3.6.1 Saisie des heures par les superviseurs (pour leurs téléconseilleurs)
**Objectif :** Permettre aux superviseurs de saisir les heures travaillées par leurs téléconseilleurs

**Processus de saisie :**
1. Sélection de la période (généralement une semaine)
2. Affichage de la liste des téléconseilleurs de l'équipe
3. Saisie des heures par jour et par téléconseiller
4. Comparaison automatique avec le planning prévu
5. Sauvegarde des données saisies

**Informations saisies :**
- Heures d'arrivée et de départ par jour
- Temps de pause effectif
- Heures supplémentaires éventuelles
- Absences et leurs motifs
- Commentaires particuliers

**Contrôles automatiques :**
- Vérification de la cohérence des horaires
- Calcul automatique du total d'heures
- Détection des écarts par rapport au planning
- Alertes en cas d'anomalies

#### 3.6.2 Saisie des heures par le Chef de Plateau (pour ses superviseurs)
**Objectif :** Permettre au CP de saisir les heures travaillées par ses superviseurs

**Processus identique à la saisie pour les TC :**
1. Sélection de la période
2. Affichage de la liste des superviseurs de l'équipe
3. Saisie des heures par jour et par superviseur
4. Validation et sauvegarde

**Spécificités pour les superviseurs :**
- Prise en compte des heures de management
- Gestion des astreintes éventuelles
- Suivi des réunions et formations

#### 3.6.3 Validation des heures saisies
**Objectif :** Valider les heures saisies pour les rendre définitives

**Processus de validation :**
1. Consultation des heures saisies par période
2. Vérification de la cohérence des données
3. Validation par employé ou en lot
4. Verrouillage des données validées

**Règles de validation :**
- Seul le Chef de Plateau peut valider les heures
- Les heures validées ne peuvent plus être modifiées
- La validation déclenche les calculs de paie
- Notification automatique aux employés concernés

#### 3.6.4 Consultation des heures par les téléconseilleurs
**Objectif :** Permettre aux TC de consulter leurs heures une fois validées

**Fonctionnalités de consultation :**
- Affichage du planning assigné
- Visualisation des heures saisies par le superviseur
- Comparaison planning prévu / heures réelles
- Historique des périodes précédentes
- Calcul des heures supplémentaires

**Informations affichées :**
- Planning de référence
- Heures travaillées par jour
- Total hebdomadaire/mensuel
- Écarts par rapport au planning
- Statut de validation

**Restrictions :**
- Consultation uniquement (pas de modification)
- Accès limité aux données personnelles
- Historique limité aux 12 derniers mois

---

## 4. INTERFACES UTILISATEUR

### 4.1 Tableau de bord
**Contenu :**
- Indicateurs clés (nombre d'employés, affectations)
- Alertes et notifications
- Accès rapide aux fonctions principales

### 4.2 Gestion des employés
**Pages principales :**
- Liste des employés avec filtres et recherche
- Formulaire de création/modification
- Fiche détaillée d'un employé


### 4.3 Gestion des affectations
**Pages principales :**
- Vue des ressources non affectées
- Vue hiérarchique des affectations
- Formulaires d'affectation par niveau
- Historique des mouvements

### 4.4 Gestion des campagnes
**Pages principales :**
- Liste des campagnes avec statuts
- Formulaire de création/modification
- Détail d'une campagne avec affectations

### 4.5 Gestion des plannings
**Pages principales :**
- Liste des modèles de planning avec statuts
- Formulaire de création/modification de modèle
- Interface d'affectation des plannings aux superviseurs
- Tableau de bord de validation des plannings
- Historique des affectations de planning

### 4.6 Saisie et suivi des heures
**Pages principales :**
- Interface de saisie des heures pour superviseurs (saisie TC)
- Interface de saisie des heures pour Chef de Plateau (saisie SUP)
- Tableau de validation des heures saisies
- Rapports d'écarts planning/réalisé
- Historique des heures par employé

---

## 5. RÈGLES DE GESTION

### 5.1 Règles d'affectation
- Un employé ne peut avoir qu'une seule affectation active à la fois
- Les affectations respectent la hiérarchie définie
- Seules les campagnes actives peuvent recevoir des affectations
- La libération d'un niveau supérieur entraîne la libération des niveaux inférieurs

### 5.2 Règles de sécurité
- Chaque action est tracée avec l'utilisateur et la date
- Les droits d'accès sont contrôlés selon le rôle
- Les données sensibles sont protégées
- L'historique des modifications est conservé

### 5.3 Règles de validation
- Les informations obligatoires doivent être saisies
- Les formats de données sont contrôlés (email, téléphone, dates)
- Les doublons sont détectés et signalés
- Les incohérences sont bloquées

### 5.4 Règles de planning
- Un employé ne peut avoir qu'un seul planning actif par période
- Les plannings doivent être validés par le Chef de Plateau avant d'être effectifs
- Les périodes de planning ne peuvent pas se chevaucher
- Seuls les employés affectés peuvent recevoir un planning

### 5.5 Règles de saisie des heures
- Seuls les superviseurs peuvent saisir les heures de leurs téléconseilleurs
- Seul le Chef de Plateau peut saisir les heures de ses superviseurs
- Les heures ne peuvent être saisies que si un planning validé existe
- Les heures validées ne peuvent plus être modifiées
- La validation des heures est obligatoire pour le calcul de paie

---
