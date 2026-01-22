import { gsap, ScrollTrigger } from './gsap-setup';

// ========================================
// REVEAL ANIMATIONS
// ========================================

/**
 * Fade up and reveal animation
 */
export function createFadeUpReveal(
    element: gsap.TweenTarget,
    options: {
        trigger?: string | Element;
        start?: string;
        delay?: number;
        duration?: number;
        y?: number;
    } = {}
) {
    const {
        trigger,
        start = 'top 85%',
        delay = 0,
        duration = 1,
        y = 60,
    } = options;

    return gsap.fromTo(
        element,
        {
            y,
            opacity: 0,
        },
        {
            y: 0,
            opacity: 1,
            duration,
            delay,
            ease: 'power3.out',
            scrollTrigger: trigger
                ? {
                      trigger,
                      start,
                      toggleActions: 'play none none reverse',
                  }
                : undefined,
        }
    );
}

/**
 * Staggered reveal for multiple elements
 */
export function createStaggerReveal(
    elements: gsap.TweenTarget,
    options: {
        trigger?: string | Element;
        start?: string;
        stagger?: number;
        y?: number;
        duration?: number;
    } = {}
) {
    const {
        trigger,
        start = 'top 85%',
        stagger = 0.1,
        y = 40,
        duration = 0.8,
    } = options;

    return gsap.fromTo(
        elements,
        {
            y,
            opacity: 0,
        },
        {
            y: 0,
            opacity: 1,
            duration,
            stagger,
            ease: 'power3.out',
            scrollTrigger: trigger
                ? {
                      trigger,
                      start,
                      toggleActions: 'play none none reverse',
                  }
                : undefined,
        }
    );
}

// ========================================
// TEXT ANIMATIONS
// ========================================

/**
 * Split text reveal animation (character by character)
 */
export function createTextReveal(
    element: gsap.TweenTarget,
    options: {
        trigger?: string | Element;
        start?: string;
        stagger?: number;
        duration?: number;
    } = {}
) {
    const {
        trigger,
        start = 'top 85%',
        stagger = 0.02,
        duration = 0.6,
    } = options;

    return gsap.fromTo(
        element,
        {
            y: '100%',
            opacity: 0,
        },
        {
            y: '0%',
            opacity: 1,
            duration,
            stagger,
            ease: 'power3.out',
            scrollTrigger: trigger
                ? {
                      trigger,
                      start,
                      toggleActions: 'play none none reverse',
                  }
                : undefined,
        }
    );
}

// ========================================
// PARALLAX ANIMATIONS
// ========================================

/**
 * Create parallax effect on scroll
 */
export function createParallax(
    element: gsap.TweenTarget,
    options: {
        trigger?: string | Element;
        start?: string;
        end?: string;
        yPercent?: number;
        scrub?: boolean | number;
    } = {}
) {
    const {
        trigger,
        start = 'top bottom',
        end = 'bottom top',
        yPercent = -20,
        scrub = true,
    } = options;

    return gsap.to(element, {
        yPercent,
        ease: 'none',
        scrollTrigger: {
            trigger: trigger || (element as gsap.DOMTarget),
            start,
            end,
            scrub,
        },
    });
}

// ========================================
// INTRO ANIMATIONS
// ========================================

/**
 * Hero intro animation timeline
 */
export function createHeroIntro(container: Element) {
    const tl = gsap.timeline({
        defaults: { ease: 'power3.out' },
    });

    // Initial state
    gsap.set(container, { visibility: 'visible' });

    return tl;
}

/**
 * Floating animation (continuous)
 */
export function createFloatingAnimation(
    element: gsap.TweenTarget,
    options: {
        y?: number;
        duration?: number;
        delay?: number;
    } = {}
) {
    const { y = 20, duration = 3, delay = 0 } = options;

    return gsap.to(element, {
        y: `-=${y}`,
        duration,
        delay,
        ease: 'sine.inOut',
        yoyo: true,
        repeat: -1,
    });
}

/**
 * Subtle rotation animation (continuous)
 */
export function createSubtleRotation(
    element: gsap.TweenTarget,
    options: {
        rotation?: number;
        duration?: number;
    } = {}
) {
    const { rotation = 5, duration = 4 } = options;

    return gsap.to(element, {
        rotation,
        duration,
        ease: 'sine.inOut',
        yoyo: true,
        repeat: -1,
    });
}

// ========================================
// UTILITY FUNCTIONS
// ========================================

/**
 * Kill all ScrollTriggers in a context
 */
export function killScrollTriggers(triggers: ScrollTrigger[]) {
    triggers.forEach((trigger) => trigger.kill());
}

/**
 * Refresh all ScrollTriggers
 */
export function refreshScrollTriggers() {
    ScrollTrigger.refresh();
}

export { gsap, ScrollTrigger };
