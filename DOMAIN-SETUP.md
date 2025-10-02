# 🌐 Configuration du Domaine `idriss-code.com`

Guide pour configurer votre domaine personnalisé avec Render.

## 🎯 Étapes de Configuration

### 1️⃣ Acheter le Domaine

#### **Registrars Recommandés :**
- **Namecheap** : [namecheap.com](https://namecheap.com) - ~$10/an
- **GoDaddy** : [godaddy.com](https://godaddy.com) - ~$12/an
- **Google Domains** : [domains.google](https://domains.google) - ~$12/an
- **Cloudflare** : [cloudflare.com](https://cloudflare.com) - ~$8/an

#### **Recherche de Disponibilité :**
1. Allez sur votre registrar préféré
2. Recherchez `idriss-code.com`
3. Ajoutez au panier si disponible
4. Procédez au paiement

### 2️⃣ Déployer sur Render

#### **Déploiement Initial :**
```bash
# Depuis votre autre ordinateur avec Git
git clone https://github.com/idriss251/portfolio-professionnel.git
cd portfolio-professionnel
git push origin main
```

#### **Configuration Render :**
1. **Allez sur** [render.com](https://render.com)
2. **New + → Static Site**
3. **Connectez** votre repository GitHub
4. **Configuration** :
   - Name: `idriss-code`
   - Build Command: *(vide)*
   - Publish Directory: `.`
5. **Deploy**

### 3️⃣ Configurer le Domaine Personnalisé

#### **Dans Render Dashboard :**
1. **Allez** dans votre service `idriss-code`
2. **Settings** → **Custom Domains**
3. **Add Custom Domain**
4. **Entrez** : `idriss-code.com`
5. **Ajoutez aussi** : `www.idriss-code.com`

#### **Render vous donnera :**
```
CNAME Record:
Name: www
Value: idriss-code.onrender.com

A Record:
Name: @
Value: 216.24.57.1 (exemple)
```

### 4️⃣ Configuration DNS

#### **Chez votre Registrar :**

**Pour le domaine principal (`idriss-code.com`) :**
```
Type: A
Name: @
Value: [IP fournie par Render]
TTL: 300
```

**Pour le sous-domaine (`www.idriss-code.com`) :**
```
Type: CNAME
Name: www
Value: idriss-code.onrender.com
TTL: 300
```

#### **Configuration Complète :**
```dns
# Domaine principal
@ A 216.24.57.1

# Sous-domaine www
www CNAME idriss-code.onrender.com

# Redirection email (optionnel)
mail CNAME idriss-code.onrender.com
```

### 5️⃣ Vérification SSL

#### **Certificat Automatique :**
- ✅ **Let's Encrypt** : Render génère automatiquement
- ✅ **HTTPS** : Activé par défaut
- ✅ **Redirection** : HTTP → HTTPS automatique

#### **Vérification :**
1. **Attendez** 5-10 minutes après configuration DNS
2. **Testez** : `https://idriss-code.com`
3. **Vérifiez** le certificat SSL dans le navigateur

## 🔧 Configuration Avancée

### **Headers de Sécurité (déjà configurés) :**
```yaml
# render.yaml
headers:
  - path: /*
    name: X-Frame-Options
    value: DENY
  - path: /*
    name: X-Content-Type-Options
    value: nosniff
  - path: /*
    name: Referrer-Policy
    value: strict-origin-when-cross-origin
```

### **Redirections (déjà configurées) :**
```
# _redirects
/*    /index.html   200
/admin    /admin-login.html    200
/login    /admin-login.html    200
/private  /private-area.html   200
/memoir   /memoir-access.html  200
```

## 📊 Monitoring & Analytics

### **Google Analytics (Optionnel) :**
```html
<!-- Dans index.html, avant </head> -->
<script async src="https://www.googletagmanager.com/gtag/js?id=GA_MEASUREMENT_ID"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'GA_MEASUREMENT_ID');
</script>
```

### **Google Search Console :**
1. **Ajoutez** votre site sur [search.google.com/search-console](https://search.google.com/search-console)
2. **Vérifiez** la propriété via DNS ou fichier HTML
3. **Soumettez** votre sitemap : `https://idriss-code.com/sitemap.xml`

## 🚀 URLs Finales

### **Site Principal :**
- 🌐 **Production** : `https://idriss-code.com`
- 🌐 **Avec www** : `https://www.idriss-code.com`
- 🔧 **Render URL** : `https://idriss-code.onrender.com` (backup)

### **Pages Importantes :**
- 🏠 **Accueil** : `https://idriss-code.com`
- 🎮 **Démos** : `https://idriss-code.com/demos/`
- 🔐 **Admin** : `https://idriss-code.com/admin`
- 📚 **Mémoire** : `https://idriss-code.com/memoir`

## 🔍 Tests de Validation

### **Checklist Post-Déploiement :**
- [ ] `https://idriss-code.com` fonctionne
- [ ] `https://www.idriss-code.com` redirige correctement
- [ ] Certificat SSL valide (cadenas vert)
- [ ] Toutes les pages se chargent
- [ ] Démos interactives fonctionnent
- [ ] Espace privé accessible
- [ ] Responsive sur mobile
- [ ] Performance > 90 (Lighthouse)

### **Outils de Test :**
- **SSL** : [ssllabs.com/ssltest](https://www.ssllabs.com/ssltest/)
- **Performance** : [pagespeed.web.dev](https://pagespeed.web.dev/)
- **SEO** : [seobility.net](https://www.seobility.net/en/seocheck/)
- **Mobile** : [search.google.com/test/mobile-friendly](https://search.google.com/test/mobile-friendly)

## 💰 Coûts Estimés

### **Annuels :**
- **Domaine** : $8-12/an
- **Render** : Gratuit (plan Starter)
- **Total** : ~$10/an

### **Optionnels :**
- **Google Workspace** : $6/mois (email professionnel)
- **Cloudflare Pro** : $20/mois (CDN avancé)

## 🆘 Dépannage

### **Problèmes Courants :**

**Domaine ne fonctionne pas :**
- Vérifiez la propagation DNS : [whatsmydns.net](https://whatsmydns.net)
- Attendez 24-48h pour propagation complète

**SSL non valide :**
- Vérifiez que le domaine pointe vers Render
- Attendez la génération automatique du certificat

**Site non accessible :**
- Vérifiez les enregistrements DNS
- Testez l'URL Render directement

---

## ✅ Résumé

Une fois configuré, vous aurez :
- ✅ **Domaine professionnel** : `idriss-code.com`
- ✅ **HTTPS sécurisé** avec certificat SSL
- ✅ **Performance optimisée** avec CDN Render
- ✅ **Déploiement automatique** depuis GitHub
- ✅ **Coût minimal** : ~$10/an

**Votre portfolio sera accessible mondialement avec un domaine professionnel !** 🌟
