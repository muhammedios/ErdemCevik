const menuButton = document.querySelector('.menu-toggle');
const mobileNav = document.getElementById('mobile-nav');
function closeMenu() { if (!menuButton || !mobileNav) return; mobileNav.hidden = true; menuButton.setAttribute('aria-expanded', 'false'); }
menuButton?.addEventListener('click', () => { const expanded = menuButton.getAttribute('aria-expanded') === 'true'; menuButton.setAttribute('aria-expanded', String(!expanded)); mobileNav.hidden = expanded; });
document.addEventListener('keydown', event => { if (event.key === 'Escape' && mobileNav && !mobileNav.hidden) { closeMenu(); menuButton.focus(); } });
window.matchMedia('(min-width: 1201px)').addEventListener('change', event => { if (event.matches) closeMenu(); });
document.querySelectorAll('.faq-item').forEach(item => item.addEventListener('toggle', () => { if (item.open) document.querySelectorAll('.faq-item').forEach(other => { if (other !== item) other.open = false; }); }));

// Google's place card disappears below 400px. Keep a full native map viewport,
// scaled into the available space, including its original controls and credits.
document.querySelectorAll('.contact-location .contact-map').forEach(map => {
    const frame = map.querySelector('iframe');
    if (!frame) return;
    const fitMap = () => {
        const width = map.clientWidth;
        if (!width) return;
        if (width < 420) {
            const scale = width / 420;
            frame.style.width = '420px';
            frame.style.height = `${map.clientHeight / scale}px`;
            frame.style.transform = `scale(${scale})`;
        } else {
            frame.style.removeProperty('width');
            frame.style.removeProperty('height');
            frame.style.removeProperty('transform');
        }
    };
    fitMap();
    if ('ResizeObserver' in window) new ResizeObserver(fitMap).observe(map);
    else window.addEventListener('resize', fitMap);
});
