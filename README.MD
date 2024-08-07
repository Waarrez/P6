### Prérequis :
- PHP 8
- MYSQL
- Composer
- Symfony CLI

## INITIALISATION

- Télécharger le projet avec la commande : ``` git clone https://github.com/Waarrez/P6.git ```
- Dans le dossier du projet et faire la commande suivante : ``` composer install ```

- Insérer le fichier snowtricks.sql qui se trouve dans le dossier sql dans phpmyadmin
- Ensuite fait un copier coller du .env et renommer le en .env.local et connecter votre BDD avec cette ligne ! ``` DATABASE_URL=mysql://root:@localhost:3306/P6?charset=utf8mb4 ```
- Importer ensuite la structure de base de donnée en faisant ``` php bin/console d:m:m --no-interaction ```
- Démarrer le serveur avec la commande suivante : ``` symfony serve -d ```
- Suivez les étapes si dessous

## Serveur MAIL
- Pour bien recevoir les mails vous devrez exécuter cette commande: ``` php bin/console messenger:consume async --time-limit=3600 ```
- Et mettre dans le .env.local cette ligne : ``` MAILER_DSN=gmail://email:password@smtp.gmail.com: ```
- Pour des raisons de sécurités je vous laisse insérer votre email ainsi que le mot de passe d'application disponible sur votre compte google.

## INSCRIPTION
- Vous devez vous rendre dans l'onglet INSCRIPTION, remplir les différents champs (veuillez à ce que l'email est valide car un email de confirmation sera envoyé)
- Ensuite rendez vous dans votre boîte mail et confirmer votre compte
- Connectez vous et ajouter, modifier et commenter les différentes figures présentes !

[![PHP Version](https://img.shields.io/badge/php-8.0-blue)](https://www.php.net/releases/8.0/en.php)
![Composer Version](https://img.shields.io/badge/Composer-2.6.6-blue)
[![Maintainability](https://api.codeclimate.com/v1/badges/79025237c78b49081758/maintainability)](https://codeclimate.com/github/Waarrez/P6/maintainability)

