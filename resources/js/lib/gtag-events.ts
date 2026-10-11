declare global {
    interface Window {
        gtag?: (...args: unknown[]) => void;
        fbq?: (...args: unknown[]) => void;
        ttq?: {
            track: (
                event: string,
                parameters?: Record<string, unknown>,
            ) => void;
        };
        __leadTracking?: LeadTrackingConfig;
    }
}

/**
 * Sanitized server-side config rendered into the page head (see partials/tracking-head.blade.php).
 */
interface LeadTrackingConfig {
    data_layer_name: string;
    gtm_event: string | null;
    meta_event: string | null;
    tiktok_event: string | null;
}

const FORM_ID = 'contact_waitlist';

/**
 * Fired after a successful contact / waitlist submission.
 * GA4 receives its recommended `generate_lead` event via gtag; GTM, Meta Pixel and TikTok Pixel receive the events configured in the admin.
 * In GA4: Reports → Engagement → Events, or Admin → Events / Key events → mark as conversion (e.g. `generate_lead`, filter custom param `form_id`).
 */
export function trackContactWaitlistGenerateLead(): void {
    if (typeof window === 'undefined') {
        return;
    }

    if (typeof window.gtag === 'function') {
        window.gtag('event', 'generate_lead', { form_id: FORM_ID });
    }

    const config = window.__leadTracking;

    if (!config) {
        return;
    }

    const dataLayer = (window as unknown as Record<string, unknown>)[
        config.data_layer_name
    ];

    if (config.gtm_event && Array.isArray(dataLayer)) {
        dataLayer.push({ event: config.gtm_event, form_id: FORM_ID });
    }

    if (config.meta_event && typeof window.fbq === 'function') {
        window.fbq('track', config.meta_event, { content_name: FORM_ID });
    }

    if (config.tiktok_event && window.ttq) {
        window.ttq.track(config.tiktok_event, { content_name: FORM_ID });
    }
}
