import gsap from 'gsap';
import { SplitText } from 'gsap/SplitText';

gsap.registerPlugin(SplitText);

let mm;

// ------------------------------------------------------------------ //
//  Detectare pagină
// ------------------------------------------------------------------ //

function detectPage() {
    let path = window.location.pathname.replace(/\/+$/, '') || '/';

    // Verifică mai întâi rutele publice
    if (path === '/') return 'welcome';
    if (path === '/login') return 'login';
    if (path === '/register') return 'register';

    // Rutele autentificate sunt sub prefixul /app
    if (path.startsWith('/app')) {
        path = path.replace('/app', '') || '/';
    }

    if (path === '/') return 'dashboard';
    if (path.startsWith('/pipeline')) return 'pipeline';
    if (path.startsWith('/keywords')) return 'keywords';
    if (path.startsWith('/cvs')) return 'cvs';
    if (path.startsWith('/skills')) return 'skills';
    if (path.startsWith('/analytics')) return 'analytics';
    if (path.startsWith('/applications')) return 'applications';
    if (path.startsWith('/ai-settings')) return 'ai-settings';
    if (path.startsWith('/rules')) return 'rules';

    return 'unknown';
}

// ------------------------------------------------------------------ //
//  Animații per pagină
//  Notă: folosim direct gsap.from / gsap.to — gsap.context()
//  colectează automat toate tween-urile create în callback-ul său.
//  Pentru cleanup manual (event listeners) folosim context.add().
// ------------------------------------------------------------------ //

function welcomeAnimations(ctx) {
    // 1. Logo Pulse
    gsap.from('.welcome-logo', {
        scale: 0,
        rotation: -180,
        duration: 0.8,
        ease: 'back.out(2)',
    });

    // 2. Title Reveal — SplitText (se face revert în cleanup)
    const titleSplit = new SplitText('.welcome-title', { type: 'words' });
    gsap.from(titleSplit.words, {
        opacity: 0,
        y: 30,
        stagger: 0.06,
        duration: 0.5,
    });
    ctx.add(() => titleSplit.revert());

    // 3. Subtitle Fade
    gsap.from('.welcome-subtitle', {
        autoAlpha: 0,
        y: 15,
        delay: 0.4,
        duration: 0.6,
    });

    // 4. CTA Buttons Stagger
    gsap.from('.welcome-cta', {
        scale: 0,
        duration: 0.5,
        stagger: 0.15,
        ease: 'back.out(2.5)',
        delay: 0.6,
    });

    // 5. Gradient Spin (continuu)
    gsap.to('.spinning-gradient', {
        rotation: 360,
        duration: 12,
        ease: 'none',
        repeat: -1,
    });

    // 6. Nav Links Stagger
    gsap.from('.welcome-nav a', {
        y: -20,
        autoAlpha: 0,
        stagger: 0.08,
        duration: 0.3,
    });
}

function authAnimations(ctx) {
    // 8. Card Slide In
    gsap.from('.auth-card', {
        x: 40,
        autoAlpha: 0,
        duration: 0.6,
        ease: 'power3.out',
    });

    // 9. Fields Stagger
    gsap.from('.auth-field', {
        y: 15,
        autoAlpha: 0,
        stagger: 0.08,
        duration: 0.4,
    });

    // 10. Button entrance
    gsap.from('.auth-btn', {
        scale: 0.9,
        autoAlpha: 0,
        duration: 0.4,
        delay: 0.3,
    });

    // 12. Quote Reveal (doar pe split layout)
    const quoteEl = document.querySelector('.auth-quote');
    if (quoteEl) {
        const quoteSplit = new SplitText('.auth-quote', { type: 'words' });
        gsap.from(quoteSplit.words, {
            opacity: 0,
            y: 20,
            stagger: 0.04,
            duration: 0.5,
        });
        ctx.add(() => quoteSplit.revert());
    }
}

