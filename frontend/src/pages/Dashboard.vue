<template>
  <div class="max-w-7xl mx-auto px-4 py-16">
    <h1 class="text-4xl font-bold mb-12">📊 Tableau de Bord Admin</h1>

    <div class="grid md:grid-cols-4 gap-6 mb-12">
      <div class="card text-center">
        <div class="text-3xl font-bold text-blue-500 mb-2">{{ stats.projects }}</div>
        <p class="text-gray-400">Projets</p>
      </div>
      <div class="card text-center">
        <div class="text-3xl font-bold text-green-500 mb-2">{{ stats.contacts }}</div>
        <p class="text-gray-400">Messages</p>
      </div>
      <div class="card text-center">
        <div class="text-3xl font-bold text-purple-500 mb-2">{{ stats.users }}</div>
        <p class="text-gray-400">Utilisateurs</p>
      </div>
      <div class="card text-center">
        <div class="text-3xl font-bold text-pink-500 mb-2">{{ stats.totalViews }}</div>
        <p class="text-gray-400">Vues Totales</p>
      </div>
    </div>

    <div class="grid md:grid-cols-2 gap-6">
      <router-link to="/dashboard/projects" class="card hover:border-blue-500 transition cursor-pointer">
        <h3 class="card-header text-blue-400">📁 Gérer Projets</h3>
        <p class="text-gray-400">Créer, modifier ou supprimer vos projets</p>
        <div class="mt-4 text-blue-500 font-medium">Accéder →</div>
      </router-link>
      <router-link to="/dashboard/contacts" class="card hover:border-green-500 transition cursor-pointer">
        <h3 class="card-header text-green-400">✉️ Gérer Messages</h3>
        <p class="text-gray-400">Consulter et répondre aux messages de contact</p>
        <div class="mt-4 text-green-500 font-medium">Accéder →</div>
      </router-link>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '../services/api'

const stats = ref({
  projects: 0,
  contacts: 0,
  users: 0,
  totalViews: 0
})

const loadStats = async () => {
  try {
    const projectsRes = await api.get('/projects')
    const contactsRes = await api.get('/contacts')
    
    stats.value.projects = projectsRes.data.data?.length || 0
    stats.value.contacts = contactsRes.data.data?.length || 0
    stats.value.users = 1 // À implémenter
    stats.value.totalViews = projectsRes.data.data?.reduce((sum, p) => sum + (p.views_count || 0), 0) || 0
  } catch (error) {
    console.error('Erreur chargement stats:', error)
  }
}

onMounted(() => {
  loadStats()
})
</script>
