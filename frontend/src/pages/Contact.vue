<template>
  <div class="max-w-2xl mx-auto px-4 py-16">
    <h1 class="text-4xl font-bold mb-12 text-center">📞 Me Contacter</h1>

    <div class="card">
      <div v-if="success" class="alert alert-success mb-6">
        ✅ Message envoyé avec succès ! Je vous recontacterai bientôt.
      </div>

      <div v-if="error" class="alert alert-error mb-6">
        ❌ {{ error }}
      </div>

      <form @submit.prevent="handleSubmit" class="space-y-6">
        <div>
          <label class="block text-sm font-medium mb-2">Nom *</label>
          <input 
            v-model="form.name" 
            type="text" 
            required
            placeholder="Votre nom"
          />
        </div>

        <div>
          <label class="block text-sm font-medium mb-2">Email *</label>
          <input 
            v-model="form.email" 
            type="email" 
            required
            placeholder="votre.email@example.com"
          />
        </div>

        <div>
          <label class="block text-sm font-medium mb-2">Sujet *</label>
          <input 
            v-model="form.subject" 
            type="text" 
            required
            placeholder="Objet de votre message"
          />
        </div>

        <div>
          <label class="block text-sm font-medium mb-2">Message *</label>
          <textarea 
            v-model="form.message" 
            required
            placeholder="Votre message ici..."
            rows="6"
          ></textarea>
          <p class="text-xs text-gray-500 mt-1">Minimum 10 caractères</p>
        </div>

        <button 
          type="submit" 
          :disabled="loading"
          class="w-full btn-primary disabled:opacity-50 disabled:cursor-not-allowed"
        >
          {{ loading ? 'Envoi en cours...' : 'Envoyer le Message' }}
        </button>
      </form>

      <div class="mt-12 pt-8 border-t border-gray-700">
        <h3 class="text-xl font-bold mb-4">Autres moyens de contact :</h3>
        <div class="space-y-2 text-gray-400">
          <p>📧 Email : <a href="mailto:idrisskyavuyirwe@gmail.com" class="text-blue-500 hover:text-blue-400">idrisskyavuyirwe@gmail.com</a></p>
          <p>💼 LinkedIn : <a href="#" class="text-blue-500 hover:text-blue-400">linkedin.com/in/idrisskyavuyirwe</a></p>
          <p>🐙 GitHub : <a href="#" class="text-blue-500 hover:text-blue-400">github.com/idriss251</a></p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import api from '../services/api'

const form = ref({
  name: '',
  email: '',
  subject: '',
  message: ''
})
const loading = ref(false)
const error = ref(null)
const success = ref(false)

const handleSubmit = async () => {
  loading.value = true
  error.value = null
  success.value = false
  
  try {
    await api.post('/contacts', form.value)
    success.value = true
    form.value = { name: '', email: '', subject: '', message: '' }
    setTimeout(() => { success.value = false }, 5000)
  } catch (err) {
    error.value = err.response?.data?.message || 'Erreur lors de l\'envoi'
  } finally {
    loading.value = false
  }
}
</script>
