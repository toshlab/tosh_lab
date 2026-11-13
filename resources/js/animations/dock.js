/**
 * 下のメニューバー（docs/design/design-system.md §2-2）
 *
 * - 読み込み後、下からふわっと出す
 * - トップでは、今見ているセクションのメニューを光らせる。光（.dock-indicator）はメニューの間をすべって移動する
 * - 他のページでは、Blade が付けた aria-current="page" の光をそのまま使う
 */
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

// メニュー → 対応するセクション（Profile は3つのどれにいても光らせる）
const SECTIONS = {
    works: ['#works'],
    services: ['#services'],
    about: ['#about', '#experience', '#skills'],
    blog: ['#blog'],
    contact: ['#contact'],
};

export function initDock({ animate, introDelay = 0 }) {
    const dock = document.querySelector('[data-dock]');
    if (!dock) {
        return;
    }

    if (animate) {
        gsap.fromTo(dock, { opacity: 0, y: 16 }, { opacity: 1, y: 0, duration: 0.8, delay: introDelay, ease: 'power3.out' });
    }

    const indicator = dock.querySelector('[data-dock-indicator]');
    const links = new Map(
        [...dock.querySelectorAll('[data-dock-link]')].map((link) => [link.dataset.dockLink, link]),
    );

    // トップ以外（セクションが無い）ページでは、Blade の aria-current="page" に任せる
    const triggers = [];
    Object.entries(SECTIONS).forEach(([key, selectors]) => {
        selectors.forEach((selector) => {
            const section = document.querySelector(selector);
            if (section && links.has(key)) {
                triggers.push({ key, section });
            }
        });
    });
    if (!indicator || triggers.length === 0) {
        return;
    }

    let currentKey = null;
    const duration = animate ? 0.45 : 0;

    function moveTo(key) {
        if (key === currentKey) {
            return;
        }
        currentKey = key;

        links.forEach((link, linkKey) => {
            if (linkKey === key) {
                link.setAttribute('aria-current', 'location');
            } else if (link.getAttribute('aria-current') === 'location') {
                link.removeAttribute('aria-current');
            }
        });

        if (key === null) {
            gsap.to(indicator, { opacity: 0, duration, ease: 'power3.out' });
            return;
        }

        const link = links.get(key);
        gsap.to(indicator, {
            x: link.offsetLeft,
            width: link.offsetWidth,
            opacity: 1,
            duration: indicator.style.opacity === '' || indicator.style.opacity === '0' ? 0 : duration,
            ease: 'power3.out',
        });
    }

    // 画面の中央を通過しているセクション。重なったときは後ろ（下）のものを優先する
    const active = new Set();
    function update() {
        const last = triggers.filter((t) => active.has(t)).at(-1);
        moveTo(last ? last.key : null);
    }

    triggers.forEach((t) => {
        ScrollTrigger.create({
            trigger: t.section,
            start: 'top center',
            end: 'bottom center',
            onToggle: (self) => {
                if (self.isActive) {
                    active.add(t);
                } else {
                    active.delete(t);
                }
                update();
            },
        });
    });

    // 画面幅が変わるとメニューの位置も変わるので、光の位置を合わせ直す
    ScrollTrigger.addEventListener('refresh', () => {
        if (currentKey) {
            const link = links.get(currentKey);
            gsap.set(indicator, { x: link.offsetLeft, width: link.offsetWidth });
        }
    });
}
