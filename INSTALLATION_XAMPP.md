# 🚀 Installation avec XAMPP

Guide complet pour installer et configurer votre portfolio professionnel avec XAMPP.

## 📋 Prérequis

- **XAMPP** version 7.4 ou supérieure
- **Navigateur web** moderne (Chrome, Firefox, Edge, Safari)

## 🔧 Installation XAMPP

### 1. Télécharger XAMPP
- Rendez-vous sur [https://www.apachefriends.org](https://www.apachefriends.org)
- Téléchargez la version pour Windows
- Installez XAMPP dans `C:\xampp` (chemin recommandé)

### 2. Démarrer les services
1. Lancez **XAMPP Control Panel**
2. Démarrez **Apache** (cliquez sur "Start")
3. Démarrez **MySQL** (cliquez sur "Start")

## 📁 Configuration du projet

### 1. Copier les fichiers
Copiez le dossier `mon-site-professionnel` dans :
```
C:\xampp\htdocs\mon-site-professionnel
```

### 2. Vérifier la structure
Votre dossier doit ressembler à :
```
C:\xampp\htdocs\mon-site-professionnel\
├── index.html
├── app/
├── assets/
├── logs/
└── ...
```

## 🗄️ Configuration de la base de données

### 1. Accéder à phpMyAdmin
- Ouvrez votre navigateur
- Allez sur : `http://localhost/phpmyadmin`
- Connectez-vous (utilisateur: `root`, mot de passe: vide)

### 2. Créer la base de données
```sql
CREATE DATABASE keyne_portfolio CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 3. Initialiser l'application
1. Ouvrez votre navigateur
2. Allez sur : `http://localhost/mon-site-professionnel/app/init.php`
3. Suivez les instructions à l'écran

**Alternative via ligne de commande :**
```bash
cd C:\xampp\htdocs\mon-site-professionnel
C:\xampp\php\php.exe app/init.php
```

## 🌐 Accéder à votre site

Une fois l'installation terminée, accédez à :
```
http://localhost/mon-site-professionnel
```

## ⚙️ Configuration avancée

### Modifier le port Apache (optionnel)
Si le port 80 est occupé :

1. Dans XAMPP Control Panel, cliquez sur "Config" à côté d'Apache
2. Sélectionnez "httpd.conf"
3. Changez `Listen 80` en `Listen 8080`
4. Redémarrez Apache
5. Accédez au site via : `http://localhost:8080/mon-site-professionnel`

### Activer les extensions PHP nécessaires
Dans `C:\xampp\php\php.ini`, décommentez :
```ini
extension=pdo_mysql
extension=openssl
extension=mbstring
extension=json
```

## 🔍 Vérifications

### Test de fonctionnement
1. **Page d'accueil** : `http://localhost/mon-site-professionnel`
2. **API Projets** : `http://localhost/mon-site-professionnel/app/controllers/ProjectController.php`
3. **Formulaire contact** : Testez l'envoi d'un message

### Vérifier les logs
Les logs sont dans :
```
C:\xampp\htdocs\mon-site-professionnel\logs\
```

## 🐛 Résolution de problèmes

### Erreur "Access forbidden"
- Vérifiez que les fichiers sont dans `htdocs`
- Redémarrez Apache dans XAMPP

### Erreur de connexion MySQL
- Vérifiez que MySQL est démarré dans XAMPP
- Vérifiez les paramètres dans `app/config/config.php`

### Page blanche
- Activez l'affichage des erreurs PHP
- Vérifiez les logs Apache : `C:\xampp\apache\logs\error.log`

### Problème d'envoi d'email
- Configurez un serveur SMTP dans `app/config/config.php`
- Ou utilisez un service comme Gmail SMTP

## 📧 Configuration email (optionnel)

Pour tester l'envoi d'emails, configurez dans `app/config/config.php` :

```php
// Configuration email
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'votre-email@gmail.com');
define('SMTP_PASSWORD', 'votre-mot-de-passe-app');
```

## 🔒 Sécurité en développement

### Fichier .htaccess
Un fichier `.htaccess` est automatiquement créé pour :
- Protéger les dossiers sensibles
- Optimiser les performances
- Ajouter des headers de sécurité

### Mode développement
Le mode développement est activé par défaut :
- Affichage des erreurs
- Logs détaillés
- Données de test

## 🚀 Mise en production

Quand vous êtes prêt pour la production :

1. Modifiez `app/config/config.php` :
```php
define('ENVIRONMENT', 'production');
define('DEBUG_MODE', false);
```

2. Configurez un vrai serveur SMTP
3. Changez les clés de sécurité
4. Activez HTTPS

## 📱 Test sur mobile

Pour tester sur mobile depuis votre réseau local :

1. Trouvez votre IP locale : `ipconfig` dans cmd
2. Accédez depuis votre mobile : `http://[VOTRE-IP]/mon-site-professionnel`
3. Assurez-vous que le firewall Windows autorise les connexions

## 🎯 Fonctionnalités à tester

- ✅ Navigation fluide entre les sections
- ✅ Animations et effets visuels
- ✅ Formulaire de contact
- ✅ Filtrage des projets
- ✅ Responsive design sur mobile
- ✅ Performance et vitesse de chargement

## 📞 Support

En cas de problème :
1. Vérifiez les logs d'erreur
2. Consultez la documentation XAMPP
3. Vérifiez la configuration PHP

---

**🎉 Félicitations ! Votre portfolio professionnel est maintenant opérationnel avec XAMPP !**
