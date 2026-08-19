import { initAnimations } from './gsap-animations';
import { registerBlatUI } from './blatui-core.js';

console.log('[GSAP Animații] app.js loaded, initializing...');
initAnimations();
console.log('[GSAP Animații] initAnimations() called');

// Register BlatUI into Livewire's Alpine
document.addEventListener('alpine:init', () => {
    registerBlatUI(window.Alpine, { darkMode: 'class' });
});
