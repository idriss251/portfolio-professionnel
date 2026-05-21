<template>
  <div class="max-w-7xl mx-auto px-4 py-16">
    <h1 class="text-4xl font-bold mb-12">✉️ Gérer Messages</h1>

    <!-- Filters -->
    <div class="mb-8 flex gap-4">
      <button 
        v-for="status in ['new', 'read', 'replied']"
        :key="status"
        @click="selectedStatus = status"
        :class="{
          'btn-primary': selectedStatus === status,
          'btn-secondary': selectedStatus !== status
        }"
      >
        {{ statusLabels[status] }}
      </button>
    </div>

    <!-- Messages List -->
    <div class="space-y-4">
      <div v-for="message in filteredMessages" :key="message.id" class="card">
        <div class="flex justify-between items-start mb-4">
          <div>
            <h3 class="font-bold text-lg">{{ message.subject }}</h3>
            <p class="text-gray-400 text-sm">De: {{ message.name }} ({{ message.email }})</p>
          </div>
          <span 
            :class="{
              'bg-yellow-900 text-yellow-200': message.status === 'new',
              'bg-blue-900 text-blue-200': message.status === 'read',
              'bg-green-900 text-green-200': message.status === 'replied'
            }"
            class="px-3 py-1 rounded text-sm"
          >
            {{ statusLabels[message.status] }}
          </span>
        </div>
        
        <p class="text-gray-300 mb-4">{{ message.message }}</p>
        
        <div class="text-sm text-gray-500 mb-4">
          {{ formatDate(message.created_at) }}
        </div>
        
        <div class="flex gap-2 flex-wrap">
          <button 
            @click="updateStatus(message.id, 'read')"
            v-if="message.status !== 'read'"
            class="btn-secondary text-sm"
          >
            ✓ Marquer comme lu
          </button>
          <button 
            @click="updateStatus(message.id, 'replied')"
            v-if="message.status !== 'replied'"
            class="btn-secondary text-sm"
          >
            ↩️ Marquer comme répondu
          </button>
          <a :href="`mailto:${message.email}`" class="btn-primary text-sm">
            📧 Répondre
          </a>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '../services/api'

const messages = ref([])
const selectedStatus = ref('new')

const statusLabels = {
  new: '🔴 Nouveau',
  read: '🔵 Lu',
  replied: '✅ Répondu'
}

const filteredMessages = computed(() => {
  return messages.value.filter(m => m.status === selectedStatus.value)
})

const formatDate = (date) => {
  return new Date(date).toLocaleString('fr-FR')
}

const fetchMessages = async () => {
  try {
    const response = await api.get('/contacts')
    messages.value = response.data.data || []
  } catch (error) {
    console.error('Erreur:', error)
  }
}

const updateStatus = async (id, status) => {
  try {
    await api.put(`/contacts/${id}/status`, { status })
    fetchMessages()
  } catch (error) {
    console.error('Erreur:', error)
  }
}

onMounted(() => {
  fetchMessages()
})
</script>
