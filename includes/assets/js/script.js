// ============================================
// BLOG DYNAMIQUE - JAVASCRIPT
// ============================================

// ============================================
// MODE SOMBRE
// ============================================
function toggleDarkMode() {
    const html = document.documentElement;
    const icon = document.getElementById('darkModeIcon');
    const isDark = html.getAttribute('data-bs-theme') === 'dark';
    
    if (isDark) {
        html.removeAttribute('data-bs-theme');
        icon.classList.remove('fa-sun');
        icon.classList.add('fa-moon');
        document.cookie = 'dark_mode=false; path=/; max-age=31536000';
    } else {
        html.setAttribute('data-bs-theme', 'dark');
        icon.classList.remove('fa-moon');
        icon.classList.add('fa-sun');
        document.cookie = 'dark_mode=true; path=/; max-age=31536000';
    }
}

// Initialiser le mode sombre
document.addEventListener('DOMContentLoaded', function() {
    const darkMode = document.cookie.includes('dark_mode=true');
    if (darkMode) {
        document.documentElement.setAttribute('data-bs-theme', 'dark');
        const icon = document.getElementById('darkModeIcon');
        if (icon) {
            icon.classList.remove('fa-moon');
            icon.classList.add('fa-sun');
        }
    }
});

// ============================================
// SCROLL TO TOP
// ============================================
const scrollTopBtn = document.getElementById('scrollTop');

window.addEventListener('scroll', function() {
    if (window.pageYOffset > 300) {
        scrollTopBtn.classList.add('show');
    } else {
        scrollTopBtn.classList.remove('show');
    }
});

function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// ============================================
// ALERTS & NOTIFICATIONS
// ============================================
function showAlert(message, type = 'success') {
    const alertContainer = document.getElementById('alert-container');
    if (!alertContainer) return;
    
    const icons = {
        success: 'fa-check-circle',
        danger: 'fa-exclamation-circle',
        warning: 'fa-exclamation-triangle',
        info: 'fa-info-circle'
    };
    
    const alertHTML = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            <i class="fas ${icons[type]} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    alertContainer.innerHTML = alertHTML;
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        const alert = alertContainer.querySelector('.alert');
        if (alert) {
            alert.classList.remove('show');
            setTimeout(() => alert.remove(), 150);
        }
    }, 5000);
}

// ============================================
// CONFIRMATION SUPPRESSION
// ============================================
function confirmDelete(message = 'Êtes-vous sûr de vouloir supprimer cet élément ?') {
    return confirm(message);
}

// ============================================
// VALIDATION FORMULAIRE
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('is-invalid');
                } else {
                    field.classList.remove('is-invalid');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                showAlert('Veuillez remplir tous les champs obligatoires.', 'danger');
            }
        });
    });
    
    // Validation en temps réel
    const requiredFields = document.querySelectorAll('[required]');
    requiredFields.forEach(field => {
        field.addEventListener('input', function() {
            if (this.value.trim()) {
                this.classList.remove('is-invalid');
            }
        });
    });
});

// ============================================
// ANIMATION AU SCROLL
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    const animatedElements = document.querySelectorAll('.article-card, .comment-card');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
            }
        });
    }, {
        threshold: 0.1
    });
    
    animatedElements.forEach(element => {
        observer.observe(element);
    });
});

// ============================================
// RECHERCHE EN DIRECT
// ============================================
let searchTimeout;
const searchInput = document.querySelector('.search-box input');

if (searchInput) {
    searchInput.addEventListener('input', function(e) {
        clearTimeout(searchTimeout);
        const searchTerm = e.target.value;
        
        if (searchTerm.length >= 2) {
            searchTimeout = setTimeout(() => {
                performSearch(searchTerm);
            }, 300);
        }
    });
}

function performSearch(searchTerm) {
    const articles = document.querySelectorAll('.article-card');
    let foundCount = 0;
    
    articles.forEach(article => {
        const title = article.querySelector('.article-title').textContent.toLowerCase();
        const excerpt = article.querySelector('.article-excerpt').textContent.toLowerCase();
        
        if (title.includes(searchTerm.toLowerCase()) || excerpt.includes(searchTerm.toLowerCase())) {
            article.style.display = 'block';
            foundCount++;
        } else {
            article.style.display = 'none';
        }
    });
    
    // Afficher un message si aucun résultat
    const noResultsMsg = document.getElementById('no-results');
    if (foundCount === 0) {
        if (!noResultsMsg) {
            const message = document.createElement('div');
            message.id = 'no-results';
            message.className = 'alert alert-info text-center fade-in';
            message.innerHTML = '<i class="fas fa-search me-2"></i>Aucun article trouvé pour cette recherche.';
            document.querySelector('.search-box').after(message);
        }
    } else if (noResultsMsg) {
        noResultsMsg.remove();
    }
}

// ============================================
// PREVIEW IMAGE
// ============================================
function previewImage(input, previewId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById(previewId);
            if (preview) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// ============================================
// AUTO-RESIZE TEXTAREA
// ============================================
const textareas = document.querySelectorAll('textarea');
textareas.forEach(textarea => {
    textarea.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = this.scrollHeight + 'px';
    });
});

// ============================================
// COMPTEUR DE CARACTÈRES
// ============================================
function setupCharacterCounter(textareaId, counterId, maxLength) {
    const textarea = document.getElementById(textareaId);
    const counter = document.getElementById(counterId);
    
    if (textarea && counter) {
        textarea.addEventListener('input', function() {
            const remaining = maxLength - this.value.length;
            counter.textContent = `${remaining} caractères restants`;
            
            if (remaining < 50) {
                counter.classList.add('text-danger');
                counter.classList.remove('text-muted');
            } else {
                counter.classList.remove('text-danger');
                counter.classList.add('text-muted');
            }
        });
    }
}

// ============================================
// TOGGLE PASSWORD VISIBILITY
// ============================================
function togglePassword(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    
    if (input && icon) {
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
}

// ============================================
// LOADING SPINNER
// ============================================
function showLoading(button) {
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Chargement...';
    button.disabled = true;
    return originalText;
}

function hideLoading(button, originalText) {
    button.innerHTML = originalText;
    button.disabled = false;
}

// ============================================
// SCROLL SMOOTH ANCHOR LINKS
// ============================================
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        const href = this.getAttribute('href');
        if (href !== '#') {
            e.preventDefault();
            const target = document.querySelector(href);
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        }
    });
});

// ============================================
// COPY TO CLIPBOARD
// ============================================
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showAlert('Copié dans le presse-papier !', 'success');
    }).catch(() => {
        showAlert('Erreur lors de la copie', 'danger');
    });
}

// ============================================
// INITIALIZATION
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser les tooltips Bootstrap
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
    
    // Initialiser les popovers Bootstrap
    const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
    const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl));
    
    console.log('🚀 Blog Dynamique - JavaScript chargé avec succès !');
});