/* ================================================================
   VELORA MARKETPLACE — Shared JavaScript
   Handles: dark mode, scroll animations, navbar, mobile menu, toast
   ================================================================ */

(function () {
    'use strict';

    /* ── DARK MODE ── */
    const HTML = document.documentElement;
    const THEME_KEY = 'velora_theme';

    function applyTheme(theme) {
        HTML.setAttribute('data-theme', theme);
        localStorage.setItem(THEME_KEY, theme);
        // Update toggle icon(s) on page
        document.querySelectorAll('.theme-toggle').forEach(btn => {
            btn.innerHTML = theme === 'dark'
                ? '<i class="bi bi-sun-fill"></i>'
                : '<i class="bi bi-moon-fill"></i>';
            btn.title = theme === 'dark' ? 'Switch to Light Mode' : 'Switch to Dark Mode';
        });
    }

    function toggleTheme() {
        const current = HTML.getAttribute('data-theme') || 'light';
        applyTheme(current === 'dark' ? 'light' : 'dark');
    }

    // Init theme on load
    const savedTheme = localStorage.getItem(THEME_KEY)
        || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
    applyTheme(savedTheme);

    // Bind toggle buttons after DOM ready
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.theme-toggle').forEach(btn => {
            btn.addEventListener('click', toggleTheme);
        });

        /* ── NAVBAR SCROLL SHRINK ── */
        const navbar = document.querySelector('.nx-navbar');
        if (navbar) {
            const onScroll = () => navbar.classList.toggle('scrolled', window.scrollY > 20);
            window.addEventListener('scroll', onScroll, { passive: true });
            onScroll();
        }

        /* ── MOBILE MENU ── */
        const hamburger = document.querySelector('.nx-hamburger');
        const mobileMenu = document.querySelector('.nx-mobile-menu');
        if (hamburger && mobileMenu) {
            hamburger.addEventListener('click', () => {
                mobileMenu.classList.toggle('open');
                const spans = hamburger.querySelectorAll('span');
                const isOpen = mobileMenu.classList.contains('open');
                if (isOpen) {
                    spans[0].style.transform = 'translateY(7px) rotate(45deg)';
                    spans[1].style.opacity = '0';
                    spans[2].style.transform = 'translateY(-7px) rotate(-45deg)';
                } else {
                    spans.forEach(s => { s.style.transform = ''; s.style.opacity = ''; });
                }
            });
            // Close on link click
            mobileMenu.querySelectorAll('a').forEach(a => {
                a.addEventListener('click', () => {
                    mobileMenu.classList.remove('open');
                    hamburger.querySelectorAll('span').forEach(s => {
                        s.style.transform = ''; s.style.opacity = '';
                    });
                });
            });
        }

        /* ── SCROLL REVEAL ── */
        const reveals = document.querySelectorAll('.reveal');
        if (reveals.length) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.05, rootMargin: '0px 0px 60px 0px' });
            reveals.forEach(el => observer.observe(el));

            // Safety-net: reveal any remaining hidden elements after 2.5s
            setTimeout(() => {
                document.querySelectorAll('.reveal:not(.visible)').forEach(el => {
                    el.classList.add('visible');
                });
            }, 2500);
        }

        /* ── COUNTER ANIMATION ── */
        const counters = document.querySelectorAll('[data-count]');
        if (counters.length) {
            const countObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        animateCounter(entry.target);
                        countObserver.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.3, rootMargin: '0px 0px 80px 0px' });
            counters.forEach(el => countObserver.observe(el));
        }

        /* ── SMOOTH ACTIVE NAV LINK ── */
        const currentPath = window.location.pathname.split('/').pop();
        document.querySelectorAll('.nx-nav-links a, .nx-mobile-menu a').forEach(a => {
            if (a.getAttribute('href') === currentPath) a.classList.add('active');
        });
    });

    /* ── COUNTER ANIMATION HELPER ── */
    function animateCounter(el) {
        const target = parseFloat(el.dataset.count);
        const suffix = el.dataset.suffix || '';
        const prefix = el.dataset.prefix || '';
        const decimals = el.dataset.decimals ? parseInt(el.dataset.decimals) : 0;
        const duration = 1800;
        const start = performance.now();

        function update(now) {
            const elapsed = now - start;
            const progress = Math.min(elapsed / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3); // ease-out cubic
            const value = eased * target;
            el.textContent = prefix + value.toFixed(decimals) + suffix;
            if (progress < 1) requestAnimationFrame(update);
        }
        requestAnimationFrame(update);
    }

    /* ── GLOBAL TOAST ── */
    window.showToast = function (msg, type = 'default') {
        let wrap = document.getElementById('nxToastWrap');
        if (!wrap) {
            wrap = document.createElement('div');
            wrap.id = 'nxToastWrap';
            wrap.className = 'nx-toast-wrap';
            document.body.appendChild(wrap);
        }
        const icon = type === 'success' ? '<i class="bi bi-check-circle-fill" style="color:#10B981"></i>'
                   : type === 'error'   ? '<i class="bi bi-x-circle-fill" style="color:#EF4444"></i>'
                   : '<i class="bi bi-info-circle-fill" style="color:#5B3FF8"></i>';
        const t = document.createElement('div');
        t.className = 'nx-toast';
        t.innerHTML = `${icon} <span>${msg}</span>`;
        wrap.appendChild(t);
        setTimeout(() => {
            t.style.transition = 'opacity .3s, transform .3s';
            t.style.opacity = '0';
            t.style.transform = 'translateY(10px)';
            setTimeout(() => t.remove(), 300);
        }, 3000);
    };

})();
