const menuToggle = document.querySelector('.menu-toggle');
const primaryNavigation = document.querySelector('#primary-navigation');
const mobileNavigation = window.matchMedia('(max-width: 820px)');

if (menuToggle && primaryNavigation) {
    const updateNavigation = () => {
        if (mobileNavigation.matches) {
            primaryNavigation.hidden = menuToggle.getAttribute('aria-expanded') !== 'true';
        } else {
            primaryNavigation.hidden = false;
            menuToggle.setAttribute('aria-expanded', 'false');
            menuToggle.querySelector('.sr-only').textContent = 'Abrir menu';
        }
    };

    menuToggle.addEventListener('click', () => {
        const expanded = menuToggle.getAttribute('aria-expanded') === 'true';
        menuToggle.setAttribute('aria-expanded', String(!expanded));
        menuToggle.querySelector('.sr-only').textContent = expanded ? 'Abrir menu' : 'Fechar menu';
        updateNavigation();
    });

    primaryNavigation.addEventListener('click', (event) => {
        if (mobileNavigation.matches && event.target.closest('a')) {
            menuToggle.setAttribute('aria-expanded', 'false');
            menuToggle.querySelector('.sr-only').textContent = 'Abrir menu';
            updateNavigation();
        }
    });

    mobileNavigation.addEventListener('change', updateNavigation);
    updateNavigation();
}
