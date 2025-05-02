# 🎵 Blog Musical Symfony

Ce projet est un blog musical développé avec Symfony. Il permet de gérer des articles, des utilisateurs, des commentaires, une API, et propose une exportation PDF.

## ✅ Fonctionnalités principales

- Authentification utilisateur (utilisateur / admin)
- CRUD complet sur les articles
- Interface admin pour gérer les articles
- API sécurisée (GET, POST, PUT, DELETE)
- Intégration d'une API musicale (Spotify)
- Export PDF d’un article en arrière-plan avec Messenger

## ⚙️ Installation rapide

```bash
git clone <repo>
cd projet
composer install
cp .env .env.local
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
symfony server:start
