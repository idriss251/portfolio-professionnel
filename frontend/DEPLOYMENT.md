# 🚀 Guide de Déploiement - Frontend & Backend

## 📋 Architecture

```
┌─────────────────────────────────────────────────────────┐
│                   Navigateur Utilisateur                 │
└────────────────────────────┬────────────────────────────┘
                             ↓
         ┌───────────────────────────────────┐
         │ Frontend (Vue.js 3) - Vercel      │
         │ https://votre-domaine.vercel.app  │
         └───────────────────────────────────┘
                             ↓ (API REST)
         ┌───────────────────────────────────┐
         │ Backend (PHP) - Render            │
         │ https://api.onrender.com/api      │
         └───────────────────────────────────┘
                             ↓
         ┌───────────────────────────────────┐
         │ Base de Données MySQL (Render)    │
         │ idriss_code_portfolio             │
         └───────────────────────────────────┘
```

## 🔧 Backend (API) - Déploiement sur Render

### Prérequis
- Repository GitHub avec la branche `app-web-moderne`
- Compte Render gratuit

### Étapes

1. **Aller sur Render.com**
   ```
   https://render.com
   ```

2. **Créer un nouveau Web Service**
   - Cliquer "New +" → "Web Service"
   - Connecter GitHub
   - Sélectionner `portfolio-professionnel`

3. **Configurer le service**
   ```
   Name: idriss-portfolio-api
   Root Directory: (laisser vide)
   Build Command: composer install
   Start Command: php -S 0.0.0.0:10000 -t .
   ```

4. **Variables d'environnement**
   Ajouter dans Render (Settings → Environment):
   ```
   DB_HOST=your-mysql-host
   DB_PORT=3306
   DB_DATABASE=idriss_code_portfolio
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   JWT_SECRET=your_super_secret_key
   ```

5. **Base de données MySQL**
   - Créer une instance MySQL sur Render
   - Ou utiliser un service externe (PlanetScale, Railway)
   - Récupérer les credentials

6. **Déployer**
   - Render détecte PHP automatiquement
   - Cliquer "Deploy"
   - URL générée: `https://idriss-portfolio-api.onrender.com`

### Tester l'API déployée
```bash
curl https://idriss-portfolio-api.onrender.com/api/health

response: {"success": true, "status": 200, "data": {"status": "ok"}}
```

---

## 🎨 Frontend (UI) - Déploiement sur Vercel

### Prérequis
- Dossier `frontend/` avec le code Vue.js 3
- Compte Vercel (gratuit)
- Git push effectué

### Étapes

1. **Aller sur Vercel.com**
   ```
   https://vercel.com
   ```

2. **Importer le projet**
   - Cliquer "Add New" → "Project"
   - Sélectionner `portfolio-professionnel`
   - Sélectionner la branche `app-web-moderne`

3. **Configurer le build**
   - **Framework Preset**: Vite
   - **Root Directory**: `./frontend`
   - **Build Command**: `npm run build`
   - **Output Directory**: `dist`

4. **Variables d'environnement**
   Ajouter dans Vercel (Settings → Environment Variables):
   ```
   VITE_API_URL=https://idriss-portfolio-api.onrender.com/api
   ```

5. **Domaine personnalisé (optionnel)**
   - Aller dans Settings → Domains
   - Ajouter `idriss-code.com`
   - Configurer les DNS chez votre registrar

6. **Déployer**
   - Cliquer "Deploy"
   - Attendre la fin du build (~2 min)
   - URL générée: `https://portfolio-professionnel.vercel.app`

### Tester le frontend
```
https://portfolio-professionnel.vercel.app

Email: admin@idriss-code.com
Password: admin2024_secure
```

---

## 🔄 Flux de déploiement automatique

### Modification du code
```bash
# Sur la branche app-web-moderne
git add .
git commit -m "feat: nouvelle fonctionnalité"
git push origin app-web-moderne
```

### Déploiement automatique
1. **Backend (Render)**
   - Render détecte le push
   - Redéploie automatiquement l'API
   - ✅ Disponible en ~3-5 minutes

2. **Frontend (Vercel)**
   - Vercel détecte le push
   - Lance le build
   - Déploie la nouvelle version
   - ✅ Disponible en ~2-3 minutes

---

## 📊 Vérification post-déploiement

### API
```bash
# Health check
curl https://idriss-portfolio-api.onrender.com/api/health

# Login
curl -X POST https://idriss-portfolio-api.onrender.com/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@idriss-code.com","password":"admin2024_secure"}'

# Récupérer projets
curl https://idriss-portfolio-api.onrender.com/api/projects
```

### Frontend
- Ouvrir l'URL Vercel
- Tester la connexion
- Vérifier l'appel API dans Network tab
- Tester les formulaires

---

## 🛡️ Sécurité

### HTTPS
- ✅ Render fournit HTTPS automatiquement
- ✅ Vercel fournit HTTPS automatiquement

### Headers de sécurité
- ✅ Implémentés dans `app/bootstrap.php`
- CSP, X-Frame-Options, HSTS, etc.

### Variables sensibles
- ❌ Ne jamais commiter `.env`
- ✅ Utiliser les variables d'environnement des plateformes
- ✅ JWT_SECRET unique en production

---

## 📈 Monitoring

### Render
- Logs: Render Dashboard → Logs
- Redémarrage auto en cas de crash
- Alertes email si déploiement échoue

### Vercel
- Logs: Vercel Dashboard → Functions
- Analytics de performance
- Erreurs JavaScript trackées

---

## 💡 Problèmes courants

### "Erreur 401 Unauthorized"
```
Solution: Vérifier JWT_SECRET identique partout
```

### "CORS error"
```
Solution: Vérifier CORS_ALLOWED_ORIGINS dans .env
Ajouter: https://votredomaine.vercel.app
```

### "Base de données introuvable"
```
Solution: Vérifier credentials DB_* dans Render
Exécuter setup: curl https://api.onrender.com/app/setup.php
```

### "Build échoué sur Vercel"
```
Solution: Vérifier Node version (minimum 16)
Vérifier VITE_API_URL configurée
Consulter Vercel Logs
```

---

## 🚀 Commandes rapides

```bash
# Pousser les changements
git push origin app-web-moderne

# Rebirth Render (redémarrer le service)
Sur Render Dashboard → Manual Deploy

# Redéployer Vercel
Sur Vercel Dashboard → Deployments → Redeploy
```

---

## 📞 Support

En cas de problème:
1. Vérifier les Logs (Render + Vercel)
2. Tester l'API localement
3. Vérifier les variables d'environnement
4. Consulter la documentation officielle

