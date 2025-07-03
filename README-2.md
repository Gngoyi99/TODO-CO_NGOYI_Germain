# ToDo - Application Symfony

ToDo est une application web développée avec Symfony permettant aux utilisateurs de gérer leurs tâches quotidiennes. L’application propose un système d’authentification sécurisé, la gestion des utilisateurs et des tâches (création, édition, suppression, marquage comme terminée).

## Prérequis

Avant d’installer le projet, assurez-vous d’avoir les outils suivants installés sur votre machine :

- PHP >= 8.1
- Composer >= 2.5
- Symfony CLI
- Node.js >= 18 (pour la gestion des assets)
- Yarn ou npm
- MySQL >= 8.0 ou MariaDB >= 10.5
- Docker (optionnel pour une installation via conteneurs)

## Installation

Clonez le projet depuis le dépôt GitHub :

```bash
git clone https://github.com/Gngoyi99/TODO-CO_NGOYI_Germain.git
cd TODO-CO_NGOYI_Germain
```

Installez les dépendances PHP avec Composer :

```bash
composer install
```

Installez les dépendances front-end (si le projet utilise Webpack Encore) :

```bash
yarn install
yarn encore dev
```
ou avec npm :
```bash
npm install
npm run dev
```

## Configuration

Copiez le fichier d’exemple `.env` et configurez vos paramètres d’environnement :

```bash
cp .env .env.local
```

Mettez à jour les variables suivantes dans `.env.local` :

```
DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/todo_db"
```

## Base de données

Créez la base de données et lancez les migrations :

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

Chargez les fixtures pour créer un utilisateur et des données de test :

```bash
php bin/console doctrine:fixtures:load
```

## Lancer l’application

Lancez le serveur de développement Symfony :

```bash
symfony server:start
```

Ou via PHP intégré :

```bash
php -S localhost:8000 -t public
```

Accédez ensuite à [http://127.0.0.1:8000](http://127.0.0.1:8000) dans votre navigateur.

## Identifiants par défaut

Après avoir chargé les fixtures, vous pouvez vous connecter avec les identifiants suivants :

- **Nom d’utilisateur** : admin
- **Mot de passe** : password

## Tests

Lancez les tests unitaires et fonctionnels avec PHPUnit :

```bash
./vendor/bin/phpunit
```

Générez le rapport de couverture de code :

```bash
./vendor/bin/phpunit --coverage-html var/coverage
```

## Docker (optionnel)

Vous pouvez lancer le projet avec Docker :

```bash
docker-compose up -d --build
```

Cela démarre :
- PHP 8.1 avec Apache
- MySQL
- phpMyAdmin (accessible sur [http://localhost:8080](http://localhost:8080))

## Fonctionnalités

- Authentification et gestion des utilisateurs
- CRUD complet pour les tâches
- Marquage des tâches comme terminées
- Interface responsive (Bootstrap 5)
- Sécurité renforcée avec encodage des mots de passe (bcrypt)
- Tests unitaires et fonctionnels
- Code optimisé et conforme aux standards PSR

## Contribution

1. Fork le projet
2. Crée une branche (`git checkout -b feature/NouvelleFeature`)
3. Commit tes modifications (`git commit -am 'Ajout d’une nouvelle fonctionnalité'`)
4. Push la branche (`git push origin feature/NouvelleFeature`)
5. Ouvre une Pull Request

## Licence

Ce projet est sous licence MIT. Voir le fichier [LICENSE](LICENSE) pour plus de détails.