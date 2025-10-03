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
                description: "Algorithme de recommandation utilisant le deep learning pour personnaliser l'expérience utilisateur.",
                category: "ml",
                tags: ["Python", "TensorFlow", "Deep Learning"],
                image: "fas fa-brain"
            },
            {
                id: 2,
                title: "Kin-Immo Manager", 
                description: "Système de gestion immobilière moderne pour Kinshasa, RDC.",
                category: "web",
                tags: ["PHP", "MySQL", "JavaScript"],
                image: "fas fa-building"
            },
            {
                id: 3,
                title: "Analyse Prédictive",
                description: "Modèle de prédiction des ventes utilisant des techniques avancées.",
                category: "data",
                tags: ["Python", "Scikit-learn", "Pandas"],
                image: "fas fa-chart-line"
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

// Render projects
function renderProjects(projects) {
    if (!projectsGrid) return;
    
    projectsGrid.innerHTML = projects.map(project => `
        <div class="project-card" data-category="${project.category}">
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

// Filter projects
function filterProjects(category) {
    const filteredProjects = category === 'all' 
        ? projectsData 
        : projectsData.filter(project => project.category === category);
    
    renderProjects(filteredProjects);
    
    // Animate new projects
    setTimeout(() => {
        const projectCards = document.querySelectorAll('.project-card');
        projectCards.forEach((card, index) => {
            setTimeout(() => {
                card.classList.add('fade-in-up');
            }, index * 100);
        });
    }, 100);
}

// Filter button event listeners
filterButtons.forEach(button => {
    button.addEventListener('click', () => {
        // Update active filter button
        filterButtons.forEach(btn => btn.classList.remove('active'));
        button.classList.add('active');
        
        // Filter projects
        const category = button.getAttribute('data-filter');
        filterProjects(category);
    });
});

// Initialize projects on page load
document.addEventListener('DOMContentLoaded', () => {
    // Load projects directly from static data to ensure demos work
    renderProjects(projectsData);
    
    // Initialize filter functionality
    const filterButtons = document.querySelectorAll('.filter-btn');
    filterButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            // Remove active class from all buttons
            filterButtons.forEach(b => b.classList.remove('active'));
            // Add active class to clicked button
            btn.classList.add('active');
            // Filter projects
            const category = btn.getAttribute('data-filter');
            filterProjects(category);
        });
    });
    
    // Secret admin access shortcut
    let keySequence = '';
    document.addEventListener('keydown', (e) => {
        keySequence += e.key.toLowerCase();
        
        // Keep only last 5 characters
        if (keySequence.length > 5) {
            keySequence = keySequence.slice(-5);
        }
        
        // Check for secret sequence "admin"
        if (keySequence.includes('admin')) {
            // Show subtle notification
            showAdminAccessNotification();
            keySequence = ''; // Reset
        }
        
        // Reset sequence after 3 seconds of inactivity
        setTimeout(() => {
            keySequence = '';
        }, 3000);
    });
});

// Show admin access notification
function showAdminAccessNotification() {
    // Create notification element
    const notification = document.createElement('div');
    notification.innerHTML = `
        <div style="
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #ffc107, #ff9800);
            color: #2c3e50;
            padding: 15px 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            z-index: 10000;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        " onclick="window.location.href='admin-login.html'">
            <i class="fas fa-user-shield" style="margin-right: 8px;"></i>
            Accès Administrateur Détecté - Cliquez ici
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 5000);
}

// Load projects from PHP API
async function loadProjectsFromAPI() {
    try {
        const response = await fetch('app/controllers/ProjectController.php?action=getAllProjects');
        const result = await response.json();
        
        if (result.success && result.data) {
            renderProjects(result.data);
        } else {
            // Fallback to static data if API fails
            renderProjects(projectsData);
        }
    } catch (error) {
        console.log('API non disponible, utilisation des données statiques');
        renderProjects(projectsData);
    }
}

// ===== CONTACT FORM HANDLING =====

if (contactForm) {
    contactForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const formData = new FormData(contactForm);
        const submitButton = contactForm.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;
        
        // Show loading state
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Envoi en cours...';
        submitButton.disabled = true;
        
        try {
            const response = await fetch('app/controllers/ContactController.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                showNotification('Message envoyé avec succès!', 'success');
                contactForm.reset();
            } else {
                showNotification('Erreur lors de l\'envoi du message.', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification('Erreur de connexion.', 'error');
        } finally {
            // Reset button state
            submitButton.innerHTML = originalText;
            submitButton.disabled = false;
        }
    });
}

// ===== NOTIFICATION SYSTEM =====

function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => notification.remove());
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
            <span>${message}</span>
        </div>
        <button class="notification-close">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    // Add styles
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6'};
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 0.5rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        z-index: 9999;
        display: flex;
        align-items: center;
        gap: 1rem;
        max-width: 400px;
        animation: slideInRight 0.3s ease-out;
    `;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Close button functionality
    const closeButton = notification.querySelector('.notification-close');
    closeButton.addEventListener('click', () => {
        notification.style.animation = 'slideOutRight 0.3s ease-out';
        setTimeout(() => notification.remove(), 300);
    });
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.style.animation = 'slideOutRight 0.3s ease-out';
            setTimeout(() => notification.remove(), 300);
        }
    }, 5000);
}

// Add notification animations to CSS
const notificationStyles = document.createElement('style');
notificationStyles.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    .notification-content {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex: 1;
    }
    
    .notification-close {
        background: none;
        border: none;
        color: white;
        cursor: pointer;
        padding: 0.25rem;
        border-radius: 0.25rem;
        transition: background-color 0.2s;
    }
    
    .notification-close:hover {
        background-color: rgba(255, 255, 255, 0.1);
    }
`;
document.head.appendChild(notificationStyles);

