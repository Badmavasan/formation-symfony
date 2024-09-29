# Guide d'installation de Symfony pour Windows et Mac

## Installation de Composer

### Windows

1. Téléchargez le fichier d'installation Composer-Setup.exe depuis https://getcomposer.org/download/
2. Exécutez le fichier téléchargé et suivez les instructions d'installation
3. Assurez-vous que l'option "Add to PATH" est cochée pendant l'installation

### Mac

1. Ouvrez le Terminal
2. Exécutez les commandes suivantes :

```
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === 'dac665fdc30fdd8ec78b38b9800061b4150413ff2e3b6f88543c636f7cd84f6db9189d43a81e5503cda447da73c7e5b6') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php
php -r "unlink('composer-setup.php');"
```

Déplacez composer.phar vers un répertoire dans votre PATH :

```
sudo mv composer.phar /usr/local/bin/composer
```

## Configuration de PHP

### Windows

1. Si vous avez déjà XAMPP installé :
  - Ajoutez le chemin du dossier PHP de XAMPP à la variable d'environnement PATH (par exemple, C:\xampp\php)


2. Si vous n'avez pas XAMPP :
  - Téléchargez PHP depuis https://windows.php.net/download/
  - Extrayez le contenu dans un dossier (par exemple, C:\php)
  - Ajoutez ce dossier à la variable d'environnement PATH

Pour ajouter PHP à la variable PATH :

1. Recherchez "Modifier les variables d'environnement système" dans le menu Démarrer
2. Cliquez sur "Variables d'environnement"
3. Sous "Variables système", trouvez PATH et cliquez sur "Modifier"
4. Cliquez sur "Nouveau" et ajoutez le chemin du dossier PHP
5. Cliquez sur "OK" pour fermer toutes les fenêtres


### Mac

1. Si vous utilisez Homebrew, installez PHP avec :
  ```
  brew install php
  ```
2. Sinon, téléchargez PHP depuis https://www.php.net/downloads.php

PHP sera automatiquement ajouté à votre PATH si vous utilisez Homebrew.

## Installation de Symfony

1. Ouvrez un terminal (Invite de commandes pour Windows, Terminal pour Mac)

2. Installez Symfony CLI :

### Windows

```
scoop install symfony-cli
```

Pour installer scoop : https://scoop.sh/

- Ouvrir un Powershell Terminal 
- saisir les commandes suivantes : 

```
Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser
Invoke-RestMethod -Uri https://get.scoop.sh | Invoke-Expression
```

### Mac

```
brew install symfony-cli/tap/symfony-cli
```

## Création d'un projet Symfony

1. Naviguez vers le dossier où vous souhaitez créer votre projet
2. Exécutez la commande suivante :

```
symfony new my_project_name --webapp
```
3. Attendez que Symfony crée votre projet

## Lancement du serveur de développement

1. Naviguez dans le dossier de votre projet :

```
cd my_project_name
```

2. Lancez le serveur de développement :

```
symfony server:start
```

3. Ouvrez votre navigateur et accédez à http://localhost:8000 pour voir votre application Symfony en action.

