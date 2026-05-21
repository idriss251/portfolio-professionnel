<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-900">
    <div class="w-full max-w-md card">
      <h2 class="card-header text-2xl text-center">🔐 Connexion</h2>

      <div v-if="error" class="alert alert-error mb-4">
        {{ error }}
      </div>

      <form @submit.prevent="handleLogin" class="space-y-4">
        <div>
          <label class="block text-sm font-medium mb-2">Email</label>
          <input 
            v-model="form.email" 
            type="email" 
            required
            placeholder="admin@example.com"
          />
        </div>

        <div>
          <label class="block text-sm font-medium mb-2">Mot de passe</label>
          <input 
            v-model="form.password" 
            type="password" 
            required
            placeholder="••••••••"
          />
        </div>

        <button 
          type="submit" 
          :disabled="loading"
          class="w-full btn-primary disabled:opacity-50 disabled:cursor-not-allowed"
        >
          {{ loading ? 'Connexion...' : 'Se Connecter' }}
        </button>
      </form>

      <div class="mt-6 text-center">
        <p class="text-gray-400 mb-4">Pas encore inscrit ?</p>
        <router-link to="/register" class="text-blue-500 hover:text-blue-400">
          Créer un compte
        </router-link>
      </div>

      <div class="mt-6 pt-6 border-t border-gray-700">
        <p class="text-xs text-gray-500 text-center mb-2">Compte de démonstration :</p>
        <p class="text-xs text-gray-400 text-center">Email : <code class="bg-gray-700 px-1 rounded">admin@idriss-code.com</code></p>
        <p class="text-xs text-gray-400 text-center">MDP : <code class="bg-gray-700 px-1 rounded">admin2024_secure</code></p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const form = ref({
  email: '',
  password: ''
})
const loading = ref(false)
const error = ref(null)

const handleLogin = async () => {
  loading.value = true
  error.value = null
  
  const success = await authStore.login(form.email, form.password)
  
  if (success) {
    router.push('/')
  } else {
    error.value = authStore.error
  }
  
  loading.value = false
}
</script>