// ===== SCROLL TO TOP FUNCTIONALITY =====

// Create scroll to top button
const scrollToTopButton = document.createElement('button');
scrollToTopButton.innerHTML = '<i class="fas fa-arrow-up"></i>';
scrollToTopButton.className = 'scroll-to-top';
scrollToTopButton.style.cssText = `
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: white;
    border: none;
    border-radius: 50%;
    cursor: pointer;
    display: none;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    z-index: 1000;
`;

document.body.appendChild(scrollToTopButton);

// Show/hide scroll to top button
window.addEventListener('scroll', () => {
    if (window.scrollY > 500) {
        scrollToTopButton.style.display = 'flex';
    } else {
        scrollToTopButton.style.display = 'none';
    }
});

// Scroll to top functionality
scrollToTopButton.addEventListener('click', () => {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
});

// ===== LOADING ANIMATION =====

// Show loading spinner on page load
window.addEventListener('load', () => {
    const loader = document.querySelector('.page-loader');
    if (loader) {
        loader.style.opacity = '0';
        setTimeout(() => {
            loader.style.display = 'none';
        }, 500);
    }
});

// ===== PERFORMANCE OPTIMIZATIONS =====

// Lazy loading for images
if ('IntersectionObserver' in window) {
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.classList.remove('lazy');
                observer.unobserve(img);
            }
        });
    });

    const lazyImages = document.querySelectorAll('img[data-src]');
    lazyImages.forEach(img => imageObserver.observe(img));
}

// Debounce function for performance
function debounce(func, wait) {
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

// Debounced scroll handler
const debouncedScrollHandler = debounce(() => {
    updateActiveNavigation();
}, 10);

window.addEventListener('scroll', debouncedScrollHandler);

// ===== ACCESSIBILITY IMPROVEMENTS =====

// Keyboard navigation for mobile menu
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && navMenu.classList.contains('active')) {
        navMenu.classList.remove('active');
        navToggle.classList.remove('active');
        navToggle.focus();
    }
});

// Focus management for mobile menu
navToggle.addEventListener('click', () => {
    if (navMenu.classList.contains('active')) {
        // Focus first menu item when menu opens
        const firstMenuItem = navMenu.querySelector('.nav-link');
        if (firstMenuItem) {
            setTimeout(() => firstMenuItem.focus(), 100);
        }
    }
});

// ===== THEME TOGGLE (OPTIONAL DARK MODE) =====

// Check for saved theme preference or default to light mode
const currentTheme = localStorage.getItem('theme') || 'light';
document.documentElement.setAttribute('data-theme', currentTheme);

// Theme toggle function
function toggleTheme() {
    const currentTheme = document.documentElement.getAttribute('data-theme');
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    
    document.documentElement.setAttribute('data-theme', newTheme);
    localStorage.setItem('theme', newTheme);
}

// Add theme toggle button (optional)
const themeToggle = document.createElement('button');
themeToggle.innerHTML = '<i class="fas fa-moon"></i>';
themeToggle.className = 'theme-toggle';
themeToggle.setAttribute('aria-label', 'Toggle dark mode');
themeToggle.addEventListener('click', toggleTheme);

// You can add the theme toggle to the navigation if desired
// navbar.appendChild(themeToggle);

console.log('🚀 Site professionnel chargé avec succès!');
console.log('💡 Toutes les fonctionnalités sont opérationnelles.');
