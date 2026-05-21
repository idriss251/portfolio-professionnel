import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '../services/api'

export const useAuthStore = defineStore('auth', () => {
  const token = ref(localStorage.getItem('auth_token') || null)
  const user = ref(JSON.parse(localStorage.getItem('auth_user') || 'null'))
  const loading = ref(false)
  const error = ref(null)

  const isAuthenticated = computed(() => !!token.value)
  const isAdmin = computed(() => user.value?.role === 'admin')

  const setToken = (newToken) => {
    token.value = newToken
    localStorage.setItem('auth_token', newToken)
    if (newToken) {
      api.defaults.headers.common['Authorization'] = `Bearer ${newToken}`
    }
  }

  const setUser = (newUser) => {
    user.value = newUser
    localStorage.setItem('auth_user', JSON.stringify(newUser))
  }

  const login = async (email, password) => {
    loading.value = true
    error.value = null
    try {
      const response = await api.post('/auth/login', { email, password })
      setToken(response.data.data.token)
      setUser(response.data.data.user)
      return true
    } catch (err) {
      error.value = err.response?.data?.message || 'Erreur de connexion'
      return false
    } finally {
      loading.value = false
    }
  }

  const register = async (email, password, name) => {
    loading.value = true
    error.value = null
    try {
      const response = await api.post('/auth/register', { email, password, name })
      setToken(response.data.data.token)
      setUser(response.data.data.user)
      return true
    } catch (err) {
      error.value = err.response?.data?.message || 'Erreur d\'inscription'
      return false
    } finally {
      loading.value = false
    }
  }

  const logout = () => {
    token.value = null
    user.value = null
    localStorage.removeItem('auth_token')
    localStorage.removeItem('auth_user')
    delete api.defaults.headers.common['Authorization']
  }

  const fetchProfile = async () => {
    try {
      const response = await api.get('/auth/profile')
      setUser(response.data.data)
      return response.data.data
    } catch (err) {
      logout()
      return null
    }
  }

  // Initialiser au chargement
  if (token.value) {
    api.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
  }

  return {
    token,
    user,
    loading,
    error,
    isAuthenticated,
    isAdmin,
    login,
    register,
    logout,
    setToken,
    setUser,
    fetchProfile
  }
})
