@php
$tr = $locale === 'tr';
$t = fn($a,$b) => $tr ? $a : $b;
$labels = ['home'=>$t('Ana sayfa','Home'),'about'=>$t('Hakkımızda','About us'),'treatment'=>$t('Tedavilerimiz','Treatments'),'doctor'=>$t('Doktorlarımız','Our dentists'),'post'=>'Blog','faq'=>$t('Sık sorulan sorular','FAQs'),'contact'=>$t('İletişim','Contact')];
$wa = $settings->whatsappUrl();
$logoUrl = $settings->logo ? asset('storage/'.$settings->logo) : asset('images/cevik-logo.jpeg');
@endphp
<!doctype html>
<html lang="{{ $locale }}">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $seo['title'] }}</title>
<meta name="description" content="{{ $seo['description'] }}"><meta name="theme-color" content="#173d35">
<meta name="robots" content="{{ \App\Support\SiteSeo::indexable() ? 'index, follow, max-image-preview:large' : 'noindex, nofollow' }}">
@if(config('seo.google_verification'))<meta name="google-site-verification" content="{{ config('seo.google_verification') }}">@endif
<link rel="canonical" href="{{ $seo['canonical'] }}">
@foreach($seo['alternates'] as $lang => $href)<link rel="alternate" hreflang="{{ $lang }}" href="{{ $href }}">@endforeach
<link rel="alternate" hreflang="x-default" href="{{ $seo['alternates']['tr'] }}">
<meta property="og:title" content="{{ $seo['title'] }}"><meta property="og:description" content="{{ $seo['description'] }}"><meta property="og:type" content="{{ $kind === 'post' && $slug ? 'article' : 'website' }}"><meta property="og:url" content="{{ $seo['canonical'] }}">
<meta property="og:site_name" content="{{ $t('Diş Hekimi', 'Dentist') }} {{ $settings->name }}"><meta property="og:locale" content="{{ $tr ? 'tr_TR' : 'en_GB' }}"><meta property="og:image" content="{{ $seo['image'] }}">
<meta name="twitter:card" content="summary_large_image"><meta name="twitter:title" content="{{ $seo['title'] }}"><meta name="twitter:description" content="{{ $seo['description'] }}"><meta name="twitter:image" content="{{ $seo['image'] }}">
<script type="application/ld+json">{!! json_encode($seo['graph'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) !!}</script>
<link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;450;500;550;600;650;700&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/site.css') }}?v={{ filemtime(public_path('css/site.css')) }}">
<link rel="stylesheet" href="{{ asset('css/refinements.css') }}?v={{ filemtime(public_path('css/refinements.css')) }}">
<link rel="stylesheet" href="{{ asset('css/responsive.css') }}?v={{ filemtime(public_path('css/responsive.css')) }}">
<script src="{{ asset('js/site.js') }}?v={{ filemtime(public_path('js/site.js')) }}" defer></script>
<link rel="stylesheet" href="{{ asset('css/clinic-photos.css') }}">
</head>
<body>
<a class="skip-link" href="#main">{{ $t('İçeriğe geç','Skip to content') }}</a>
<div class="topline"><div class="wrap"><span>{{ $copy('top_text') }}</span><a href="{{ $url('contact') }}">{{ $copy('top_link') }} <span aria-hidden="true">↗</span></a></div></div>
<header class="header"><div class="wrap nav-wrap">
<a class="brand" href="{{ $url('home') }}" aria-label="{{ $settings->name }} {{ $t('ana sayfa','home') }}">
<x-site-image class="custom-logo" :src="$logoUrl" :alt="$settings->name" sizes="100px" loading="eager" />
<span class="header-brand-name">Erdem Çevik<small>{{ $t('Diş Hekimi', 'Dentist') }}</small></span>
</a>
<nav class="desktop-nav" aria-label="{{ $t('Ana menü','Main navigation') }}">@foreach(['home','about','treatment','doctor','post','contact'] as $key)<a href="{{ $url($key) }}" @if($kind === $key) aria-current="page" @endif>{{ $labels[$key] }}</a>@endforeach</nav>
<div class="nav-actions"><div class="lang"><span aria-hidden="true">◎</span><a href="{{ $tr ? url()->current() : $alternateUrl }}" lang="tr" class="{{ $tr ? 'selected' : '' }}" @if($tr) aria-current="true" @endif>TR</a><span class="lang-divider">/</span><a href="{{ $tr ? $alternateUrl : url()->current() }}" lang="en" class="{{ !$tr ? 'selected' : '' }}" @if(!$tr) aria-current="true" @endif>EN</a></div>
<a class="button small nav-appointment" href="{{ $wa ?: $url('contact') }}" @if($wa) target="_blank" rel="noopener noreferrer" @endif>{{ $copy('nav_appointment') }} <span>↗</span></a>
<button class="menu-toggle" aria-label="{{ $t('Menüyü aç','Open menu') }}" aria-expanded="false" aria-controls="mobile-menu" aria-haspopup="dialog"><span></span><span></span></button></div>
</div></header>
<dialog id="mobile-menu" class="mobile-menu" aria-label="{{ $t('Mobil menü','Mobile navigation') }}">
<div class="mobile-menu-heading"><span>{{ $t('Menü','Menu') }}</span><button class="mobile-menu-close" type="button" aria-label="{{ $t('Menüyü kapat','Close menu') }}">×</button></div>
<nav id="mobile-nav" class="mobile-nav" aria-label="{{ $t('Mobil menü','Mobile navigation') }}">@foreach($labels as $key=>$label)<a href="{{ $url($key) }}" @if($kind === $key) aria-current="page" @endif>{{ $label }}<span aria-hidden="true">↗</span></a>@endforeach</nav>
</dialog>
<main id="main">@yield('content')</main>
<section class="contact-band"><div class="wrap contact-band-inner"><div><span class="eyebrow light">{{ $copy('cta_label') }}</span><h2>{{ $copy('cta_title') }}</h2><p>{{ $copy('cta_body') }}</p></div><a class="button cream" href="{{ $wa ?: $url('contact') }}" @if($wa) target="_blank" rel="noopener noreferrer" @endif>{{ $copy('cta_button') }} <span>↗</span></a></div></section>
<footer>
    <div class="wrap footer-main">
        <div class="footer-brand">
            <a class="brand" href="{{ $url('home') }}"><x-site-image class="custom-logo" :src="$logoUrl" :alt="$settings->name" sizes="100px" loading="eager" /></a>
            <p>{{ $copy('footer_tagline') }}</p>
            @if($settings->externalUrl('instagram') || $wa || $settings->externalUrl('facebook'))
            <nav class="footer-social" aria-label="{{ $t('Sosyal medya','Social media') }}">
                @if($settings->externalUrl('instagram'))<a href="{{ $settings->externalUrl('instagram') }}" target="_blank" rel="noopener noreferrer" aria-label="Instagram" title="Instagram">@include('site.instagram')</a>@endif
                @if($wa)<a href="{{ $wa }}" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp" title="WhatsApp">@include('site.whatsapp')</a>@endif
                @if($settings->externalUrl('facebook'))<a href="{{ $settings->externalUrl('facebook') }}" target="_blank" rel="noopener noreferrer" aria-label="Facebook" title="Facebook">@include('site.facebook')</a>@endif
            </nav>
            @endif
        </div>
        <nav class="footer-links" aria-label="{{ $t('Alt menü','Footer navigation') }}">
            @foreach(['about','doctor','treatment','post','faq','contact'] as $key)
                <a href="{{ $url($key) }}">{{ $labels[$key] }}</a>
            @endforeach
        </nav>
        <dl class="footer-contact">
            @if($settings->phone)<div class="footer-contact-row"><dt>{{ $t('Telefon','Phone') }}</dt><dd><a href="tel:{{ preg_replace('/[^+0-9]/','',$settings->phone) }}">{{ $settings->phone }}</a></dd></div>@endif
            @if($settings->email)<div class="footer-contact-row"><dt>{{ $t('E-posta','Email') }}</dt><dd><a href="mailto:{{ $settings->email }}">{{ $settings->email }}</a></dd></div>@endif
            @if($settings->translated('address'))<div class="footer-contact-row"><dt>{{ $t('Adres','Address') }}</dt><dd>@if($settings->externalUrl('map_url'))<a href="{{ $settings->externalUrl('map_url') }}" target="_blank" rel="noopener noreferrer">{{ $settings->translated('address') }}</a>@else{{ $settings->translated('address') }}@endif</dd></div>@endif
        </dl>
    </div>
    <div class="wrap footer-bottom"><span>© {{ now('Europe/Istanbul')->year }} {{ $settings->name }}. {{ $t('Tüm hakları saklıdır.','All rights reserved.') }}</span>@if($settings->show_preview_notice)<span>{{ $t('Önizleme · Örnek içerikler ve stok görseller kullanılmıştır.','Preview · Sample content and stock photography.') }}</span>@endif</div>
</footer>
@if($wa)<a class="floating-wa" href="{{ $wa }}" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp">@include('site.whatsapp')</a>@endif
@if($settings->popup_enabled && $settings->popup_image)
<dialog class="announcement-popup" aria-label="{{ $t('Duyuru','Announcement') }}" data-popup-key="{{ hash('sha256', $settings->popup_image) }}">
<button class="announcement-close" type="button" aria-label="{{ $t('Duyuruyu kapat','Close announcement') }}">×</button>
@if($settings->popup_url && str_starts_with($settings->popup_url, 'https://'))<a href="{{ $settings->popup_url }}" target="_blank" rel="noopener noreferrer">@endif
<x-site-image :src="asset('storage/'.$settings->popup_image)" :alt="$settings->translated('popup_alt') ?: $t('Duyuru','Announcement')" sizes="(max-width: 600px) calc(100vw - 36px), 560px" loading="eager" />
@if($settings->popup_url && str_starts_with($settings->popup_url, 'https://'))</a>@endif
</dialog>
@endif
</body></html>
