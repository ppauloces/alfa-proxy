import './bootstrap';
import { initGlobe } from './globe';

// Dark Mode Toggle
function initDarkMode() {
    // Verificar preferência salva ou preferência do sistema
    const isDark = localStorage.getItem('darkMode') === 'true' ||
                   (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches);

    if (isDark) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
}

// Toggle dark mode
window.toggleDarkMode = function() {
    const html = document.documentElement;
    const isDark = html.classList.contains('dark');

    if (isDark) {
        html.classList.remove('dark');
        localStorage.setItem('darkMode', 'false');
    } else {
        html.classList.add('dark');
        localStorage.setItem('darkMode', 'true');
    }
}

// Inicializar quando DOM carregar
document.addEventListener('DOMContentLoaded', () => {
    initDarkMode();
    initGlobe();
});
