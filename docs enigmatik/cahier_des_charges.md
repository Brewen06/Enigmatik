# Cahier des charges - Enigmatik

## 1. Contexte et objectif
Enigmatik est une application web pedagogique de type escape game numerique. Elle permet a des equipes d eleves de resoudre des enigmes informatiques pour retrouver un code final, pendant que les enseignants pilotent le contenu et suivent les parties.

Objectif principal:
- Proposer une experience de jeu en equipe, interactive et motivante.
- Permettre aux enseignants et administrateurs de configurer le jeu, les enigmes et les ressources associees.

## 2. Perimetre du projet
Le perimetre couvre:
- La consultation et la participation au jeu cote equipe.
- La creation et la gestion des contenus pedagogiques (enigmes, types, vignettes, parametres, avatars).
- La gestion des equipes et des utilisateurs back-office.
- La securisation des acces selon les roles.

Hors perimetre actuel:
- Paiement, inscription publique des enseignants, export avance des statistiques, application mobile native.

## 3. Parties prenantes
- Client / porteur du projet: equipe pedagogique.
- Utilisateurs metier:
  - Joueurs (equipes d eleves).
  - Professeurs (ROLE_PROF).
  - Administrateurs (ROLE_ADMIN).
- Equipe technique: developpement Symfony.

## 4. Roles et droits
- ROLE_USER: role minimal.
- ROLE_PROF: acces gestion pedagogique et tableau de bord.
- ROLE_ADMIN: acces complet administration.

Principes d acces attendus:
- Joueurs: acces au flux de jeu et creation d equipe.
- Prof/Admin: acces aux ecrans de gestion.
- Admin: operations sensibles (parametrage global avance, suppression de donnees critiques).

## 5. Besoins fonctionnels
### 5.1 Parcours joueur (equipe)
- Creer une equipe avec un nom et un avatar.
- Demarrer ou reprendre une partie via la session.
- Visualiser les enigmes disponibles.
- Ouvrir une enigme, lire la consigne, acceder a un lien de contenu.
- Saisir une reponse:
  - Reponse libre.
  - Reponse a choix (simple ou multiple selon configuration).
- Obtenir un retour immediat (bonne/mauvaise reponse).
- Debloquer des indices (code secret d enigme).
- Saisir le code final pour valider la victoire.
- Jouer avec timer si un parametre de duree est defini.

### 5.2 Parcours enseignant / administrateur
- Se connecter a l application.
- Acceder au tableau de bord enseignant:
  - Parties en cours.
  - Historique des parties terminees.
- Configurer le jeu:
  - Titre.
  - Message de bienvenue.
  - Image de bienvenue.
  - Code final.
- Gerer les enigmes (CRUD):
  - Ordre, titre, consigne, type, vignette, lien, code secret, statut actif/inactif.
  - Configuration de reponse libre ou quiz avec choix.
- Gerer les types d enigmes (CRUD).
- Gerer les vignettes (CRUD).
- Gerer les avatars (CRUD).
- Gerer les equipes (CRUD de suivi).
- Gerer les utilisateurs (CRUD).
- Gerer les parametres de jeu (CRUD).

## 6. Exigences non fonctionnelles
### 6.1 Techniques
- Stack cible: PHP >= 8.2, Symfony 7.3, Doctrine ORM, Twig, Stimulus.
- Base de donnees: PostgreSQL (environnement standard du projet).
- Compatibilite navigateurs modernes desktop.

### 6.2 Securite
- Authentification par formulaire.
- Protection CSRF sur formulaires et actions POST.
- Controle d acces par role.
- Mots de passe stockes de facon hachee.

### 6.3 Qualite
- Architecture MVC claire et maintenable.
- Logs applicatifs exploitables.
- Tests fonctionnels de base presents (a consolider).

## 7. Donnees metier principales
- User: email, roles, password.
- Equipe: nom, avatar, progression, horodatage de session.
- Jeu: meta configuration globale, code final, activation.
- Enigme: ordre, consigne, type de reponse, solution, statut actif.
- Type: categorie d enigme.
- Vignette: image et information de contexte.
- Parametre: cle/valeur liee au jeu (ex: duree en minutes).
- Avatar: nom + image.

## 8. Regles metier essentielles
- Une equipe doit avoir un avatar.
- Les enigmes inactives ne doivent pas etre visibles des joueurs.
- Les enseignants peuvent activer/desactiver les enigmes.
- La verification du code final est insensible a la casse.
- Le timer est calcule a partir d un parametre de duree du jeu.

## 9. Livrables attendus
- Application web fonctionnelle.
- Schema de base de donnees et migrations Doctrine.
- Jeux de donnees de demonstration (fixtures).
- Documentation technique.
- Documentation utilisateur.

## 10. Critere d acceptation
Le projet est accepte si:
- Les parcours joueur et enseignant sont realisables de bout en bout.
- Les droits d acces sont appliques selon les roles.
- Les CRUD principaux sont operationnels.
- Le controle de reponse d enigme et du code final fonctionne.
- Le tableau de bord affiche les parties en cours et l historique.

## 11. Contraintes et points d attention
- Coherence des routes securisees vs routes reelles a verifier regulierement.
- Coherence des relations Doctrine a maintenir (Equipe -> Avatar, Parametre -> Jeu).
- Migrations SQL a relire selon le moteur cible (PostgreSQL vs syntaxes MySQL).

## 12. Evolutions envisageables
- Statistiques avancees de progression par equipe.
- Export CSV/PDF des resultats.
- Gestion multi-jeux et archivage par session.
- Internationalisation complete.
- Renforcement de la couverture de tests automatisee.
