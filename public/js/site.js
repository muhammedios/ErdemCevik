const menuButton = document.querySelector('.menu-toggle');
const mobileMenu = document.getElementById('mobile-menu');
function closeMenu() { mobileMenu?.close(); }
menuButton?.addEventListener('click', () => {
    if (!mobileMenu) return;
    const headerBottom = document.querySelector('.header')?.getBoundingClientRect().bottom || 80;
    mobileMenu.style.setProperty('--menu-top', `${Math.max(0, headerBottom)}px`);
    mobileMenu.showModal();
    menuButton.setAttribute('aria-expanded', 'true');
    document.body.classList.add('menu-open');
});
mobileMenu?.querySelector('.mobile-menu-close')?.addEventListener('click', closeMenu);
mobileMenu?.addEventListener('click', event => {
    const rect = mobileMenu.getBoundingClientRect();
    if (event.clientX < rect.left || event.clientX > rect.right || event.clientY < rect.top || event.clientY > rect.bottom) closeMenu();
});
mobileMenu?.addEventListener('close', () => {
    menuButton?.setAttribute('aria-expanded', 'false');
    document.body.classList.remove('menu-open');
    menuButton?.focus();
});
mobileMenu?.querySelectorAll('nav a').forEach(link => link.addEventListener('click', closeMenu));
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

const announcement = document.querySelector('.announcement-popup');
if (announcement) {
    const key = `clinic-popup:${announcement.dataset.popupKey}`;
    let seen = false;
    try { seen = sessionStorage.getItem(key) === 'seen'; } catch (_) {}
    const image = announcement.querySelector('img');
    const showAnnouncement = () => {
        if (seen || !image.naturalWidth) return;
        // Avoid opening two modal dialogs at once.
        if (mobileMenu?.open) { mobileMenu.addEventListener('close', showAnnouncement, { once: true }); return; }
        announcement.showModal();
        document.body.classList.add('announcement-open');
        seen = true;
        try { sessionStorage.setItem(key, 'seen'); } catch (_) {}
    };
    announcement.querySelector('.announcement-close').addEventListener('click', () => announcement.close());
    announcement.addEventListener('click', event => {
        if (event.target !== announcement) return;
        const rect = announcement.getBoundingClientRect();
        if (event.clientX < rect.left || event.clientX > rect.right || event.clientY < rect.top || event.clientY > rect.bottom) announcement.close();
    });
    announcement.addEventListener('close', () => document.body.classList.remove('announcement-open'));
    if (image.complete) showAnnouncement();
    else image.addEventListener('load', showAnnouncement, { once: true });
}
