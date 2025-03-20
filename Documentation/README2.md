# Formulaire d'Entrée en Relation Professionnel

## Description
Ce projet est une application développée avec **Symfony 6.4** et **PHP 8.1**, permettant de faciliter l'entrée en relation des professionnels auprès des conseillers. 

Le conseiller envoie un email contenant un lien vers le formulaire, lequel intègre son matricule chiffré. Le prospect renseigne ensuite ses informations dans le formulaire, et après soumission, un PDF récapitulatif des données est généré et envoyé au conseiller.

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
