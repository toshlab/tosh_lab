/**
 * ファーストビュー：ロゴ → キャッチコピー・情報 の順に、下からふわっと出す。
 * 戻り値はアニメーションの長さ（秒）。ファーストビューが無いページでは 0。
 */
import { gsap } from 'gsap';

export function initHero() {
    const lines = gsap.utils.toArray('[data-hero-line]');
    const items = gsap.utils.toArray('[data-hero-item]');

    if (lines.length === 0 && items.length === 0) {
        return 0;
    }

    const tl = gsap.timeline({ defaults: { ease: 'power3.out' } });

    tl.fromTo(lines, { opacity: 0, y: 40 }, { opacity: 1, y: 0, duration: 1.0, stagger: 0.08 })
        .fromTo(items, { opacity: 0, y: 24 }, { opacity: 1, y: 0, duration: 0.8, stagger: 0.08 }, '-=0.6');

    return tl.duration();
}
