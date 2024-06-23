# Dream Glimpse

Dream Glimpse est une application web développée avec Symfony 6.4 qui permet aux utilisateurs de partager leurs rêves et de générer automatiquement des illustrations pour ces rêves (fonctionnalité non implémentée dans cette version MVP).

![Capture d'écran de la page d'accueil](data/screenshots/accueil.webp)

## Fonctionnalités

- Inscription et authentification des utilisateurs
- Publication de rêves avec upload d'images
- Affichage des rêves récents sur la page d'accueil
- Pagination des listes de rêves et d'utilisateurs
- Gestion des profils utilisateurs
- Système de newsletter

## Prérequis

- PHP 8.1 ou supérieur
- Composer
- Symfony CLI
- MySQL

## Installation

1. Clonez le dépôt :
   ```
   git clone https://github.com/timlapov/dream-glimpse.git
   cd dream-glimpse
   ```

2. Installez les dépendances :
   ```
   composer install
   ```

3. Configurez votre base de données et le server SMTP dans le fichier `.env.local` :
   ```
   DATABASE_URL="mysql://username:password@127.0.0.1:3306/dream_glimpse?serverVersion=8.0"
   MAILER_DSN=smtp://localhost:1025
   ```

4. Créez la base de données :
   ```
   php bin/console doctrine:database:create
   ```

5. Appliquez les migrations :
   ```
   php bin/console doctrine:migrations:migrate
   ```

6. Chargez les fixtures (données de test) :
   ```
   php bin/console doctrine:fixtures:load
   ```

7. Lancez le serveur de développement (vous devez également lancer votre serveur SMTP pour tester la réception des courriels) :
   ```
   symfony serve --no-tls
   ```

L'application devrait maintenant être accessible à l'adresse `http://localhost:8000`. Vérifiez la sortie de la ligne de commande (vous pouvez avoir un port différent)

## Utilisation

### Page d'accueil
![Capture d'écran de la page d'accueil](data/screenshots/accueil.webp)

La page d'accueil affiche les derniers rêves publiés et permet aux utilisateurs de s'inscrire ou de se connecter. En bas de la page, une option permet de s'abonner aux newsletters. Lorsque vous vous abonnez, un message de bienvenue est envoyé à votre adresse email.

### Publication d'un rêve
![Capture d'écran de la page de publication](data/screenshots/addPost.webp)

Les utilisateurs connectés peuvent publier leurs rêves en fournissant une description et en téléchargeant une image.

### Affichage de la publication
![Capture d'écran de la page de publication](data/screenshots/post.webp)

La publication peut être visualisée dans son intégralité. Si l'utilisateur a des droits d'administrateur ou est l'auteur de la publication, il peut également voir les boutons "supprimer" et "modifier".

### Liste des utilisateurs
![Capture d'écran de la liste des utilisateurs](data/screenshots/users.webp)

Une page listant tous les utilisateurs inscrits est disponible.Les administrateurs disposent d'un bouton pour modifier les utilisateurs (en cliquant dessus, ils peuvent modifier leurs données ou les supprimer). Ce bouton est également disponible pour l'utilisateur connecté.

### Profil utilisateur
![Capture d'écran du profil utilisateur](data/screenshots/profile.webp)

Chaque utilisateur a un profil qui affiche ses informations et ses rêves publiés. Il peut modifier ses coordonnées en cliquant sur l'icône du stylo à côté de son nom.

## Structure du projet

- `assets/`: Contient les fichiers front-end (JavaScript, CSS, images) et les configurations de Webpack.
- `bin/`: Contient les fichiers binaires utilisés par Symfony, tels que le console.
- `config/`: Contient les fichiers de configuration de l'application Symfony.
- `data/`: Peut contenir des fichiers de données, captures d'écran pour readme.md.
- `migrations/`: Contient les fichiers de migration de la base de données pour Doctrine.
- `public/`: Contient les fichiers publics accessibles via le navigateur (images, CSS, JS, etc.).
- `src/`: Dossier source contenant le code PHP du projet
   - `Controller/`: Contient les contrôleurs de l'application.
   - `DataFixtures/`: Contient les fixtures de données pour le peuplement de la base de données.
   - `Entity/`: Définit les entités Doctrine.
   - `EventListener/`: Contient les écouteurs d'événements pour la gestion des événements spécifiques de l'application.
   - `Form/`: Contient les classes de formulaires.
   - `Newsletter/`: Contient la logique liée aux newsletters.
   - `Repository/`: Contient les repository pour l'accès aux données.
   - `Security/`: Contient la logique de sécurité (voters, etc.).
   - `Service/`: Services personnalisés (ex: FileUploader).
   - `Kernel.php`: Le point d'entrée de l'application Symfony.
- `templates/`: Contient les templates Twig.
- `.env`: Fichier de configuration des variables d'environnement.
- `composer.json`: Fichier de configuration de Composer, contenant les dépendances du projet.
- `composer.lock`: Fichier généré par Composer, verrouillant les versions exactes des dépendances installées.
- `README.md`: Fichier de documentation du projet.
- `symfony.lock`: Fichier généré par Symfony, verrouillant les versions exactes des composants installés.

## Préparations de la version

### Fonctionnalités en cours de développement

1. **Génération d'images avec l'IA via une API externe**
- Intégration d'un service externe pour générer des illustrations basées sur les descriptions de rêves fournies par les utilisateurs.
- Utilisation d'API pour obtenir des images uniques et personnalisées.

2. **Système de likes**
- Mise en place d'un système de likes permettant aux utilisateurs d'aimer les rêves partagés.
- Affichage du nombre de likes pour chaque rêve et possibilité de retirer un like.

## Contributions

Les contributions sont les bienvenues ! N'hésitez pas à ouvrir une issue ou à soumettre une pull request.

## Licence

Ce projet est sous licence [Creative Commons Attribution-NonCommercial 4.0 International License](https://creativecommons.org/licenses/by-nc/4.0/).
