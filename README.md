# AlterVault

AlterVault est un outil qui permet de consulter sa collection de carte du TCG Altered. 
Il vous permet de voir facilement si vous avez le playset d'une carte, combien il vous en manque
ou encore combien vous avez de carte en trop par rapport au playset.

## Technologies utilisées

- PHP 8.4
- Node 22
- Laravel 12
- MariaDB

## Installation du projet

<details>
<summary>Avec Docker (<a href="https://laravel.com/docs/12.x/sail">Sail</a>)</summary>

1. Récupération du projet :
    ```bash
    git clone git@github.com:romainrhd/AlterVault.git
    ```
2. On se déplace dans le projet :
    ```bash
    cd AlterVault/
    ```
3. On crée le fichier .env et on le remplit avec les bonnes informations :
   ```bash
   cp .env.example .env
   ```
   Il faut bien penser à mettre à jour les valeurs suivantes :
    - LOG_CHANNEL (Mettre daily pour avoir un fichier de log par jour)
4. Installation des dépendances :
    ```bash
    docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php84-composer:latest \
    composer install --ignore-platform-reqs
    ```
5. Installation de la base de données :
    ```bash
    sail artisan migrate
    ```
6. Création du lien symbolique pour les images :
    ```bash
    sail artisan storage:link
    ```
7. Création du lien symbolique pour les images :
    ```bash
    sail artisan key:generate
    ```
8. Installation des dépendances front :
    ```bash
    sail npm install
    ```
9. Lancement du projet :
    ```bash
    sail npm run dev
    ```

</details>
<details>
<summary>Sans Docker</summary>

1. Récupération du projet :
    ```bash
    git clone git@github.com:romainrhd/AlterVault.git
    ```
2. On se déplace dans le projet :
    ```bash
    cd AlterVault/
    ```
3. On crée le fichier .env et on le remplit avec les bonnes informations :
    ```bash
    cp .env.example .env
    ```
   Il faut bien penser à mettre à jour les valeurs suivantes :
    - LOG_CHANNEL (Mettre daily pour avoir un fichier de log par jour)
4. Installation des dépendances :
    ```bash
    composer install
    ```
5. Installation de la base de données :
    ```bash
    php artisan migrate --seed
    ```
6. Création du lien symbolique pour les images :
   ```bash
   php artisan storage:link
    ```
7. Création de la clé Laravel :
   ```bash
   php artisan key:generate
    ```
8. Installation des dépendances front :
    ```bash
    npm install
    ```
9. Lancement du projet :
    ```bash
    npm run dev
    ```

</details>
