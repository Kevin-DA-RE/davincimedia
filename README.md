# 🎬 DaVinciMedia  

**📂 Gestion intuitive de vos médias (films, series) via une interface web moderne et performante.**  
Application développée avec **Vue.js**, **Laravel** et **Quasar Framework**.  

---

## 🚀 Présentation  

DaVinciMedia est une application web qui permet de :  

- 🖼️ **Gérer vos médias** (films, series)  
- 🔍 **Rechercher et filtrer** facilement vos fichiers  
- 🏷️ **Classer avec tags et catégories**  
- 🌐 **Accéder via une interface web intuitive**  
- ⚡ **Utiliser une API REST** pour intégration avec d’autres services  
- 🎨 **Profiter d’une UI moderne grâce à Quasar**  

--- 🌍 Site Web  
V1: http://thedavincimedia.alwaysdata.net/

---

## 🛠️ Stack technique  

| Technologie | Description |
|-------------|-------------|
| ⚙️ **Laravel** | Backend, API et gestion des données |
| 🎨 **Vue.js 3** | Frontend réactif et dynamique |
| 💎 **Quasar Framework** | UI moderne et responsive |
| 🗄️ **MySQL / PostgreSQL** | Base de données |
| 📦 **Composer & NPM** | Gestion des dépendances |
| 🌍 **REST API** | Communication entre frontend & backend |

---

## 📦 Installation  

### Prérequis  
- PHP ≥ 8.x  
- Composer  
- Node.js ≥ 18 + npm / yarn  
- MySQL ou PostgreSQL  

### Étapes  

```bash
# Cloner le projet
git clone https://github.com/Kevin-DA-RE/davincimedia.git
cd davincimedia

# Installer les dépendances PHP
composer install

# Installer les dépendances JS
npm install   # ou yarn install

# Configurer l'environnement
cp .env.example .env
php artisan key:generate

# Lancer les migrations
php artisan migrate

# Compiler les assets
npm run dev   # ou yarn dev

# Démarrer le serveur
php artisan serve