function dashboardAnimations(ctx) {
    // 15. Stats Cards Stagger
    gsap.from('.stat-card', {
        y: 30,
        scale: 0.95,
        autoAlpha: 0,
        stagger: 0.1,
        duration: 0.5,
        ease: 'power2.out',
    });

    // 14. Stats Counter
    document.querySelectorAll('.stat-number').forEach((el) => {
        const raw = el.getAttribute('data-target') || el.textContent.replace(/[,%]/g, '');
        const finalValue = parseFloat(raw) || 0;
        const isPercent = el.textContent.includes('%');
        const obj = { val: 0 };
        gsap.to(obj, {
            val: finalValue,
            duration: 1.5,
            ease: 'power2.out',
            onUpdate: () => {
                el.textContent = isPercent
                    ? obj.val.toFixed(1) + '%'
                    : Math.round(obj.val).toLocaleString();
            },
        });
    });

    // 17. Activity List Stagger
    gsap.from('.activity-item', {
        x: -20,
        autoAlpha: 0,
        stagger: 0.08,
        duration: 0.4,
    });

    // 18. Quick Actions Hover
    document.querySelectorAll('.quick-action').forEach((el) => {
        const onEnter = () => gsap.to(el, { x: 5, duration: 0.2, overwrite: 'auto' });
        const onLeave = () => gsap.to(el, { x: 0, duration: 0.2, overwrite: 'auto' });
        el.addEventListener('mouseenter', onEnter);
        el.addEventListener('mouseleave', onLeave);
        ctx.add(() => {
            el.removeEventListener('mouseenter', onEnter);
            el.removeEventListener('mouseleave', onLeave);
        });
    });

    // 19. Upcoming Sends Stagger
    gsap.from('.upcoming-item', {
        y: 15,
        autoAlpha: 0,
        stagger: 0.08,
        delay: 0.5,
        duration: 0.4,
    });
}

function pipelineAnimations(ctx) {
    // 20. Step Active Pulse
    const stepIcon = document.querySelector('.step-active .step-icon');
    if (stepIcon) {
        gsap.to(stepIcon, {
            scale: 1.15,
            duration: 0.8,
            yoyo: true,
            repeat: -1,
            ease: 'sine.inOut',
        });
    }

    // 22. Fetch Button Pulse
    const fetchBtn = document.querySelector('.fetch-btn');
    if (fetchBtn) {
        gsap.to('.fetch-btn:not(:disabled)', {
            boxShadow: '0 0 12px rgba(59,130,246,0.5)',
            duration: 1,
            yoyo: true,
            repeat: -1,
            ease: 'sine.inOut',
        });
    }
}

function keywordsAnimations(ctx) {
    // 24. Cards Stagger
    gsap.from('.keyword-card', {
        scale: 0.9,
        y: 20,
        autoAlpha: 0,
        stagger: 0.08,
        ease: 'back.out(1.5)',
        duration: 0.5,
    });

    // 25. Card Hover Lift
    document.querySelectorAll('.keyword-card').forEach((el) => {
        const onEnter = () => gsap.to(el, {
            y: -4,
            boxShadow: '0 8px 25px rgba(0,0,0,0.1)',
            duration: 0.2,
            overwrite: 'auto',
        });
        const onLeave = () => gsap.to(el, {
            y: 0,
            boxShadow: 'none',
            duration: 0.2,
            overwrite: 'auto',
        });
        el.addEventListener('mouseenter', onEnter);
        el.addEventListener('mouseleave', onLeave);
        ctx.add(() => {
            el.removeEventListener('mouseenter', onEnter);
            el.removeEventListener('mouseleave', onLeave);
        });
    });

    // 29. Empty State Icon Bob
    const emptyIcon = document.querySelector('.empty-icon');
    if (emptyIcon) {
        gsap.to(emptyIcon, {
            y: -6,
            duration: 2,
            ease: 'sine.inOut',
            yoyo: true,
            repeat: -1,
        });
    }
}

function skillsAnimations(ctx) {
    // 34. Skill Cards 3D Flip
    gsap.from('.skill-card', {
        rotationY: 90,
        autoAlpha: 0,
        stagger: 0.06,
        duration: 0.5,
        ease: 'power2.out',
        transformOrigin: 'left center',
    });
}

