# 🎨 Frontend Portfolio - Vue.js 3

Frontend moderne avec Vue.js 3, Vite et Tailwind CSS.

## 🚀 Démarrage rapide

### Installation
```bash
npm install
```

### Développement
```bash
npm run dev
# Accès: http://localhost:3000
```

### Build production
```bash
npm run build
# Résultat: dossier 'dist'
```

## 📁 Structure

```
src/
├── pages/             # Pages Vue
├── components/        # Composants réutilisables
├── router/           # Vue Router
├── stores/           # Pinia stores
├── services/         # API Axios
├── App.vue           # Composant racine
├── main.js           # Entry point
└── style.css         # Tailwind styles
```

## 🔑 Identifiants de test

```
Email:    admin@idriss-code.com
Password: admin2024_secure
```

## 🌐 Déploiement

### Vercel
```bash
npm install -g vercel
vercel
```

### Netlify
```bash
npm install -g netlify-cli
netlify deploy --prod --dir=dist
```

## 🛠️ Technologies

- Vue.js 3 - Framework UI
- Vite - Build tool
- Vue Router - Routing
- Pinia - State management
- Axios - HTTP client
- Tailwind CSS - Styling

## 📝 Variables d'environnement

Copier `.env.example` en `.env` et configurer:

```ini
VITE_API_URL=http://localhost:8000/api
```

## ✨ Fonctionnalités

✅ Authentication JWT complète  
✅ Route guards (auth, admin)  
✅ Pages principales (Home, Projects, Contact)  
✅ Dashboard Admin (Gestion projets/messages)  
✅ Responsive design  
✅ Dark theme  
✅ Validation formulaires  
✅ Error handling  

## 🚀 Prochaines étapes

1. Ajouter composants réutilisables (Modal, Toast)
2. Implémenter theme switcher
3. Ajouter animations
4. Tests unitaires
5. PWA support
