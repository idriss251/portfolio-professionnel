<template>
  <div class="max-w-7xl mx-auto px-4 py-16">
    <!-- Hero Section -->
    <div class="text-center mb-16">
      <h1 class="text-5xl font-bold mb-4 text-transparent bg-clip-text bg-gradient-to-r from-blue-500 to-purple-500">
        🚀 Bienvenue sur mon Portfolio
      </h1>
      <p class="text-xl text-gray-400 mb-8">
        Ingénieur Informatique | Spécialiste IA & Machine Learning
      </p>
      <div class="flex gap-4 justify-center">
        <router-link to="/projects" class="btn-primary">
          Voir mes Projets
        </router-link>
        <router-link to="/contact" class="btn-secondary">
          Me Contacter
        </router-link>
      </div>
    </div>

    <!-- Stats -->
    <div class="grid md:grid-cols-3 gap-6 mb-16">
      <div class="card text-center">
        <div class="text-4xl font-bold text-blue-500 mb-2">15+</div>
        <p class="text-gray-400">Projets Complétés</p>
      </div>
      <div class="card text-center">
        <div class="text-4xl font-bold text-purple-500 mb-2">50+</div>
        <p class="text-gray-400">Clients Satisfaits</p>
      </div>
      <div class="card text-center">
        <div class="text-4xl font-bold text-pink-500 mb-2">3+</div>
        <p class="text-gray-400">Années d'Expérience</p>
      </div>
    </div>

    <!-- Skills -->
    <div class="mb-16">
      <h2 class="text-3xl font-bold mb-8 text-center">Compétences Principales</h2>
      <div class="grid md:grid-cols-2 gap-6">
        <div class="card">
          <h3 class="card-header text-blue-400">🤖 Intelligence Artificielle</h3>
          <p class="text-gray-400 mb-4">Développement de modèles IA avancés, NLP, Vision par Ordinateur</p>
          <div class="flex gap-2 flex-wrap">
            <span class="bg-blue-900 text-blue-200 px-3 py-1 rounded text-sm">TensorFlow</span>
            <span class="bg-blue-900 text-blue-200 px-3 py-1 rounded text-sm">PyTorch</span>
            <span class="bg-blue-900 text-blue-200 px-3 py-1 rounded text-sm">Python</span>
          </div>
        </div>
        <div class="card">
          <h3 class="card-header text-purple-400">🌐 Développement Web</h3>
          <p class="text-gray-400 mb-4">Full-stack web avec Vue.js, React et PHP/Laravel</p>
          <div class="flex gap-2 flex-wrap">
            <span class="bg-purple-900 text-purple-200 px-3 py-1 rounded text-sm">Vue.js</span>
            <span class="bg-purple-900 text-purple-200 px-3 py-1 rounded text-sm">PHP</span>
            <span class="bg-purple-900 text-purple-200 px-3 py-1 rounded text-sm">MySQL</span>
          </div>
        </div>
        <div class="card">
          <h3 class="card-header text-green-400">📊 Data Science</h3>
          <p class="text-gray-400 mb-4">Analyse de données, ML et statistiques avancées</p>
          <div class="flex gap-2 flex-wrap">
            <span class="bg-green-900 text-green-200 px-3 py-1 rounded text-sm">Pandas</span>
            <span class="bg-green-900 text-green-200 px-3 py-1 rounded text-sm">Scikit-learn</span>
            <span class="bg-green-900 text-green-200 px-3 py-1 rounded text-sm">SQL</span>
          </div>
        </div>
        <div class="card">
          <h3 class="card-header text-pink-400">⚙️ DevOps & Cloud</h3>
          <p class="text-gray-400 mb-4">CI/CD, Docker, Kubernetes et déploiement cloud</p>
          <div class="flex gap-2 flex-wrap">
            <span class="bg-pink-900 text-pink-200 px-3 py-1 rounded text-sm">Docker</span>
            <span class="bg-pink-900 text-pink-200 px-3 py-1 rounded text-sm">GitHub Actions</span>
            <span class="bg-pink-900 text-pink-200 px-3 py-1 rounded text-sm">AWS</span>
          </div>
        </div>
      </div>
    </div>

    <!-- CTA -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg p-8 text-center">
      <h2 class="text-2xl font-bold mb-4">Intéressé par une collaboration ?</h2>
      <p class="text-gray-200 mb-6">Contactez-moi pour discuter de vos besoins</p>
      <router-link to="/contact" class="inline-block px-8 py-3 bg-white text-blue-600 rounded font-bold hover:bg-gray-100 transition">
        Démarrer un Projet
      </router-link>
    </div>
  </div>
</template>

<script setup>
// Page d'accueil
</script>
