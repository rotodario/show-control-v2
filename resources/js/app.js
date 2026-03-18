import './bootstrap';

import Alpine from 'alpinejs';

const preferredTheme = () => {
    const storedTheme = window.localStorage.getItem('theme');

    if (storedTheme === 'dark' || storedTheme === 'light') {
        return storedTheme;
    }

    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
};

const applyTheme = (theme) => {
    const isDark = theme === 'dark';

    document.documentElement.classList.toggle('dark', isDark);
    document.documentElement.dataset.theme = theme;
    window.localStorage.setItem('theme', theme);

    return theme;
};

window.showControlTheme = {
    get() {
        return document.documentElement.classList.contains('dark') ? 'dark' : 'light';
    },
    set(theme) {
        return applyTheme(theme);
    },
    toggle() {
        return applyTheme(this.get() === 'dark' ? 'light' : 'dark');
    },
};

window.Alpine = Alpine;

document.addEventListener('alpine:init', () => {
    Alpine.store('theme', {
        current: window.showControlTheme.get(),
        toggle() {
            this.current = window.showControlTheme.toggle();
        },
    });
});

applyTheme(preferredTheme());

Alpine.start();
