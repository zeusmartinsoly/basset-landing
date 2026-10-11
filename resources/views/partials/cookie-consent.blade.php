@php($cc = $tracking['cookie_consent'])
@if($cc)
    <style>
        #cc-root {
            --cc-bg: {{ $cc['background_color'] }};
            --cc-text: {{ $cc['text_color'] }};
            --cc-primary: {{ $cc['primary_color'] }};
            --cc-primary-text: {{ $cc['primary_text_color'] }};
            --cc-secondary: {{ $cc['secondary_color'] }};
            --cc-radius: {{ $cc['border_radius'] }}px;
            font-family: inherit;
        }
        #cc-root [hidden] { display: none !important; }
        .cc-banner {
            position: fixed; z-index: 2147483000; box-sizing: border-box;
            background: var(--cc-bg); color: var(--cc-text);
            border: 1px solid var(--cc-secondary); border-radius: var(--cc-radius);
            box-shadow: 0 20px 50px rgba(0, 0, 0, .35);
            padding: 20px; line-height: 1.6; font-size: 14px;
            max-height: calc(100vh - 32px); overflow-y: auto;
        }
        .cc-banner--bar { inset-inline: 16px; bottom: 16px; }
        .cc-banner--box_start, .cc-banner--box_end { bottom: 16px; width: min(420px, calc(100vw - 32px)); }
        .cc-banner--box_start { inset-inline-start: 16px; }
        .cc-banner--box_end { inset-inline-end: 16px; }
        .cc-banner--modal { top: 50%; left: 50%; transform: translate(-50%, -50%); width: min(520px, calc(100vw - 32px)); }
        .cc-backdrop { position: fixed; inset: 0; z-index: 2147482999; background: rgba(0, 0, 0, .55); backdrop-filter: blur(2px); }
        .cc-title { margin: 0 0 6px; font-size: 16px; font-weight: 700; }
        .cc-message { margin: 0; opacity: .85; }
        .cc-message a { color: inherit; text-decoration: underline; }
        .cc-actions { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 16px; }
        .cc-banner--bar .cc-layout { display: flex; flex-wrap: wrap; align-items: center; gap: 16px; }
        .cc-banner--bar .cc-text { flex: 1 1 360px; }
        .cc-banner--bar .cc-actions { margin-top: 0; }
        .cc-btn {
            flex: 1 1 auto; cursor: pointer; font: inherit; font-weight: 600; font-size: 14px;
            padding: 10px 16px; border-radius: calc(var(--cc-radius) * .6);
            border: 1px solid var(--cc-secondary); background: transparent; color: var(--cc-text);
        }
        .cc-btn--primary { background: var(--cc-primary); border-color: var(--cc-primary); color: var(--cc-primary-text); }
        .cc-btn:hover { filter: brightness(1.1); }
        .cc-btn:focus-visible, .cc-switch input:focus-visible + span, .cc-reopen:focus-visible { outline: 2px solid var(--cc-primary); outline-offset: 2px; }
        .cc-categories { display: grid; gap: 10px; margin-top: 14px; }
        .cc-category { display: flex; gap: 12px; align-items: flex-start; padding: 12px; border: 1px solid var(--cc-secondary); border-radius: calc(var(--cc-radius) * .6); }
        .cc-category strong { display: block; font-size: 14px; }
        .cc-category p { margin: 2px 0 0; font-size: 13px; opacity: .8; }
        .cc-category > div { flex: 1; }
        .cc-switch { position: relative; flex: none; width: 40px; height: 22px; }
        .cc-switch input { position: absolute; opacity: 0; inset: 0; margin: 0; cursor: pointer; }
        .cc-switch span { position: absolute; inset: 0; border-radius: 999px; background: var(--cc-secondary); transition: background .2s; pointer-events: none; }
        .cc-switch span::after { content: ""; position: absolute; top: 3px; inset-inline-start: 3px; width: 16px; height: 16px; border-radius: 50%; background: #fff; transition: transform .2s; }
        .cc-switch input:checked + span { background: var(--cc-primary); }
        .cc-switch input:checked + span::after { transform: translateX(18px); }
        [dir="rtl"] .cc-switch input:checked + span::after { transform: translateX(-18px); }
        .cc-switch input:disabled { cursor: not-allowed; }
        .cc-switch input:disabled + span { opacity: .6; }
        .cc-reopen {
            position: fixed; bottom: 16px; z-index: 2147482998; width: 44px; height: 44px; border-radius: 50%;
            display: grid; place-items: center; cursor: pointer;
            background: var(--cc-bg); color: var(--cc-text); border: 1px solid var(--cc-secondary);
            box-shadow: 0 8px 24px rgba(0, 0, 0, .3);
        }
        .cc-reopen--start { inset-inline-start: 16px; }
        .cc-reopen--end { inset-inline-end: 16px; }
        @media (prefers-reduced-motion: reduce) { .cc-switch span, .cc-switch span::after { transition: none; } }
    </style>

    <div id="cc-root" dir="{{ $cc['direction'] }}">
        @if($cc['layout'] === 'modal')
            <div class="cc-backdrop" data-cc-backdrop hidden></div>
        @endif

        <div class="cc-banner cc-banner--{{ $cc['layout'] }}" role="dialog" aria-modal="{{ $cc['layout'] === 'modal' ? 'true' : 'false' }}" aria-labelledby="cc-title" aria-describedby="cc-message" data-cc-banner hidden>
            <div class="cc-layout">
                <div class="cc-text">
                    <p class="cc-title" id="cc-title">{{ $cc['title'] }}</p>
                    <p class="cc-message" id="cc-message">
                        {{ $cc['message'] }}
                        @if($cc['privacy_link_url'] !== '' && $cc['privacy_link_label'] !== '')
                            <a href="{{ $cc['privacy_link_url'] }}">{{ $cc['privacy_link_label'] }}</a>
                        @endif
                    </p>
                </div>

                <div class="cc-categories" data-cc-preferences hidden>
                    <div class="cc-category">
                        <div>
                            <strong>{{ $cc['necessary_label'] }}</strong>
                            <p>{{ $cc['necessary_description'] }}</p>
                        </div>
                        <label class="cc-switch"><input type="checkbox" checked disabled aria-label="{{ $cc['necessary_label'] }}"><span></span></label>
                    </div>
                    @foreach(['analytics' => 'a', 'marketing' => 'm', 'functional' => 'f'] as $__ccCategory => $__ccKey)
                        @if($cc[$__ccCategory.'_enabled'])
                            <div class="cc-category">
                                <div>
                                    <strong id="cc-label-{{ $__ccKey }}">{{ $cc[$__ccCategory.'_label'] }}</strong>
                                    <p>{{ $cc[$__ccCategory.'_description'] }}</p>
                                </div>
                                <label class="cc-switch"><input type="checkbox" data-cc-category="{{ $__ccKey }}" aria-labelledby="cc-label-{{ $__ccKey }}"><span></span></label>
                            </div>
                        @endif
                    @endforeach
                </div>

                <div class="cc-actions">
                    <button type="button" class="cc-btn cc-btn--primary" data-cc-action="accept">{{ $cc['accept_label'] }}</button>
                    @if($cc['show_reject_button'])
                        <button type="button" class="cc-btn" data-cc-action="reject">{{ $cc['reject_label'] }}</button>
                    @endif
                    <button type="button" class="cc-btn" data-cc-action="customize">{{ $cc['customize_label'] }}</button>
                    <button type="button" class="cc-btn" data-cc-action="save" hidden>{{ $cc['save_label'] }}</button>
                </div>
            </div>
        </div>

        @if($cc['show_reopen_button'])
            <button type="button" class="cc-reopen cc-reopen--{{ $cc['reopen_position'] }}" data-cc-reopen aria-label="{{ $cc['reopen_label'] }}" title="{{ $cc['reopen_label'] }}" hidden>
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 2a10 10 0 1 0 10 10 4 4 0 0 1-5-5 4 4 0 0 1-5-5"/><path d="M8.5 8.5v.01"/><path d="M16 15.5v.01"/><path d="M12 12v.01"/><path d="M11 17v.01"/><path d="M7 14v.01"/></svg>
            </button>
        @endif
    </div>

    <script>
        (function (w, d) {
            var config = {
                version: @json($cc['version']),
                expiryDays: @json($cc['expiry_days']),
                dataLayer: @json($tracking['data_layer_name']),
                shown: { a: @json($cc['analytics_enabled']), m: @json($cc['marketing_enabled']), f: @json($cc['functional_enabled']) }
            };
            var root = d.getElementById('cc-root');
            var banner = root.querySelector('[data-cc-banner]');
            var backdrop = root.querySelector('[data-cc-backdrop]');
            var preferences = root.querySelector('[data-cc-preferences]');
            var reopen = root.querySelector('[data-cc-reopen]');
            var button = function (action) { return root.querySelector('[data-cc-action="' + action + '"]'); };
            var lastFocus = null;

            function store(choice) {
                var secure = location.protocol === 'https:' ? '; Secure' : '';
                d.cookie = 'cookie_consent=' + encodeURIComponent(JSON.stringify(choice)) + '; Max-Age=' + (config.expiryDays * 86400) + '; Path=/; SameSite=Lax' + secure;
            }

            function apply(choice) {
                var layer = w[config.dataLayer] = w[config.dataLayer] || [];
                (function () { layer.push(arguments); })('consent', 'update', w.__consentUpdate(choice));
                layer.push({ event: 'cookie_consent_update', cookie_consent_analytics: choice.a, cookie_consent_marketing: choice.m, cookie_consent_functional: choice.f });
                if (typeof w.fbq === 'function') { w.fbq('consent', choice.m ? 'grant' : 'revoke'); }
                if (w.ttq) { choice.m ? w.ttq.grantConsent() : w.ttq.revokeConsent(); }
                if (typeof w.clarity === 'function') { w.clarity('consentv2', { ad_Storage: choice.m ? 'granted' : 'denied', analytics_Storage: choice.a ? 'granted' : 'denied' }); }
            }

            function decide(choice) {
                choice.v = config.version;
                choice.t = Date.now();
                store(choice);
                w.__cookieConsent = choice;
                apply(choice);
                close();
            }

            function all(value) { return { a: value, m: value, f: value }; }

            function fromToggles() {
                var current = w.__cookieConsent || all(false);
                var choice = {};
                ['a', 'm', 'f'].forEach(function (key) {
                    var input = root.querySelector('[data-cc-category="' + key + '"]');
                    choice[key] = input ? input.checked : (config.shown[key] ? false : !!current[key]);
                });
                return choice;
            }

            function showPreferences(show) {
                preferences.hidden = !show;
                button('customize').hidden = show;
                button('save').hidden = !show;
                if (show) {
                    var current = w.__cookieConsent || all(false);
                    root.querySelectorAll('[data-cc-category]').forEach(function (input) { input.checked = !!current[input.getAttribute('data-cc-category')]; });
                }
            }

            function open(withPreferences) {
                lastFocus = d.activeElement;
                showPreferences(!!withPreferences);
                banner.hidden = false;
                if (backdrop) { backdrop.hidden = false; }
                if (reopen) { reopen.hidden = true; }
                button('accept').focus({ preventScroll: true });
            }

            function close() {
                banner.hidden = true;
                if (backdrop) { backdrop.hidden = true; }
                if (reopen) { reopen.hidden = false; }
                if (lastFocus && lastFocus.focus && lastFocus !== d.body) { lastFocus.focus({ preventScroll: true }); }
            }

            root.addEventListener('click', function (event) {
                var target = event.target.closest('[data-cc-action]');
                if (!target) { return; }
                var action = target.getAttribute('data-cc-action');
                if (action === 'accept') { decide(all(true)); }
                if (action === 'reject') { decide(all(false)); }
                if (action === 'customize') { showPreferences(true); }
                if (action === 'save') { decide(fromToggles()); }
            });

            banner.addEventListener('keydown', function (event) {
                if (event.key === 'Escape' && w.__cookieConsent) { close(); }
                if (event.key !== 'Tab' || backdrop === null) { return; }
                var focusable = Array.prototype.filter.call(banner.querySelectorAll('button, input:not([disabled]), a[href]'), function (el) { return el.offsetParent !== null; });
                var first = focusable[0], last = focusable[focusable.length - 1];
                if (event.shiftKey && d.activeElement === first) { last.focus(); event.preventDefault(); }
                else if (!event.shiftKey && d.activeElement === last) { first.focus(); event.preventDefault(); }
            });

            if (reopen) { reopen.addEventListener('click', function () { open(true); }); }

            w.CookieConsent = {
                open: function () { open(true); },
                get: function () { return w.__cookieConsent || null; }
            };

            if (w.__cookieConsent) {
                if (reopen) { reopen.hidden = false; }
            } else {
                open(false);
            }
        })(window, document);
    </script>
@endif
