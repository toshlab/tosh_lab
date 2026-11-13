/**
 * ライト／ダークの切り替えボタン。
 *
 * 最初のテーマは <head> のインラインスクリプトが決めている（ちらつき防止）。
 * ここではボタンの状態を合わせ、押されたら切り替えて localStorage に保存する。
 * 保存が無い（自分で選んでいない）あいだは、OSの設定が変わったら追従する。
 */
const STORAGE_KEY = 'theme';
const root = document.documentElement;
const media = window.matchMedia('(prefers-color-scheme: dark)');

function readStored() {
    try {
        const value = localStorage.getItem(STORAGE_KEY);
        return value === 'light' || value === 'dark' ? value : null;
    } catch {
        return null;
    }
}

function store(theme) {
    try {
        localStorage.setItem(STORAGE_KEY, theme);
    } catch {
        // プライベートブラウズなどで保存できなくても、切り替え自体はできる
    }
}

function syncButtons(theme) {
    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        button.setAttribute('aria-pressed', String(theme === 'dark'));
    });
}

function apply(theme) {
    root.setAttribute('data-theme', theme);
    syncButtons(theme);
}

export function initTheme() {
    syncButtons(root.getAttribute('data-theme') === 'dark' ? 'dark' : 'light');

    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const next = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            apply(next);
            store(next);
        });
    });

    media.addEventListener('change', (event) => {
        if (readStored() === null) {
            apply(event.matches ? 'dark' : 'light');
        }
    });
}
