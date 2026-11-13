/**
 * 見出し・一覧の行を、画面に入ったときに下からふわっと出す（1回だけ）。
 *
 * - [data-reveal]      … 1つずつ出す（見出し・導入文など）
 * - [data-reveal-item] … 同じ画面に入ったものをまとめて、0.06秒ずつずらして出す（一覧の行）
 */
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

const FROM = { opacity: 0, y: 24 };
const TO = {
    opacity: 1,
    y: 0,
    duration: 0.8,
    ease: 'power3.out',
    // 終わったら transform を消して、行のホバー（CSS）とぶつからないようにする
    clearProps: 'transform',
};

export function initReveal() {
    gsap.utils.toArray('[data-reveal]').forEach((el) => {
        gsap.fromTo(el, FROM, {
            ...TO,
            scrollTrigger: { trigger: el, start: 'top 85%', once: true },
        });
    });

    const items = gsap.utils.toArray('[data-reveal-item]');
    if (items.length === 0) {
        return;
    }

    gsap.set(items, FROM);
    ScrollTrigger.batch(items, {
        start: 'top 88%',
        once: true,
        onEnter: (batch) => gsap.to(batch, { ...TO, stagger: 0.06, overwrite: true }),
    });
}
