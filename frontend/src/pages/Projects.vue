<template>
  <div class="max-w-7xl mx-auto px-4 py-16">
    <h1 class="text-4xl font-bold mb-12 text-center">📁 Mes Projets</h1>

    <!-- Filters -->
    <div class="mb-8 flex gap-4 flex-wrap justify-center">
      <button 
        @click="selectedCategory = null"
        :class="{
          'btn-primary': selectedCategory === null,
          'btn-secondary': selectedCategory !== null
        }"
      >
        Tous
      </button>
      <button 
        v-for="cat in categories"
        :key="cat"
        @click="selectedCategory = cat"
        :class="{
          'btn-primary': selectedCategory === cat,
          'btn-secondary': selectedCategory !== cat
        }"
      >
        {{ categoryLabels[cat] }}
      </button>
    </div>

    <!-- Projects Grid -->
    <div v-if="!loading" class="grid-auto">
      <router-link 
        v-for="project in filteredProjects"
        :key="project.id"
        :to="`/projects/${project.id}`"
        class="card hover:border-blue-500 transition cursor-pointer group"
      >
        <div v-if="project.image_url" class="mb-4 rounded overflow-hidden bg-gray-700 h-40">
          <img :src="project.image_url" :alt="project.title" class="w-full h-full object-cover group-hover:scale-105 transition">
        </div>
        
        <h3 class="text-lg font-bold mb-2 group-hover:text-blue-400 transition">{{ project.title }}</h3>
        <p class="text-gray-400 text-sm mb-4 line-clamp-2">{{ project.description }}</p>
        
        <div class="flex gap-2 flex-wrap mb-4">
          <span class="bg-blue-900 text-blue-200 px-2 py-1 rounded text-xs">{{ categoryLabels[project.category] }}</span>
          <span v-if="project.featured" class="bg-yellow-900 text-yellow-200 px-2 py-1 rounded text-xs">⭐ Vedette</span>
        </div>
        
        <div class="flex justify-between text-sm text-gray-500">
          <span>👁️ {{ project.views_count }}</span>
          <span>❤️ {{ project.likes_count }}</span>
        </div>
      </router-link>
    </div>

    <div v-else class="text-center py-12">
      <p class="text-gray-400">Chargement des projets...</p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '../services/api'

const projects = ref([])
const loading = ref(true)
const selectedCategory = ref(null)

const categories = ['ai', 'ml', 'data', 'web', 'database']
const categoryLabels = {
  ai: '🤖 IA',
  ml: '🧠 ML',
  data: '📊 Data',
  web: '🌐 Web',
  database: '🗄️ BD'
}

const filteredProjects = computed(() => {
  if (!selectedCategory.value) return projects.value
  return projects.value.filter(p => p.category === selectedCategory.value)
})

const fetchProjects = async () => {
  try {
    const response = await api.get('/projects')
    projects.value = response.data.data || []
  } catch (error) {
    console.error('Erreur chargement projets:', error)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchProjects()
})
</script>