function analyticsAnimations(ctx) {
    // 39. Analytics Stats Stagger
    gsap.from('.analytics-stat', {
        y: 30,
        scale: 0.95,
        autoAlpha: 0,
        stagger: 0.1,
        duration: 0.5,
        ease: 'power2.out',
    });

    // 41. Top Keywords Stagger
    gsap.from('.top-keyword', {
        x: -15,
        autoAlpha: 0,
        stagger: 0.05,
        duration: 0.4,
    });

    // 38. Animated Bars
    gsap.from('.keyword-bar', {
        scaleX: 0,
        duration: 0.8,
        ease: 'power2.out',
        transformOrigin: 'left center',
        stagger: 0.08,
    });
}

function tableAnimations(ctx) {
    // 23. Table Rows Stagger
    gsap.from('.table-row-animate', {
        y: 10,
        autoAlpha: 0,
        stagger: 0.04,
        duration: 0.3,
    });
}

function applicationsAnimations(ctx) {
    tableAnimations(ctx);

    // 43. Status Badge Pulse
    gsap.to('.status-badge', {
        scale: 1.1,
        duration: 0.3,
        yoyo: true,
        repeat: 1,
    });
}

function sidebarAnimations(ctx) {
    // 46. Sidebar Items Stagger
    gsap.from('.sidebar-item', {
        x: -15,
        autoAlpha: 0,
        stagger: 0.04,
        duration: 0.3,
        delay: 0.2,
    });
}

// ------------------------------------------------------------------ //
//  Harta pagini → funcții animație
// ------------------------------------------------------------------ //

const PAGE_ANIMATIONS = {
    welcome: welcomeAnimations,
    login: authAnimations,
    register: authAnimations,
    dashboard: dashboardAnimations,
    pipeline: pipelineAnimations,
    keywords: keywordsAnimations,
    cvs: tableAnimations,
    skills: skillsAnimations,
    analytics: analyticsAnimations,
    applications: applicationsAnimations,
    'ai-settings': () => {},
    rules: tableAnimations,
};

// ------------------------------------------------------------------ //
//  Runner principal
// ------------------------------------------------------------------ //

function runPageAnimations() {
    const page = detectPage();
    const animFn = PAGE_ANIMATIONS[page];
    if (!animFn) return;

    // Curăță matchMedia anterior dacă există
    if (mm) {
        mm.revert();
        mm = null;
    }

    mm = gsap.matchMedia();

    mm.add('(prefers-reduced-motion: no-preference)', () => {
        // gsap.context() primește self ca parametru — e contextul însuși
        return gsap.context((self) => {
            animFn(self);
        });
    });

    // Pentru reduce-motion: context gol (fără animații)
    mm.add('(prefers-reduced-motion: reduce)', () => {});
}

// ------------------------------------------------------------------ //
//  Animație modal (declanșată la Livewire component update)
// ------------------------------------------------------------------ //

function animateModal(container) {
    const backdrop = container.querySelector('[class*="modal-backdrop"], [role="dialog"] > div:first-child');
    const panel = container.querySelector('[role="dialog"] > div:last-child, [role="dialog"] > div:nth-child(2), .modal-box');

    gsap.from(backdrop, { autoAlpha: 0, duration: 0.2 });
    gsap.from(panel, {
        scale: 0.9,
        y: 20,
        autoAlpha: 0,
        duration: 0.3,
        ease: 'back.out(1.7)',
    });
}

function runComponentAnimations(component) {
    const el = component.el;
    if (el && (el.querySelector('[role="dialog"]') || el.closest('[role="dialog"]'))) {
        animateModal(el);
    }
}

// ------------------------------------------------------------------ //
//  Inițializare
// ------------------------------------------------------------------ //

export function initAnimations() {
    // Așteaptă ca DOM-ul să fie gata înainte de prima rulare
    const run = () => {
        runPageAnimations();

        // Rulează din nou după wire:navigate (SPA)
        document.addEventListener('livewire:navigated', runPageAnimations);

        // Rulează la update de componentă Livewire (modale, etc.)
        document.addEventListener('livewire:initialized', () => {
            if (typeof Livewire !== 'undefined') {
                Livewire.hook('component.updated', (component) => {
                    runComponentAnimations(component);
                });
            }
        });
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', run);
    } else {
        run();
    }
}
