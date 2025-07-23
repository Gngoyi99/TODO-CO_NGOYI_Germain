# Guide de collaboration pour l’équipe

Ce guide explique comment contribuer efficacement au projet, quelles règles de qualité respecter et les bonnes pratiques à adopter pour une collaboration fluide.

---

## A. Organisation du projet

- **Branche principale :** `main`
- **Branches de développement :** `feature/`, `fix/`

Chaque fonctionnalité ou correction doit être développée dans une branche dédiée en suivant une convention de nommage claire :

```bash
feature/ajout-authentification
fix/correction-login-bug
```
## B. Processus de contribution

### 1. Mettre à jour la branche locale
Avant de commencer une nouvelle tâche, assurez-vous de récupérer les dernières modifications de la branche principale :
```bash
git pull origin main
```

### 2. Créer une nouvelle branche
Créez une branche pour votre fonctionnalité ou correctif :
```bash
git checkout -b feature/nom-de-la-fonctionnalite
```

### 3. Développer et tester localement
Effectuez vos développements et vérifiez que tout fonctionne sur votre environnement local.

### 4. Faire une pull request (PR)
Une fois vos modifications terminées, poussez votre branche et ouvrez une Pull Request (PR) vers main sur GitHub/GitLab.

### 5. Revue de code
Un autre membre de l’équipe doit relire et valider le code avant de le fusionner.

## C. Règles de qualité

- Code propre et commenté : Expliquez les parties complexes pour aider vos collègues.
- Tests : Écrire des tests pour les nouvelles fonctionnalités lorsque cela est pertinent.
- Migrations Doctrine : Ne jamais pousser des migrations en base sans concertation avec l’équipe.
- Normes : Respecter la convention PSR-12 pour le code PHP.

## D. Bonnes pratiques
- Ne jamais modifier directement la branche main.
- Toujours tester le projet en local avant de pousser vos modifications.
- Mettre à jour le fichier README.md pour documenter les nouvelles routes ou fonctionnalités.
- Résoudre les conflits Git avant d’ouvrir une PR.
- Utiliser des messages de commit clairs et explicites :
```bash
feat: ajout de l’authentification JWT
fix: correction bug suppression de tâche anonyme
```
