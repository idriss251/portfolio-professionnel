<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-900">
    <div class="w-full max-w-md card">
      <h2 class="card-header text-2xl text-center">✍️ Inscription</h2>

      <div v-if="error" class="alert alert-error mb-4">
        {{ error }}
      </div>

      <form @submit.prevent="handleRegister" class="space-y-4">
        <div>
          <label class="block text-sm font-medium mb-2">Nom Complet</label>
          <input 
            v-model="form.name" 
            type="text" 
            required
            placeholder="John Doe"
          />
        </div>

        <div>
          <label class="block text-sm font-medium mb-2">Email</label>
          <input 
            v-model="form.email" 
            type="email" 
            required
            placeholder="email@example.com"
          />
        </div>

        <div>
          <label class="block text-sm font-medium mb-2">Mot de passe</label>
          <input 
            v-model="form.password" 
            type="password" 
            required
            placeholder="••••••••"
            minlength="6"
          />
          <p class="text-xs text-gray-500 mt-1">Minimum 6 caractères</p>
        </div>

        <button 
          type="submit" 
          :disabled="loading"
          class="w-full btn-primary disabled:opacity-50 disabled:cursor-not-allowed"
        >
          {{ loading ? 'Inscription...' : "S'Inscrire" }}
        </button>
      </form>

      <div class="mt-6 text-center">
        <p class="text-gray-400 mb-4">Déjà inscrit ?</p>
        <router-link to="/login" class="text-blue-500 hover:text-blue-400">
          Se connecter
        </router-link>
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
  name: '',
  email: '',
  password: ''
})
const loading = ref(false)
const error = ref(null)

const handleRegister = async () => {
  loading.value = true
  error.value = null
  
  const success = await authStore.register(
    form.value.email,
    form.value.password,
    form.value.name
  )
  
  if (success) {
    router.push('/')
  } else {
    error.value = authStore.error
  }
  
  loading.value = false
}
</script>
