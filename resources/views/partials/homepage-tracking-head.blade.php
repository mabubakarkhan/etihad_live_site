@php
    $siteSeo = $siteSeo ?? null;
@endphp
@if($siteSeo && !empty($siteSeo->google_site_verification))
    <meta name="google-site-verification" content="{{ e($siteSeo->google_site_verification) }}" />
@endif
@if($siteSeo && !empty($siteSeo->bing_site_verification))
    <meta name="msvalidate.01" content="{{ e($siteSeo->bing_site_verification) }}" />
@endif
@if($siteSeo && !empty($siteSeo->facebook_domain_verification))
    <meta name="facebook-domain-verification" content="{{ e($siteSeo->facebook_domain_verification) }}" />
@endif
@if($siteSeo && !empty($siteSeo->google_tag_manager_id))
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','{{ e($siteSeo->google_tag_manager_id) }}');</script>
@endif
@if($siteSeo && !empty($siteSeo->google_analytics_id))
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ e($siteSeo->google_analytics_id) }}"></script>
    <script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','{{ e($siteSeo->google_analytics_id) }}');@if(!empty($siteSeo->google_ads_id))gtag('config','{{ e($siteSeo->google_ads_id) }}');@endif</script>
@endif
@if($siteSeo && !empty($siteSeo->facebook_pixel_id))
    <script>!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');fbq('init','{{ e($siteSeo->facebook_pixel_id) }}');fbq('track','PageView');</script>
@endif
@if($siteSeo && !empty($siteSeo->tiktok_pixel_id))
    <script>!function(w,d,t){w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=["page","track","identify","instances","debug","on","off","once","ready","alias","group","enableCookie","disableCookie"];ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);ttq.instance=function(t){for(var e=ttq._i[t]||[],n=0;n<ttq.methods.length;n++)ttq.setAndDefer(e,ttq.methods[n]);return e};ttq.load=function(e,n){var i="https://analytics.tiktok.com/i18n/pixel/events.js";ttq._i=ttq._i||{};ttq._i[e]=[];ttq._i[e]._u=i;ttq._t=ttq._t||{};ttq._t[e]=+new Date;ttq._o=ttq._o||{};ttq._o[e]=n||{};var o=document.createElement("script");o.type="text/javascript";o.async=!0;o.src=i+"?sdkid="+e+"&lib="+t;var a=document.getElementsByTagName("script")[0];a.parentNode.insertBefore(o,a)};ttq.load('{{ e($siteSeo->tiktok_pixel_id) }}');ttq.page();}(window,document,'ttq');</script>
@endif
@if($siteSeo && !empty($siteSeo->linkedin_partner_id))
    <script>window._linkedin_partner_id="{{ e($siteSeo->linkedin_partner_id) }}";window._linkedin_data_partner_ids=window._linkedin_data_partner_ids||[];window._linkedin_data_partner_ids.push(window._linkedin_partner_id);</script>
    <script>(function(l){if(!l){window.lintrk=function(a,b){window.lintrk.q.push([a,b])};window.lintrk.q=[]}var s=document.getElementsByTagName("script")[0];var b=document.createElement("script");b.type="text/javascript";b.async=true;b.src="https://snap.licdn.com/li.lms-analytics/insight.min.js";s.parentNode.insertBefore(b,s);})(window.lintrk);</script>
@endif
@if($siteSeo && !empty($siteSeo->hotjar_id))
    <script>(function(h,o,t,j,a,r){h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};h._hjSettings={hjid:{{ (int) $siteSeo->hotjar_id }},hjsv:6};a=o.getElementsByTagName('head')[0];r=o.createElement('script');r.async=1;r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;a.appendChild(r);})(window,document,'https://static.hotjar.com/c/hotjar-','.js?sv=');</script>
@endif
@if($siteSeo && !empty($siteSeo->custom_head_code))
{!! $siteSeo->custom_head_code !!}
@endif
