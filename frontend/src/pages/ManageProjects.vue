<template>
  <div class="max-w-7xl mx-auto px-4 py-16">
    <div class="flex justify-between items-center mb-12">
      <h1 class="text-4xl font-bold">📁 Gérer Projets</h1>
      <button @click="showForm = true" class="btn-primary">
        + Nouveau Projet
      </button>
    </div>

    <!-- Form Modal -->
    <div v-if="showForm" class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50">
      <div class="bg-gray-800 rounded-lg p-8 max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <h2 class="text-2xl font-bold mb-6">{{ editingId ? 'Modifier' : 'Créer' }} Projet</h2>
        
        <form @submit.prevent="saveProject" class="space-y-4">
          <div>
            <label class="block text-sm font-medium mb-2">Titre *</label>
            <input v-model="form.title" type="text" required />
          </div>
          
          <div>
            <label class="block text-sm font-medium mb-2">Description *</label>
            <textarea v-model="form.description" required rows="3"></textarea>
          </div>
          
          <div>
            <label class="block text-sm font-medium mb-2">Catégorie *</label>
            <select v-model="form.category" required>
              <option value="">Sélectionner...</option>
              <option value="ai">🤖 IA</option>
              <option value="ml">🧠 ML</option>
              <option value="data">📊 Data</option>
              <option value="web">🌐 Web</option>
              <option value="database">🗄️ BD</option>
            </select>
          </div>
          
          <div class="flex gap-4">
            <label class="flex items-center gap-2">
              <input v-model="form.featured" type="checkbox" class="w-4 h-4" />
              <span>Vedette</span>
            </label>
            <label class="flex items-center gap-2">
              <input v-model="form.status" type="checkbox" :true-value="'published'" :false-value="'draft'" class="w-4 h-4" />
              <span>Publié</span>
            </label>
          </div>
          
          <div class="flex gap-4 justify-end pt-4">
            <button type="button" @click="showForm = false" class="btn-secondary">
              Annuler
            </button>
            <button type="submit" class="btn-primary">
              {{ editingId ? 'Modifier' : 'Créer' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Projects List -->
    <div class="space-y-4">
      <div v-for="project in projects" :key="project.id" class="card flex justify-between items-center">
        <div>
          <h3 class="font-bold text-lg">{{ project.title }}</h3>
          <p class="text-gray-400 text-sm">{{ project.description }}</p>
        </div>
        <div class="flex gap-2">
          <button @click="editProject(project)" class="btn-secondary text-sm">
            ✏️ Éditer
          </button>
          <button @click="deleteProject(project.id)" class="btn-danger text-sm">
            🗑️ Supprimer
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '../services/api'

const projects = ref([])
const showForm = ref(false)
const editingId = ref(null)
const form = ref({
  title: '',
  description: '',
  category: '',
  featured: false,
  status: 'draft'
})

const fetchProjects = async () => {
  try {
    const response = await api.get('/projects')
    projects.value = response.data.data || []
  } catch (error) {
    console.error('Erreur:', error)
  }
}

const editProject = (project) => {
  editingId.value = project.id
  form.value = { ...project }
  showForm.value = true
}

const saveProject = async () => {
  try {
    if (editingId.value) {
      await api.put(`/projects/${editingId.value}`, form.value)
    } else {
      await api.post('/projects', form.value)
    }
    form.value = { title: '', description: '', category: '', featured: false, status: 'draft' }
    editingId.value = null
    showForm.value = false
    fetchProjects()
  } catch (error) {
    console.error('Erreur:', error)
  }
}

const deleteProject = async (id) => {
  if (confirm('Êtes-vous sûr ?')) {
    try {
      await api.delete(`/projects/${id}`)
      fetchProjects()
    } catch (error) {
      console.error('Erreur:', error)
    }
  }
}

onMounted(() => {
  fetchProjects()
})
</script>
