import './bootstrap';
import { initGlobe } from './globe';
import QRCode from 'qrcode';

window.QRCode = QRCode;

// Inicializar globo quando DOM carregar
document.addEventListener('DOMContentLoaded', () => {
    initGlobe();
});
