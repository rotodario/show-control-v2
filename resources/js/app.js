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

window.showControlEmojiPicker = (textareaId) => ({
    open: false,
    emojis: [
        '👍', '👌', '🙏', '👏', '🙌', '💪', '✅', '❌',
        '⚠️', '🚨', '🔥', '⭐', '🎯', '🕒', '📍', '📝',
        '📞', '📧', '📎', '🎟️', '🎛️', '🎚️', '🎤', '🔊',
        '🔇', '💡', '🔌', '🚚', '🚪', '🏟️', '🎪', '🚧',
    ],
    insert(emoji) {
        const textarea = document.getElementById(textareaId);

        if (!textarea) {
            return;
        }

        const start = textarea.selectionStart ?? textarea.value.length;
        const end = textarea.selectionEnd ?? textarea.value.length;
        const current = textarea.value;
        const spacerBefore = start > 0 && !/\s$/.test(current.slice(0, start)) ? ' ' : '';
        const spacerAfter = end < current.length && !/^\s/.test(current.slice(end)) ? ' ' : '';
        const nextValue = `${current.slice(0, start)}${spacerBefore}${emoji}${spacerAfter}${current.slice(end)}`;

        textarea.value = nextValue;
        textarea.dispatchEvent(new Event('input', { bubbles: true }));

        const cursor = start + spacerBefore.length + emoji.length + spacerAfter.length;

        textarea.focus();
        textarea.setSelectionRange(cursor, cursor);
        this.open = false;
    },
});

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
