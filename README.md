
# 📡 Symfony Backend - Test Technique Poisson Soluble 2025

Ce projet Symfony 6.4 permet d'importer des destinataires depuis un fichier CSV et d'envoyer des alertes météo par SMS de manière **asynchrone** via **Symfony Messenger**. L'envoi de SMS est simulé par un service qui les loggue.

## ✅ Fonctionnalités

- ✅ Import CSV via une commande CLI
- ✅ Validation des données (INSEE & numéro de téléphone)
- ✅ Persistance PostgreSQL (sans ORM)
- ✅ Envoi de SMS simulé via logs
- ✅ Dispatch des alertes via Symfony Messenger
- ✅ Endpoint `/alerter` sécurisé par clé API
- ✅ Migrations SQL avec `sql-migrations-bundle`

---

## 🧰 Prérequis

- PHP **8.4**
- Symfony **6.4**
- PostgreSQL
- Composer

---

## 🚀 Installation

### 1. Récupérer le projet

```bash
git clone git@github.com:Kv1k/sms_notifier.git
cd sms-notifier
```

### 2. Installer les dépendances

Exécutez la commande suivante pour installer les dépendances PHP via Composer :

```bash
composer install
```

### 3. Ajouter les packages nécessaires

Ensuite, vous devez installer les packages suivant pour l'installation de SQL Migrations Bundle (gère les migrations) :

```bash
composer require symfony/orm-pack
composer require doctrine/dbal:^4
composer require swouters/sql-migrations-bundle
```

### 4. Configuration de PostgreSQL 🛠

1. Ouvrez une session PostgreSQL :

   ```bash
   psql postgres
   ```

2. Créez un utilisateur et une base de données :

   ```sql
   CREATE USER postgres WITH PASSWORD 'root';
   CREATE DATABASE sms_notifier;
   GRANT ALL PRIVILEGES ON DATABASE sms_notifier TO postgres;
   ```

### 5. Configuration de l'environnement

Assurez-vous que le fichier `.env` contient les informations correctes :

```dotenv
DATABASE_URL="postgresql://postgres:root@127.0.0.1:5432/sms_notifier?serverVersion=14&charset=utf8"
BDDHOST="127.0.0.1"
BDDPORT=5432
BDDUSER="postgres"
BDDPASSWORD="root"
BDDNAME="sms_notifier"

MESSENGER_TRANSPORT_DSN=doctrine://default
```

##  Migrations SQL 🧱

Exécutez la commande suivante pour générer et appliquer les migrations SQL dans la base de données :

```bash
php bin/console sql-migrations:execute
```

## Import CSV 📥

Le fichier est placé dans public/uploads/ 

Lancer l'import :

```bash
php bin/console app:upload-csv
```

Un rapport est généré dans la console indiquant :

✅ Le nombre de lignes valides enregistrées

❌ Le nombre de lignes invalides



## Envoi d’alertes météo 🚨

1. Lancer le serveur symfony en arrière plan : 

```bash
symfony server:start -d
```

2. Lancer le worker Messenger :

```bash
php bin/console messenger:consume async -vv
```

Cela permet de démarrer l’envoi des messages et d'afficher les logs pour suivre l'état d'exécution.

3. Appeler l’endpoint /alerter :

```bash
curl -X POST http://localhost:8000/alerter -H "X-API-KEY: RTmIxqzx10e0kqZOdLHZMC25sBti" -d "insee=34172"
```

Remplacez `34172` par le code INSEE de la ville pour laquelle vous souhaitez envoyer la notification.

Cela dispatche les messages à tous les destinataires avec ce code INSEE.

Les SMS sont simulés via le SmsService et loggués.

## 🔒 Sécurisation par clé API

L’accès à /alerter est protégé par une clé d’API à envoyer :

X_API_KEY : RTmIxqzx10e0kqZOdLHZMC25sBti

En cas de clé absente ou incorrecte, l’API retourne un statut HTTP 401 ou 400.


## 📎 Structure du Projet

├── config/
├── migrations/
├── public/   <-- Fichiers CSV importés
├── src/
│   ├── Command/
│   ├── Controller/
│   ├── Message/
│   ├── MessageHandler/
│   └── Service/         
├── .env.dev              
├── composer.json
└── README.md


## 👨‍💻 Auteur
Test technique backend réalisé pour Poisson Soluble - 2025
Par Kamil NACHAT
