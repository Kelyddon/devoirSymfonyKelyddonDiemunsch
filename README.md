Commandes pour l'installation et le lancement du projet Symfony
=================================================
Prérequis
---------
-   PHP 8.1 ou supérieur
-  Composer
-  Un serveur de base de données (MySQL)
-  Symfony

Installation
------------
1.  Cloner le dépôt GitHub:
git clone
2. cd nom-du-projet
3. Installer les dépendances avec Composer:
composer install
4. Modifier les paramètres de connexion à la base de données du fichier .env 
5. Puis créer la base de données :
php bin/console doctrine:database:create
6. Exécuter les migrations pour créer les tables:
php bin/console doctrine:migrations:migrate
7. Charger les fixtures pour peupler la base de données avec des données initiales:
php bin/console doctrine:fixtures:load
8. Lancer le serveur de développement Symfony:
symfony server

Il y a 2 tables : User et Book.

Il y a une seed de fixtures au niveau de la table User:
-   Un admin avec le rôle ROLE_AUTHOR (email: admin@author.com, mot de passe: adminpass).
-   Un utilisateur classique avec le rôle ROLE_USER (email: user@user.com, mot de passe: userpass).

Il est possible de regarder les livres sans avoir besoin de se connecter.
Pour pouvoir ajouter, modifier ou supprimer, il faut être connecté.
Le ROLE_USER peut écrire des livres et modifier ou supprimer les si c'est lui qui les a écrits.
Le ROLE_AUTHOR peut écrire des livres ainsi que modifier ou supprimer tous les livres.

Le code du projet a été crée via le framework de ce qu'on avait fait en cours.
Plusieurs choses ont été modifiée à partir de celui-ci.
Il y a eu suppressions des anciennes tables telle que post et category qui ont été remplacées par book.
Il y a eu utilisations de l'ia pour débugage et connections de certaines parties du code.
Les commandes vue en cours ont été utilisées pour la création de l'entité Book.

L'ia n'a pas été utilisée pour rédiger le README.md.
