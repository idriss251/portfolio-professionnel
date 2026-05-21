<template>
  <div class="max-w-4xl mx-auto px-4 py-16">
    <div v-if="loading" class="text-center py-12">
      <p class="text-gray-400">Chargement...</p>
    </div>

    <div v-else-if="project" class="card fade-in">
      <div class="flex justify-between items-start mb-6">
        <div>
          <h1 class="text-3xl font-bold mb-2">{{ project.title }}</h1>
          <div class="flex gap-4 text-gray-400">
            <span>{{ categoryLabels[project.category] }}</span>
            <span v-if="project.featured">⭐ Vedette</span>
          </div>
        </div>
        <router-link to="/projects" class="btn-secondary">
          ← Retour
        </router-link>
      </div>

      <div v-if="project.image_url" class="mb-8 rounded overflow-hidden bg-gray-700 h-96">
        <img :src="project.image_url" :alt="project.title" class="w-full h-full object-cover">
      </div>

      <div class="grid md:grid-cols-3 gap-6 mb-8">
        <div>
          <p class="text-gray-500 text-sm">👁️ Vues</p>
          <p class="text-2xl font-bold">{{ project.views_count }}</p>
        </div>
        <div>
          <p class="text-gray-500 text-sm">❤️ Likes</p>
          <p class="text-2xl font-bold">{{ project.likes_count }}</p>
        </div>
        <div>
          <p class="text-gray-500 text-sm">📅 Date</p>
          <p class="text-2xl font-bold">{{ formatDate(project.created_at) }}</p>
        </div>
      </div>

      <div class="mb-8 pb-8 border-b border-gray-700">
        <h2 class="text-xl font-bold mb-4">Description</h2>
        <p class="text-gray-300 leading-relaxed">{{ project.description }}</p>
      </div>

      <div v-if="project.tags" class="mb-8 pb-8 border-b border-gray-700">
        <h2 class="text-xl font-bold mb-4">Technologies</h2>
        <div class="flex gap-2 flex-wrap">
          <span v-for="tag in JSON.parse(project.tags || '[]')" :key="tag" class="bg-blue-900 text-blue-200 px-3 py-1 rounded">
            {{ tag }}
          </span>
        </div>
      </div>

      <div class="flex gap-4 flex-wrap">
        <button 
          @click="likeProject" 
          class="btn-primary"
          :disabled="liked"
        >
          {{ liked ? '❤️ Aimé' : '🤍 Aimer' }}
        </button>
        <a v-if="project.github_url" :href="project.github_url" target="_blank" class="btn-secondary">
          🐙 GitHub
        </a>
        <a v-if="project.demo_url" :href="project.demo_url" target="_blank" class="btn-secondary">
          🔗 Démo en ligne
        </a>
      </div>
    </div>

    <div v-else class="text-center py-12">
      <p class="text-gray-400">Projet non trouvé</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '../services/api'

const route = useRoute()
const project = ref(null)
const loading = ref(true)
const liked = ref(false)

const categoryLabels = {
  ai: '🤖 IA',
  ml: '🧠 ML',
  data: '📊 Data',
  web: '🌐 Web',
  database: '🗄️ BD'
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('fr-FR')
}

const fetchProject = async () => {
  try {
    const response = await api.get(`/projects/${route.params.id}`)
    project.value = response.data.data
  } catch (error) {
    console.error('Erreur:', error)
  } finally {
    loading.value = false
  }
}

const likeProject = async () => {
  try {
    const response = await api.post(`/projects/${route.params.id}/like`)
    project.value = response.data.data
    liked.value = true
  } catch (error) {
    console.error('Erreur:', error)
  }
}

onMounted(() => {
  fetchProject()
})
</script>
