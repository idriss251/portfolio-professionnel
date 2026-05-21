import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import Home from '../pages/Home.vue'
import Login from '../pages/Login.vue'
import Register from '../pages/Register.vue'
import Projects from '../pages/Projects.vue'
import ProjectDetail from '../pages/ProjectDetail.vue'
import Contact from '../pages/Contact.vue'
import Dashboard from '../pages/Dashboard.vue'
import ManageProjects from '../pages/ManageProjects.vue'
import ManageContacts from '../pages/ManageContacts.vue'

const routes = [
  {
    path: '/',
    component: Home,
    meta: { title: 'Accueil' }
  },
  {
    path: '/login',
    component: Login,
    meta: { title: 'Connexion', requiresGuest: true }
  },
  {
    path: '/register',
    component: Register,
    meta: { title: 'Inscription', requiresGuest: true }
  },
  {
    path: '/projects',
    component: Projects,
    meta: { title: 'Projets' }
  },
  {
    path: '/projects/:id',
    component: ProjectDetail,
    meta: { title: 'Détail Projet' }
  },
  {
    path: '/contact',
    component: Contact,
    meta: { title: 'Contact' }
  },
  {
    path: '/dashboard',
    component: Dashboard,
    meta: { title: 'Tableau de Bord', requiresAuth: true, requiresAdmin: true }
  },
  {
    path: '/dashboard/projects',
    component: ManageProjects,
    meta: { title: 'Gérer Projets', requiresAuth: true, requiresAdmin: true }
  },
  {
    path: '/dashboard/contacts',
    component: ManageContacts,
    meta: { title: 'Gérer Messages', requiresAuth: true, requiresAdmin: true }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Route guards
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()
  
  // Vérifier authentification requise
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next('/login')
    return
  }
  
  // Vérifier rôle admin
  if (to.meta.requiresAdmin && !authStore.isAdmin) {
    next('/')
    return
  }
  
  // Rediriger login/register si déjà authentifié
  if (to.meta.requiresGuest && authStore.isAuthenticated) {
    next('/')
    return
  }
  
  document.title = to.meta.title + ' - Portfolio'
  next()
})

export default router
