import './bootstrap';
import { initGlobe } from './globe';

// Inicializar globo quando DOM carregar
document.addEventListener('DOMContentLoaded', () => {
    initGlobe();
});
