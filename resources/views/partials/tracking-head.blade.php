{{-- Consent defaults, stored visitor choice, data layer variables and lead tracking config. Must run before any tag loads. --}}
<script>
    (function(w,d,l){
        w[l]=w[l]||[];
        function g(){w[l].push(arguments);}
        w.__consentUpdate=function(c){
            return {
                analytics_storage:c.a?'granted':'denied',
                ad_storage:c.m?'granted':'denied',
                ad_user_data:c.m?'granted':'denied',
                ad_personalization:c.m?'granted':'denied',
                functionality_storage:c.f?'granted':'denied',
                personalization_storage:c.f?'granted':'denied'
            };
        };
        @if($tracking['consent_defaults'])
            g('consent','default',@json($tracking['consent_defaults']));
        @endif
        @if($tracking['gtm'] && $tracking['gtm']['ads_data_redaction'])
            g('set','ads_data_redaction',true);
        @endif
        @if($tracking['gtm'] && $tracking['gtm']['url_passthrough'])
            g('set','url_passthrough',true);
        @endif
        @if($tracking['cookie_consent'])
            var c=null;
            try{var m=d.cookie.match(/(?:^|; )cookie_consent=([^;]*)/);if(m){c=JSON.parse(decodeURIComponent(m[1]));}}catch(e){c=null;}
            if(!c||c.v!==@json($tracking['cookie_consent']['version'])){c=null;}
            w.__cookieConsent=c;
            if(c){g('consent','update',w.__consentUpdate(c));}
        @endif
        @if($tracking['gtm'] && $tracking['gtm']['data_layer_variables'] !== [])
            w[l].push(@json($tracking['gtm']['data_layer_variables']));
        @endif
        w.__leadTracking=@json($tracking['lead_tracking']);
    })(window,document,@json($tracking['data_layer_name']));
</script>

@if($tracking['gtm'])
    <!-- Google Tag Manager -->
    @foreach($tracking['gtm']['containers'] as $__gtmContainer)
        <script>(function(w,d,s,l,i,b,q){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        b+'/gtm.js?id='+i+dl+q;f.parentNode.insertBefore(j,f);
        })(window,document,'script',@json($tracking['data_layer_name']),@json($__gtmContainer['id']),@json($tracking['gtm']['base_url']),@json($__gtmContainer['query']));</script>
    @endforeach
    <!-- End Google Tag Manager -->
@endif

@if($tracking['ga4_measurement_id'])
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $tracking['ga4_measurement_id'] }}{{ $tracking['data_layer_name'] !== 'dataLayer' ? '&l='.$tracking['data_layer_name'] : '' }}"></script>
    <script>
        window[@json($tracking['data_layer_name'])] = window[@json($tracking['data_layer_name'])] || [];

        function gtag() {
            window[@json($tracking['data_layer_name'])].push(arguments);
        }

        gtag('js', new Date());
        gtag('config', '{{ $tracking['ga4_measurement_id'] }}');
    </script>
@endif

@if($tracking['clarity_project_id'])
    <script type="text/javascript">
        (function(c,l,a,r,i,t,y){
            c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
            t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
            y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
        })(window, document, "clarity", "script", @json($tracking['clarity_project_id']));
        @if($tracking['cookie_consent'])
            window.clarity('consentv2', {
                ad_Storage: window.__cookieConsent && window.__cookieConsent.m ? 'granted' : 'denied',
                analytics_Storage: window.__cookieConsent && window.__cookieConsent.a ? 'granted' : 'denied'
            });
        @endif
    </script>
@endif

@if($tracking['meta_pixel'])
    <!-- Meta Pixel Code -->
    <script>
        !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        @if($tracking['cookie_consent'])
            if (!(window.__cookieConsent && window.__cookieConsent.m)) { fbq('consent', 'revoke'); }
        @endif
        @foreach($tracking['meta_pixel']['pixel_ids'] as $__metaPixelId)
            fbq('init', @json($__metaPixelId));
        @endforeach
        fbq('track', 'PageView');
    </script>
    <!-- End Meta Pixel Code -->
@endif

@if($tracking['tiktok_pixel'])
    <!-- TikTok Pixel Code -->
    <script>
        !function (w, d, t) {
          w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=["page","track","identify","instances","debug","on","off","once","ready","alias","group","enableCookie","disableCookie","holdConsent","revokeConsent","grantConsent"],ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);ttq.instance=function(t){for(var e=ttq._i[t]||[],n=0;n<ttq.methods.length;n++)ttq.setAndDefer(e,ttq.methods[n]);return e},ttq.load=function(e,n){var i="https://analytics.tiktok.com/i18n/pixel/events.js";ttq._i=ttq._i||{},ttq._i[e]=[],ttq._i[e]._u=i,ttq._t=ttq._t||{},ttq._t[e]=+new Date,ttq._o=ttq._o||{},ttq._o[e]=n||{};var o=document.createElement("script");o.type="text/javascript",o.async=!0,o.src=i+"?sdkid="+e+"&lib="+t;var a=document.getElementsByTagName("script")[0];a.parentNode.insertBefore(o,a)};

          @if($tracking['cookie_consent'])
            if (!(w.__cookieConsent && w.__cookieConsent.m)) { ttq.holdConsent(); }
          @endif
          @foreach($tracking['tiktok_pixel']['pixel_ids'] as $__tiktokPixelId)
            ttq.load(@json($__tiktokPixelId));
          @endforeach
          ttq.page();
        }(window, document, 'ttq');
    </script>
    <!-- End TikTok Pixel Code -->
@endif
