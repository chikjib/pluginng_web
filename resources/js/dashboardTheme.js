// The old theme bound events before Vite's lazy-loaded layout existed.
// Delegated handlers work with Vue-created elements and can be disposed on logout.
export function initializeDashboardTheme() {
    const collapseMenus = () => document.querySelectorAll('.sidebar .collapse.show').forEach(menu => menu.classList.remove('show'));
    const scrollButton = () => {
        const button = document.querySelector('.scroll-to-top');
        if (button) button.style.display = window.scrollY > 100 ? 'inline' : 'none';
    };
    const click = event => {
        if (event.target.closest?.('#sidebarToggle, #sidebarToggleTop')) {
            document.body.classList.toggle('sidebar-toggled');
            document.querySelectorAll('.sidebar').forEach(sidebar => sidebar.classList.toggle('toggled'));
            if (document.body.classList.contains('sidebar-toggled')) collapseMenus();
        }
        if (event.target.closest?.('a.scroll-to-top')) {
            event.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    };
    const resize = () => { if (window.innerWidth < 768) collapseMenus(); };
    document.addEventListener('click', click);
    window.addEventListener('resize', resize);
    window.addEventListener('scroll', scrollButton, { passive: true });
    const version = document.getElementById('version-ruangadmin');
    if (version) version.textContent = 'Version 1.1';
    scrollButton();
    return () => {
        document.removeEventListener('click', click);
        window.removeEventListener('resize', resize);
        window.removeEventListener('scroll', scrollButton);
        document.body.classList.remove('sidebar-toggled');
    };
}
