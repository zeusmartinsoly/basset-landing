declare global {
    interface Window {
        gtag?: (...args: unknown[]) => void;
    }
}

/**
 * GA4 recommended event fired after a successful contact / waitlist submission.
 * In GA4: Reports → Engagement → Events, or Admin → Events / Key events → mark as conversion (e.g. `generate_lead`, filter custom param `form_id`).
 */
export function trackContactWaitlistGenerateLead(): void {
    const gtag = typeof window !== 'undefined' ? window.gtag : undefined;

    if (typeof gtag !== 'function') {
        return;
    }

    gtag('event', 'generate_lead', {
        form_id: 'contact_waitlist',
    });
}
