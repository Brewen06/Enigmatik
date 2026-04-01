# Documentation utilisateur - Enigmatik

## 1. Introduction
Enigmatik est une plateforme de jeu pedagogique. Les equipes resolvent des enigmes informatiques pour trouver un code final. Les enseignants et administrateurs configurent le contenu et pilotent les parties.

## 2. Profils utilisateurs
- Equipe (joueurs): joue une partie.
- Professeur: gere le contenu du jeu et suit les parties.
- Administrateur: gere l ensemble de la plateforme.

## 3. Connexion
1. Ouvrir la page de connexion.
2. Saisir email et mot de passe.
3. Cliquer sur Se connecter.

En cas d erreur:
- Verifier l email.
- Verifier le mot de passe.
- Reessayer.

## 4. Parcours joueur
### 4.1 Creer une equipe
1. Depuis l accueil, cliquer sur Creer votre equipe.
2. Renseigner le nom de l equipe.
3. Choisir un avatar.
4. Valider.

Resultat:
- La partie demarre et l equipe est redirigee vers le plateau de jeu.

### 4.2 Comprendre le plateau
Le plateau contient:
- Les cartes des enigmes.
- Une zone de message de bienvenue (si configuree).
- Un timer (si active par l enseignant).
- Le verrou final pour saisir le code de victoire.

### 4.3 Resoudre une enigme
1. Cliquer sur une carte enigme active.
2. Lire la consigne et ouvrir l enigme.
3. Saisir la reponse:
  - soit librement,
  - soit via choix proposes.
4. Valider la reponse.

Resultat:
- Si juste: message de succes + code secret de l enigme.
- Si faux: message d erreur, vous pouvez reessayer.

### 4.4 Finir la partie
1. Rassembler les indices obtenus.
2. Saisir le code final dans la zone Verrou final.
3. Cliquer sur Deverrouiller.

Resultat:
- Si le code est bon: victoire.
- Sinon: message code incorrect.

## 5. Parcours professeur
### 5.1 Acceder au tableau de bord
1. Se connecter avec un compte professeur/admin.
2. Ouvrir le menu Tableau de bord.

Le tableau affiche:
- Les parties en cours.
- L historique des parties terminees.

### 5.2 Configurer le jeu
Menu Gestion > Configuration du jeu:
- Titre du jeu.
- Message de bienvenue.
- Image de bienvenue.
- Code final.

Cliquer sur Mettre a jour pour enregistrer.

### 5.3 Gerer les enigmes
Menu Gestion > Enigmes:
- Creer une enigme.
- Modifier une enigme.
- Activer/desactiver une enigme pour les joueurs.
- Supprimer une ou plusieurs enigmes.

Conseils:
- Definir un ordre logique (champ Ordre).
- Verifier la coherence solution/reponses possibles en mode quiz.

### 5.4 Gerer les ressources
Depuis le menu Gestion:
- Types d enigmes: categories pedagogiques.
- Vignettes: images et informations contextuelles.
- Avatars: visuels des equipes.
- Parametres: valeurs globales (ex: duree en minutes).

## 6. Parcours administrateur
En plus des droits professeur, l administrateur peut:
- Gerer les utilisateurs.
- Executer les operations sensibles de supervision.
- Administrer l ensemble du contenu.

## 7. Bonnes pratiques d utilisation
- Tester une enigme juste apres creation.
- Eviter des intitulés trop ambigus pour les solutions.
- Verifier les droits du compte avant des operations de gestion.
- Sauvegarder regulierement les donnees (sauvegarde base).

## 8. Depannage
### 8.1 Impossible de se connecter
- Verifier les identifiants.
- Verifier qu un administrateur a cree le compte.

### 8.2 Une enigme n apparait pas
- Verifier qu elle est active.
- Verifier le role utilisateur (joueur vs professeur).

### 8.3 Le timer ne s affiche pas
- Verifier qu un parametre de duree du jeu est defini.

### 8.4 Les images ne s affichent pas
- Verifier le chemin du fichier image.
- Verifier la presence du fichier dans le dossier public/images.

## 9. Donnees de demonstration (si fixtures chargees)
Comptes par defaut souvent utilises en environnement de test:
- admin@mail.com / admin
- prof@mail.com / prof

Important:
- Modifier ces mots de passe en environnement reel.

## 10. Support
En cas de probleme non resolu:
- Noter l action realisee, le message affiche et l heure.
- Transmettre ces informations a l equipe technique pour diagnostic.
