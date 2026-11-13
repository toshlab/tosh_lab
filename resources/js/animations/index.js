/**
 * アニメーションの入口（docs/design/design-system.md §3）。
 *
 * - 動きを減らす設定（prefers-reduced-motion: reduce）のときは、ふわっと出す動きを登録しない
 * - <head> のスクリプトが付けた html.js-motion が「最初に隠す」合図。動きを登録しないときは外して全部表示する
 * - 下のメニューバーの「今いる場所」は、動きを減らす設定でも動かす（光る場所が瞬時に切り替わるだけ）
 */
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { initHero } from './hero';
import { initReveal } from './reveal';
import { initDock } from './dock';

export function initAnimations() {
    const root = document.documentElement;

    gsap.registerPlugin(ScrollTrigger);

    const mm = gsap.matchMedia();

    mm.add(
        {
            motion: '(prefers-reduced-motion: no-preference)',
            reduce: '(prefers-reduced-motion: reduce)',
        },
        (context) => {
            const { motion } = context.conditions;

            if (motion && root.classList.contains('js-motion')) {
                const heroDuration = initHero();
                initReveal();
                initDock({ animate: true, introDelay: heroDuration > 0 ? 0.6 : 0.2 });
            } else {
                root.classList.remove('js-motion');
                initDock({ animate: false });
            }
        },
    );

    // 安全装置（<head> の3秒タイマー）に「動き始めた」ことを伝える
    window.__toshMotionReady = true;
}
