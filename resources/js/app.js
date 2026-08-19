import { initAnimations } from './gsap-animations';
import { registerBlatUI } from './blatui-core.js';

console.log('[GSAP Animații] app.js loaded, initializing...');
initAnimations();
console.log('[GSAP Animații] initAnimations() called');

// Register BlatUI into Livewire's Alpine (no dark mode - Flux handles it)
document.addEventListener('alpine:init', () => {
    registerBlatUI(window.Alpine, { darkMode: false });
});
