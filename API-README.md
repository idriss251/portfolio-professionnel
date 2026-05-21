# 🚀 API REST - Portfolio Moderne

## Configuration Rapide

### 1. Copier la configuration
```bash
cp app/config/.env.example app/config/.env
```

### 2. Configurer `.env`
```ini
DB_HOST=localhost
DB_DATABASE=idriss_code_portfolio
DB_USERNAME=root
DB_PASSWORD=
JWT_SECRET=votre_secret_ici
```

### 3. Initialiser la BD
Accéder à : `http://localhost:8000/app/setup.php`

---

## 📋 Endpoints API

### 🔐 Authentification

#### Login
```bash
POST /api/auth/login
Content-Type: application/json

{
  "email": "admin@idriss-code.com",
  "password": "admin2024_secure"
}

✅ Response:
{
  "success": true,
  "status": 200,
  "message": "Connexion réussie",
  "data": {
    "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "user": {
      "id": 1,
      "email": "admin@idriss-code.com",
      "name": "Idriss Admin",
      "role": "admin"
    }
  }
}
```

#### Register
```bash
POST /api/auth/register
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password123",
  "name": "John Doe"
}
```

#### Get Profile
```bash
GET /api/auth/profile
Authorization: Bearer {token}
```

#### Update Profile
```bash
PUT /api/auth/profile
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Nouveau Nom",
  "bio": "Ma bio"
}
```

---

### 📁 Projets (Public)

#### Get All Projects
```bash
GET /api/projects
GET /api/projects?status=published
GET /api/projects?category=ai
GET /api/projects?featured=1
```

#### Get Project by ID
```bash
GET /api/projects/1
```

---

### 📁 Projets (Admin Only)

#### Create Project
```bash
POST /api/projects
Authorization: Bearer {token}
Content-Type: application/json

{
  "title": "Mon Projet",
  "description": "Description...",
  "category": "ai",
  "tags": ["tag1", "tag2"],
  "featured": true,
  "status": "published"
}
```

#### Update Project
```bash
PUT /api/projects/1
Authorization: Bearer {token}
Content-Type: application/json

{
  "title": "Titre modifié"
}
```

#### Delete Project
```bash
DELETE /api/projects/1
Authorization: Bearer {token}
```

#### Like Project
```bash
POST /api/projects/1/like
```

---

### ✉️ Contact (Public)

#### Send Message
```bash
POST /api/contacts
Content-Type: application/json

{
  "name": "John",
  "email": "john@example.com",
  "subject": "Question",
  "message": "Votre message ici..."
}
```

---

### ✉️ Contact (Admin)

#### Get All Messages
```bash
GET /api/contacts
Authorization: Bearer {token}
```

#### Get Message by ID
```bash
GET /api/contacts/1
Authorization: Bearer {token}
```

#### Update Message Status
```bash
PUT /api/contacts/1/status
Authorization: Bearer {token}
Content-Type: application/json

{
  "status": "read" // ou "replied"
}
```

---

## 🔐 Authentification JWT

### Bearer Token
Tous les endpoints nécessitant l'authentification utilisent le header:
```
Authorization: Bearer {votre_token_jwt}
```

### Token Structure
- **Header**: `{"alg": "HS256", "typ": "JWT"}`
- **Payload**: `{"id": 1, "email": "...", "role": "admin", "iat": ..., "exp": ...}`
- **Signature**: HMAC SHA256 avec secret JWT

### Expiration
Les tokens expirent après 2 heures (configurable dans `.env`)

---

## 🧪 Tester avec cURL

```bash
# 1. Login
TOKEN=$(curl -s -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@idriss-code.com","password":"admin2024_secure"}' \
  | jq -r '.data.token')

# 2. Récupérer profil
curl -X GET http://localhost:8000/api/auth/profile \
  -H "Authorization: Bearer $TOKEN"

# 3. Récupérer tous les projets
curl -X GET http://localhost:8000/api/projects

# 4. Créer un projet
curl -X POST http://localhost:8000/api/projects \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"title":"Nouveau","description":"Test","category":"ai","status":"published"}'
```

---

## 🛡️ Sécurité

✅ **Authentification JWT** - Tokens sécurisés  
✅ **Bcrypt Hashing** - Mots de passe chiffrés  
✅ **SQL Protection** - Requêtes préparées PDO  
✅ **CORS Headers** - Protection cross-origin  
✅ **Input Validation** - Validation données entrantes  
✅ **Rate Limiting** - À implémenter  

---

## 📱 Prochaines Étapes

1. Créer un **Frontend Vue.js 3** qui appelle cette API
2. Ajouter un **système de rate limiting**
3. Implémenter **OAuth 2.0** pour login social
4. Déployer l'API sur **Render.com**
5. Déployer le frontend sur **Vercel**

---

## 📚 Documentation

- [Configuration](app/config/Config.php)
- [Models](app/models/)
- [Controllers](app/controllers/)
- [Helpers](app/helpers/)
