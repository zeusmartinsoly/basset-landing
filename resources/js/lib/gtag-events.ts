declare global {
    interface Window {
        gtag?: (...args: unknown[]) => void;
        [dataLayerName: string]: unknown;
    }
}

export interface GoogleTagManagerLeadEvent {
    dataLayerName: string;
    eventName: string;
}

/**
 * Fired after a successful contact / waitlist submission.
 * GA4 receives its recommended `generate_lead` event via gtag; GTM receives the configured dataLayer event.
 * In GA4: Reports → Engagement → Events, or Admin → Events / Key events → mark as conversion (e.g. `generate_lead`, filter custom param `form_id`).
 */
export function trackContactWaitlistGenerateLead(
    gtm?: GoogleTagManagerLeadEvent | null,
): void {
    if (typeof window === 'undefined') {
        return;
    }

    if (typeof window.gtag === 'function') {
        window.gtag('event', 'generate_lead', {
            form_id: 'contact_waitlist',
        });
    }

    const dataLayer = gtm ? window[gtm.dataLayerName] : undefined;

    if (gtm && Array.isArray(dataLayer)) {
        dataLayer.push({ event: gtm.eventName, form_id: 'contact_waitlist' });
    }
}
