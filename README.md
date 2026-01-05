---
marp: true
title: Mini Projet – Gestion des Annonces Immobilières
author: Ali Essamrachi
theme: default
paginate: true
---

# Mini Projet  
## Plateforme de Gestion d’Annonces Immobilières

**Fonctionnalités clés :**
- CRUD des biens immobiliers
- Recherche et filtrage dynamiques
- Interface publique + back-office admin

---

## 1. Choix du Sujet

### Sujet du projet
Développement d’une mini plateforme de **gestion d’annonces immobilières**.

### Motivation
- Sujet concret et professionnel
- Très répandu dans le domaine web
- Permet de pratiquer les bases essentielles du full-stack

*Ce projet sert de test technique pour valider la maîtrise des opérations CRUD et de la recherche dynamique.*

---

## 2. Contexte

### Contexte pédagogique
Ce projet technique a pour objectif :
- D’appliquer les connaissances acquises en développement web
- De valider notre compréhension des concepts fondamentaux
- De mettre en pratique une démarche de développement structurée

### Processus de développement
- Méthodologie adoptée : **2TUP (Two-Track Unified Process)**
- Séparation claire entre :
  - Analyse & conception
  - Implémentation & validation

---

![Schéma du processus 2TUP](./images/2tup.png)

---

## 3. Les technologies à utiliser 

1 Base de données : Mysql
2 Framework : laravel
3 Architecture N-tier : Services 
4 Architecture : MVC
5 Moteur de vues : blade 
6 Ajax
7 Upload imges
8 laravel multilangue
9 Vite
10 Preline Ui library
11 lucide library
12 Tailwindcss

---
## 4.  

---

## 5. Conception

### Architecture générale
- **2 pages publiques**
  - Liste des propriétés
- **1 page admin**
  - Gestion CRUD

### Choix techniques
- **UI** : Preline (composants prêts à l’emploi)
- **Recherche** : AJAX (sans rechargement de page)
- **Création** : Modal (meilleure UX)
- **Présentation** : Marp (Markdown → Slides)

---

## Conception – Interface Utilisateur

### Page publique (Properties)
- Liste des annonces sous forme de cartes
- Barre de recherche dynamique
- Filtres instantanés

### Page admin
- Tableau des propriétés
- Bouton "Ajouter"
- Actions : Modifier / Supprimer

*L’interface privilégie la simplicité et la lisibilité.*

---

## Conception – Flux CRUD

1. L’admin ouvre le modal de création
2. Saisie des informations du bien
3. Envoi AJAX vers le serveur
4. Mise à jour instantanée de la liste
5. Feedback visuel (succès / erreur)

---

## Conclusion

### Résumé
- Projet simple mais réaliste
- Validation des bases du développement web
- Approche professionnelle et structurée

### Perspectives d’évolution
- Authentification admin
- Upload d’images
- Pagination
- API REST

---

## Merci pour votre attention
Questions ?