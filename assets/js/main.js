// ===== PORTFOLIO MODERNE - JAVASCRIPT =====

// Configuration globale
const CONFIG = {
    animationDuration: 800,
    scrollOffset: 100,
    counterSpeed: 2000,
    debounceDelay: 10
};

// Utilitaires modernes
const $ = (selector) => document.querySelector(selector);
const $$ = (selector) => document.querySelectorAll(selector);

// Classes principales
class ModernPortfolio {
    constructor() {
        this.navbar = $('#navbar');
        this.navToggle = $('#nav-toggle');
        this.navMenu = $('#nav-menu');
        this.navLinks = $$('.nav-link');
        this.init();
    }
    
    init() {
        this.setupNavigation();
        this.setupAnimations();
        this.setupProjects();
        this.setupContactForm();
        this.setupPerformanceOptimizations();
        console.log('🚀 Portfolio moderne initialisé!');
    }
    
    // ===== NAVIGATION MODERNE =====
    setupNavigation() {
        // Toggle menu mobile
        this.navToggle?.addEventListener('click', () => this.toggleMobileMenu());
        
        // Fermer menu au clic sur lien
        this.navLinks.forEach(link => {
            link.addEventListener('click', () => this.closeMobileMenu());
        });
        
        // Scroll navbar
        window.addEventListener('scroll', this.debounce(() => this.handleScroll(), CONFIG.debounceDelay));
        
        // Navigation smooth
        this.navLinks.forEach(link => {
            link.addEventListener('click', (e) => this.smoothScroll(e));
        });
        
        // Fermer menu avec Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') this.closeMobileMenu();
        });
    }
    
    toggleMobileMenu() {
        this.navMenu?.classList.toggle('active');
        this.navToggle?.classList.toggle('active');
    }
    
    closeMobileMenu() {
        this.navMenu?.classList.remove('active');
        this.navToggle?.classList.remove('active');
    }
    
    handleScroll() {
        // Effet navbar au scroll
        if (window.scrollY > 50) {
            this.navbar?.classList.add('scrolled');
        } else {
            this.navbar?.classList.remove('scrolled');
        }
        
        // Mise à jour du lien actif
        this.updateActiveLink();
    }
    
    updateActiveLink() {
        const sections = $$('section[id]');
        const scrollPos = window.scrollY + CONFIG.scrollOffset;
        
        sections.forEach(section => {
            const top = section.offsetTop;
            const height = section.offsetHeight;
            const id = section.getAttribute('id');
            
            if (scrollPos >= top && scrollPos < top + height) {
                this.navLinks.forEach(link => {
                    link.classList.remove('active');
                    if (link.getAttribute('href') === `#${id}`) {
                        link.classList.add('active');
                    }
                });
            }
        });
    }
    
    smoothScroll(e) {
        const href = e.target.getAttribute('href');
        if (href?.startsWith('#')) {
            e.preventDefault();
            const target = $(href);
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        }
    }
    
    // ===== ANIMATIONS ET COMPTEURS =====
    setupAnimations() {
        this.setupIntersectionObserver();
        this.initCounters();
    }
    
    setupIntersectionObserver() {
        const options = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        this.observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in-up');
                    
                    // Animer les compteurs
                    if (entry.target.classList.contains('hero-stats')) {
                        this.animateCounters();
                    }
                }
            });
        }, options);
        
        // Observer les éléments
        $$('.hero-stats, .skill-category, .stat-item').forEach(el => {
            this.observer.observe(el);
        });
    }
    
    initCounters() {
        const counters = $$('.stat-number[data-target]');
        counters.forEach(counter => {
            this.observer.observe(counter);
        });
    }
    
    animateCounters() {
        const counters = $$('.stat-number[data-target]');
        
        counters.forEach(counter => {
            const target = parseInt(counter.getAttribute('data-target'));
            const duration = CONFIG.counterSpeed;
            const start = performance.now();
            
            const animate = (currentTime) => {
                const elapsed = currentTime - start;
                const progress = Math.min(elapsed / duration, 1);
                
                const easeOutQuart = 1 - Math.pow(1 - progress, 4);
                const current = Math.floor(easeOutQuart * target);
                
                counter.textContent = current;
                
                if (progress < 1) {
                    requestAnimationFrame(animate);
                } else {
                    counter.textContent = target;
                }
            };
            
            requestAnimationFrame(animate);
        });
    }
    
    // ===== PROJETS MODERNES =====
    setupProjects() {
        this.projectsData = [
            {
                id: 1,
                title: "Système de Recommandation ML",
                description: "Algorithme de recommandation utilisant le deep learning et le filtrage collaboratif pour personnaliser l'expérience utilisateur.",
                category: "ml",
                tags: ["Python", "TensorFlow", "Collaborative Filtering", "Deep Learning"],
                image: "fas fa-brain",
                github: "#",
                demo: "demos/ml-recommendation.html"
            },
            {
                id: 2,
                title: "Analyse Prédictive des Ventes",
                description: "Modèle de prédiction des ventes utilisant des techniques de time series et de régression pour optimiser la stratégie commerciale.",
                category: "data",
                tags: ["Python", "Scikit-learn", "Pandas", "Time Series"],
                image: "fas fa-chart-line",
                github: "#",
                demo: "demos/sales-prediction.html"
            },
            {
                id: 3,
                title: "Kin-Immo Manager",
                description: "Système de gestion immobilière moderne pour Kinshasa, République Démocratique du Congo. Application complète de gestion des biens immobiliers.",
                category: "web",
                tags: ["PHP", "MySQL", "JavaScript", "Bootstrap"],
                image: "fas fa-building",
                github: "#",
                demo: "demos/kin-immo-manager.html"
            },
            {
                id: 4,
                title: "Chatbot Intelligent",
                description: "Assistant conversationnel basé sur NLP et transformers pour automatiser le support client avec compréhension contextuelle.",
                category: "ai",
                tags: ["NLP", "Transformers", "BERT", "Python"],
                image: "fas fa-robot",
                github: "#",
                demo: "demos/chatbot-intelligent.html"
            },
            {
                id: 5,
                title: "Vision par Ordinateur",
                description: "Système de détection et classification d'objets en temps réel utilisant des réseaux de neurones convolutionnels.",
                category: "ai",
                tags: ["OpenCV", "CNN", "PyTorch", "Computer Vision"],
                image: "fas fa-eye",
                github: "#",
                demo: "demos/computer-vision.html"
            },
            {
                id: 6,
                title: "Pipeline MLOps",
                description: "Infrastructure complète de déploiement et monitoring de modèles ML en production avec CI/CD automatisé.",
                category: "ml",
                tags: ["Docker", "Kubernetes", "MLflow", "AWS"],
                image: "fas fa-cogs",
                github: "#",
                demo: "demos/mlops-pipeline.html"
            },
            {
                id: 7,
                title: "Analyse de Sentiment",
                description: "Outil d'analyse de sentiment sur les réseaux sociaux utilisant des modèles de NLP pour le monitoring de marque.",
                category: "data",
                tags: ["NLP", "Sentiment Analysis", "Twitter API", "Visualization"],
                image: "fas fa-heart",
                github: "#",
                demo: "demos/sentiment-analysis.html"
            }
        ];
        
        this.renderProjects();
        this.setupProjectFilters();
    }
    
    renderProjects(projects = this.projectsData) {
        const projectsGrid = $('#projects-grid');
        if (!projectsGrid) return;
        
        projectsGrid.innerHTML = projects.map(project => `
            <div class="project-card fade-in-up" data-category="${project.category}">
                <div class="project-image">
                    <i class="${project.image}"></i>
                </div>
                <div class="project-content">
                    <h3 class="project-title">${project.title}</h3>
                    <p class="project-description">${project.description}</p>
                    <div class="project-tags">
                        ${project.tags.map(tag => `<span class="project-tag">${tag}</span>`).join('')}
                    </div>
                    <div class="project-links">
                        <a href="${project.github}" class="project-link">
                            <i class="fab fa-github"></i>
                            Code
                        </a>
                        <a href="${project.demo}" class="project-link">
                            <i class="fas fa-external-link-alt"></i>
                            Demo
                        </a>
                    </div>
                </div>
            </div>
        `).join('');
    }
    
    setupProjectFilters() {
        const filterButtons = $$('.filter-btn');
        filterButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                filterButtons.forEach(btn => btn.classList.remove('active'));
                button.classList.add('active');
                
                const category = button.getAttribute('data-filter');
                const filteredProjects = category === 'all' 
                    ? this.projectsData 
                    : this.projectsData.filter(project => project.category === category);
                
                this.renderProjects(filteredProjects);
            });
        });
    }
    
    // ===== FORMULAIRE DE CONTACT =====
    setupContactForm() {
        const contactForm = $('#contact-form');
        if (!contactForm) return;
        
        contactForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(contactForm);
            const submitButton = contactForm.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            
            // Loading state
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Envoi...';
            submitButton.disabled = true;
            
            try {
                // Simulation d'envoi
                await new Promise(resolve => setTimeout(resolve, 1000));
                this.showNotification('Message envoyé avec succès!', 'success');
                contactForm.reset();
            } catch (error) {
                this.showNotification('Erreur lors de l\'envoi', 'error');
            } finally {
                submitButton.innerHTML = originalText;
                submitButton.disabled = false;
            }
        });
    }
    
    // ===== OPTIMISATIONS PERFORMANCE =====
    setupPerformanceOptimizations() {
        // Lazy loading des images
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        if (img.dataset.src) {
                            img.src = img.dataset.src;
                            img.classList.remove('lazy');
                            imageObserver.unobserve(img);
                        }
                    }
                });
            });
            
            $$('img[data-src]').forEach(img => imageObserver.observe(img));
        }
        
        // Scroll to top button
        this.createScrollToTopButton();
    }
    
    createScrollToTopButton() {
        const scrollBtn = document.createElement('button');
        scrollBtn.innerHTML = '<i class="fas fa-arrow-up"></i>';
        scrollBtn.className = 'scroll-to-top';
        scrollBtn.style.cssText = `
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            width: 3rem;
            height: 3rem;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 50%;
            display: none;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: var(--shadow-lg);
            transition: var(--transition);
            z-index: var(--z-fixed);
        `;
        
        document.body.appendChild(scrollBtn);
        
        // Show/hide au scroll
        window.addEventListener('scroll', () => {
            scrollBtn.style.display = window.scrollY > 500 ? 'flex' : 'none';
        });
        
        // Scroll to top
        scrollBtn.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
    
    // ===== UTILITAIRES =====
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    
    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'info-circle'}"></i>
            <span>${message}</span>
        `;
        
        notification.style.cssText = `
            position: fixed;
            top: 2rem;
            right: 2rem;
            background: ${type === 'success' ? 'var(--success)' : 'var(--primary)'};
            color: white;
            padding: 1rem 1.5rem;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            z-index: var(--z-tooltip);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            animation: slideInRight 0.3s ease-out;
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.animation = 'slideOutRight 0.3s ease-out';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
}

// ===== INITIALISATION =====
document.addEventListener('DOMContentLoaded', () => {
    new ModernPortfolio();
});

// Styles pour les animations
const animationStyles = document.createElement('style');
animationStyles.textContent = `
    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    @keyframes slideOutRight {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
    
    .scroll-to-top:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-xl);
    }
`;
document.head.appendChild(animationStyles);

console.log('🎉 Portfolio moderne chargé avec succès!');
