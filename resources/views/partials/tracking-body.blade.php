@if($tracking['gtm'])
    <!-- Google Tag Manager (noscript) -->
    @foreach($tracking['gtm']['containers'] as $__gtmContainer)
        <noscript><iframe src="{{ $tracking['gtm']['base_url'] }}/ns.html?id={{ $__gtmContainer['id'] }}{{ $__gtmContainer['query'] }}"
        height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    @endforeach
    <!-- End Google Tag Manager (noscript) -->
@endif

{{-- The Meta noscript pixel cannot check consent, so it is skipped while the consent banner is active. --}}
@if($tracking['meta_pixel'] && ! $tracking['cookie_consent'])
    @foreach($tracking['meta_pixel']['pixel_ids'] as $__metaPixelId)
        <noscript><img height="1" width="1" style="display:none" alt=""
        src="https://www.facebook.com/tr?id={{ $__metaPixelId }}&ev=PageView&noscript=1"/></noscript>
    @endforeach
@endif
