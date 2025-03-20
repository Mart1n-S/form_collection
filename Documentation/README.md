# Formulaire d'Entrée en Relation pour les Associations

## Description
Ce projet est une application développée avec **Symfony 6.4** et **PHP 8.1**, permettant aux associations de faciliter leur entrée en relation avec les conseillers en fournissant des informations sur l'association, ses membres et les pièces justificatives nécessaires.


Ce projet est une application développée avec **Symfony 6.4** et **PHP 8.1**, permettant aux associations de faciliter leur entrée en relation avec les conseillers en fournissant des informations sur l'association, ses membres et les pièces justificatives nécessaires.

Le conseiller envoie un email contenant un lien vers le formulaire, lequel intègre son matricule chiffré. 
Le prospect renseigne ensuite les informations dans le formulaire, et après soumission, deux PDF sont générés : un récapitulatif complet et un autre contenant les montants des associations.

Ensuite, ces PDF ainsi que toutes les pièces justificatives sont compressés dans une archive ZIP et stockés sur le serveur. Un email est envoyé au conseiller avec un lien pour télécharger l'archive ZIP contenant toutes les informations. Le ZIP est conservé sur le serveur pendant 30 jours avant d'être automatiquement supprimé dans le cadre de la purge. Après le téléchargement par le conseiller, le ZIP est immédiatement supprimé du serveur. 


## Stack Technique
- **Framework :** Symfony 6.4
- **Langage :** PHP 8.1
- **Base de données :** MySQL
- **Gestion des dépendances :** Composer

## Installation

### 1. Cloner le dépôt
```bash
git clone https://github.com/ton-repo/mon-projet.git
cd mon-projet
```

### 2. Configurer l'environnement
Copier le fichier `.env` en `.env.local` et modifier les valeurs nécessaires, notamment :
```bash
APP_ENV=dev
APP_DEBUG=1
```

### 3. Installer les dépendances backend
```bash
composer install
```

### 4. Lancer les migrations
```bash
symfony console d:m:m
```

### 5. Charger les fixtures
```bash
symfony console d:f:l
```

### 6. Lancer le serveur Symfony
```bash
symfony server:start
```

## Utilisation
L'application est accessible à l'adresse : `http://127.0.0.1:8000`
