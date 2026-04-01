# Documentation technique - Enigmatik

## 1. Vue d ensemble
Enigmatik est une application web Symfony 7.3 orientee jeu pedagogique. L architecture suit un modele MVC classique:
- Controleurs Symfony pour la logique HTTP.
- Entites Doctrine pour le modele de donnees.
- Vues Twig (Bootstrap + classes Tailwind prefixees tw-).
- Interactivite front via Stimulus.

## 2. Stack technique
- Langage: PHP 8.2+
- Framework: Symfony 7.3
- ORM: Doctrine ORM + Doctrine Migrations
- Base de donnees: PostgreSQL (configuration par defaut)
- Frontend:
  - Twig
  - Bootstrap 5.3
  - Tailwind via CDN (prefix tw-)
  - Symfony UX Stimulus + Turbo
- Tests: PHPUnit
- Conteneurisation: compose.yaml (service database PostgreSQL)

## 3. Structure du projet
- src/Controller: controleurs HTTP
- src/Entity: modeles Doctrine
- src/Form: definitions de formulaires Symfony
- src/Repository: requetes d acces aux donnees
- src/DataFixtures: jeux de donnees de demonstration
- templates/: vues Twig
- assets/controllers: controleurs Stimulus
- migrations/: scripts de migration schema
- tests/: tests fonctionnels

## 4. Architecture applicative
### 4.1 Controleurs principaux
- HomeController: page d accueil et reprise partie par session equipe.
- SecurityController: login/logout.
- AdminController: tableau de bord enseignant, suppression historique parties.
- JeuController: affichage du jeu, validation code final, configuration globale jeu.
- EnigmeController: CRUD enigmes, verification reponse, activation/desactivation.
- EquipeController: creation et suivi equipes.
- UserController: CRUD utilisateurs + pages rolees.
- TypeController, VignetteController, AvatarController, ParametreController: CRUD de referentiels.

### 4.2 Couches
- Presentation: Twig + classes Bootstrap/Tailwind.
- Domaine: entites Doctrine et regles dans controleurs/formulaires.
- Persistance: repositories Doctrine.
- Interaction front: Stimulus pour actions dynamiques (board, game, avatar preview, CSRF helper).

## 5. Securite
Configuration principale dans security.yaml:
- Provider base sur App\Entity\User (identifiant email).
- Authentification form_login (route app_login).
- Logout route app_logout.
- Hierarchie des roles:
  - ROLE_ADMIN > ROLE_PROF > ROLE_USER
- Access control par patterns de routes.

Mesures en place:
- Hash automatique des mots de passe.
- Token CSRF sur formulaires.
- Verification CSRF sur actions sensibles POST.

Point d attention:
- Certaines regles access_control ciblent des prefixes differents des routes reelles (ex: /user vs /utilisateur). Une revue de coherence est recommandee.

## 6. Modele de donnees (Doctrine)
Entites principales:
- User (tbl_user): email unique, roles, password.
- Equipe (tbl_equipe): nom, position, enigmeActuelle, startedAt, finishedAt, avatar.
- Avatar (tbl_avatar): nom, image.
- Jeu (tbl_jeu): titre, messageDeBienvenue, imageBienvenue, codeFinal, active.
- Enigme (tbl_enigme): ordre, titre, consigne, codeSecret, codeReponse, choices (JSON), lien, solution, active, type, vignette, jeu.
- Type (tbl_type): libelle.
- Vignette (tbl_vignette): image, information.
- Parametre: libelle, valeur, choix (array), lien optionnel vers jeu.

Relations clefs:
- Equipe ManyToOne Avatar.
- Jeu OneToMany Parametre.
- Jeu OneToMany Enigme.
- Enigme ManyToOne Type.
- Enigme ManyToOne Vignette.
- Enigme ManyToOne Jeu.

## 7. Flux metier importants
### 7.1 Demarrage d une partie
1. Creation equipe (/equipe/creer).
2. Persist equipe avec position et enigme initiale.
3. Sauvegarde de equipe_id en session.
4. Redirection vers /jeu avec contexte equipe.

### 7.2 Resolution d enigme
1. Ouverture carte enigme depuis plateau (Stimulus board).
2. Navigation vers detail enigme.
3. Soumission reponse via fetch vers /enigme/{id}/check.
4. Normalisation et comparaison des solutions cote serveur.
5. Retour JSON success/failure + code secret si succes.

### 7.3 Validation code final
1. Saisie code sur plateau.
2. Requete POST JSON vers /jeu/validate-final-code.
3. Comparaison insensible a la casse.
4. Retour JSON avec message de victoire ou echec.

## 8. Frontend et comportement dynamique
Controleurs Stimulus:
- board_controller.js:
  - gestion modales contexte/enigme
  - timer de partie
  - validation code final
- game_controller.js:
  - collecte reponse (texte/radio/checkbox)
  - appel API de verification enigme
- avatar_preview_controller.js:
  - apercu avatar lors de la selection equipe
- csrf_protection_controller.js:
  - gestion token/cookie/header CSRF pour soumissions

## 9. Fixtures
Fixtures disponibles:
- UserFixtures: comptes admin/prof de base.
- JeuFixtures: jeu principal + code final initial.
- TypeFixtures: categories d enigmes.
- VignettesFixtures: vignettes d illustrations.
- EnigmeFixtures: jeu de 5 enigmes exemple.
- AvatarFixtures: bibliotheque d avatars.
- ParametreFixtures: parametre de duree (a rattacher au jeu selon besoin metier).

## 10. Migrations
- Migration presente: ajout de champ active sur tbl_jeu.
- Cycle standard:
  - php bin/console doctrine:migrations:diff
  - php bin/console doctrine:migrations:migrate

Point d attention:
- Verifier la compatibilite SQL generee avec la base cible (PostgreSQL attendu).

## 11. Installation et execution
Prerequis:
- PHP 8.2+
- Composer
- PostgreSQL ou Docker

Etapes type:
1. composer install
2. configurer DATABASE_URL dans .env.local
3. php bin/console doctrine:database:create
4. php bin/console doctrine:migrations:migrate
5. php bin/console doctrine:fixtures:load
6. symfony serve -d (ou serveur web equivalent)

Avec Docker base:
1. docker compose up -d
2. executer les commandes Doctrine depuis l environnement PHP du projet

## 12. Tests
- PHPUnit configure via phpunit.dist.xml.
- Tests existants:
  - LoginControllerTest (parcours de connexion)
  - Tests scaffoldes pour plusieurs controleurs (plusieurs marques incompletes).

Recommandations:
- Corriger les tests scaffoldes non alignes sur le modele actuel.
- Ajouter des tests d integration sur:
  - verification enigme multi-solutions
  - controle d acces par role
  - validation code final

## 13. Journalisation et observabilite
- Logs Symfony/Monolog dans var/log.
- Web profiler disponible en dev/test.

## 14. Dette technique et risques connus
- Incoherences ponctuelles routes/access_control.
- Quelques artefacts de scaffolding dans tests.
- Points de vigilance sur certaines relations et mappings Doctrine a maintenir propres.
- Melange Bootstrap + Tailwind (maitrise necessaire pour eviter conflits CSS).

## 15. Bonnes pratiques de maintenance
- Appliquer une migration par lot de changements metier.
- Garder fixtures et schema synchronises.
- Verifier les droits d acces a chaque ajout de route.
- Ajouter tests automatiques sur chaque fonctionnalite critique.
