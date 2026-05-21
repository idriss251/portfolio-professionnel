<template>
  <div class="min-h-screen bg-gray-900 text-gray-100">
    <!-- Navigation -->
    <nav class="bg-gray-800 border-b border-gray-700 sticky top-0 z-50">
      <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">
        <router-link to="/" class="text-2xl font-bold text-blue-500 hover:text-blue-400">
          💼 Portfolio
        </router-link>
        
        <div class="flex gap-4 items-center">
          <router-link 
            v-if="!authStore.isAuthenticated"
            to="/login" 
            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded transition"
          >
            Connexion
          </router-link>
          
          <div v-else class="flex gap-3 items-center">
            <span class="text-sm">{{ authStore.user?.name }}</span>
            
            <router-link 
              v-if="authStore.isAdmin"
              to="/dashboard" 
              class="px-3 py-1 bg-purple-600 hover:bg-purple-700 rounded text-sm transition"
            >
              Admin
            </router-link>
            
            <button 
              @click="authStore.logout" 
              class="px-4 py-2 bg-red-600 hover:bg-red-700 rounded transition"
            >
              Déconnexion
            </button>
          </div>
        </div>
      </div>
    </nav>

    <!-- Routes -->
    <main>
      <router-view />
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 border-t border-gray-700 mt-16 py-6">
      <div class="max-w-7xl mx-auto px-4 text-center text-gray-400 text-sm">
        <p>© 2026 Idriss Kyavuyirwe - Ingénieur IA & ML</p>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { useAuthStore } from './stores/auth'

const authStore = useAuthStore()
</script>
