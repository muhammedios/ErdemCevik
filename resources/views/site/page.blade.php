@extends('site.layout')
@section('content')
@if($kind === 'home')
<section class="hero"><div class="wrap hero-grid"><div class="hero-copy"><div class="eyebrow"><span class="tiny-line"></span>{{ $copy('hero_01') }}</div><h1>@foreach(explode("\n",$page->translated('title')) as $line)<span class="headline-line {{ $loop->last ? 'headline-accent' : '' }}">{{ $line }}</span>@endforeach</h1><p>{{ $page->translated('excerpt') }}</p><div class="hero-buttons"><a class="button" href="{{ $wa ?: $url('contact') }}" @if($wa) target="_blank" rel="noopener noreferrer" @endif>{{ $copy('hero_02') }} <span>↗</span></a><a class="text-link" href="{{ $url('about') }}">{{ $copy('hero_03') }} <span>→</span></a></div>@include('site.hero-shortcuts', ['mobile' => false])</div><div class="clinic-board">
    <div class="board-heading"><span>{{ $copy('hero_08') }}</span><span aria-hidden="true">✳</span></div>
    <div class="board-main-photo"><img src="{{ $page->imageUrl() ?: asset('images/clinic.jpg') }}" alt="{{ $copy('hero_image_alt') }}" fetchpriority="high" width="1600" height="1200"><span>{{ $copy('hero_09') }}</span><a href="{{ $url('about') }}" aria-label="{{ $t('Kliniğimizi tanıyın','Discover our clinic') }}">↗</a></div>
    <div class="board-caption"><span class="board-caption-icon" aria-hidden="true">@include('site.tooth')</span><div><strong>{{ $copy('hero_10') }}</strong><span>{{ $copy('hero_11') }}</span></div><span aria-hidden="true">✧</span></div>
</div>@include('site.hero-shortcuts', ['mobile' => true])</div></section>
<div class="discovery-dock wrap"><a href="{{ $url('treatment') }}"><span class="dock-number">01</span><div><small>{{ $copy('dock_01') }}</small><strong>{{ $copy('dock_02') }}</strong></div><span class="dock-arrow">↗</span></a><a href="{{ $url('faq') }}"><span class="dock-number">02</span><div><small>{{ $copy('dock_03') }}</small><strong>{{ $copy('dock_04') }}</strong></div><span class="dock-arrow">↗</span></a><a href="{{ $wa ?: $url('contact') }}" @if($wa) target="_blank" rel="noopener noreferrer" @endif><span class="dock-icon">@include('site.whatsapp')</span><div><small>{{ $copy('dock_05') }}</small><strong>{{ $copy('dock_06') }}</strong></div><span class="dock-arrow">↗</span></a></div>
<div class="values-strip"><div class="wrap"><span>{{ $copy('values_01') }}</span><i>✧</i><span>{{ $copy('values_02') }}</span><i>✧</i><span>{{ $copy('values_03') }}</span><i>✧</i><span>{{ $copy('values_04') }}</span></div></div>
<section class="section treatments"><div class="wrap"><div class="section-head"><div><span class="eyebrow">{{ $copy('treatments_01') }}</span><h2>{{ $copy('treatments_02') }}<br><em>{{ $copy('treatments_03') }}</em></h2></div><div class="section-intro"><p>{{ $copy('treatments_04') }}</p><a class="text-link" href="{{ $url('treatment') }}">{{ $copy('treatments_05') }} <span>↗</span></a></div></div><div class="treatment-grid">@foreach($treatments as $item)@include('site.treatment-card')@endforeach</div></div></section>
<section class="section about-section"><div class="wrap about-grid"><div class="about-photo"><img src="{{ $settings->siteImageUrl('home_about', 'images/care.jpg') }}" loading="lazy" alt="{{ $copy('about_image_alt') }}"><span class="photo-label">{{ $copy('about_01') }} <span>↗</span></span></div><div class="about-copy"><span class="eyebrow">{{ $copy('about_02') }}</span><h2>{{ $copy('about_03') }}<br><em>{{ $copy('about_04') }}</em></h2><p class="lead">{{ $copy('about_05') }}</p><p>{{ $copy('about_06') }}</p><a class="text-link" href="{{ $url('about') }}">{{ $copy('about_07') }} <span>↗</span></a><div class="signature">{{ $copy('about_signature') }} <span>{{ $copy('about_role') }}</span></div><div class="approach-details"><div><span>01</span><p>{{ $copy('about_08') }}</p></div><div><span>02</span><p>{{ $copy('about_09') }}</p></div><div><span>03</span><p>{{ $copy('about_10') }}</p></div></div></div></div></section>
<section class="section doctor-section"><div class="wrap doctor-preview doctor-count-{{ $doctors->count() }}"><div><span class="eyebrow">{{ $copy('doctors_01') }}</span><h2>{{ $copy('doctors_02') }}<br><em>{{ $copy('doctors_03') }}</em></h2></div>@foreach($doctors as $item)<a class="doctor-feature" href="{{ $url('doctor',$item->slug) }}"><div class="doctor-avatar">@if($item->imageUrl())<img src="{{ $item->imageUrl() }}" alt="{{ $item->translated('title') }}" loading="lazy">@else<span>eç</span>@endif</div><div><h3>{{ $item->translated('title') }}</h3><p>{{ $item->translated('excerpt') }}</p></div><span class="round-arrow">↗</span></a>@endforeach</div></section>
<section class="section faq-section"><div class="wrap faq-grid"><div><span class="eyebrow">{{ $copy('faq_01') }}</span><h2>{{ $copy('faq_02') }}<br><em>{{ $copy('faq_03') }}</em></h2><p>{{ $copy('faq_04') }}</p><a class="text-link" href="{{ $url('faq') }}">{{ $copy('faq_05') }} <span>↗</span></a></div><div class="faq-list">@foreach($faqs as $item)@include('site.faq-item')@endforeach</div></div></section>
<section class="section blog-section"><div class="wrap"><div class="section-head"><div><span class="eyebrow">{{ $copy('blog_01') }}</span><h2>{{ $copy('blog_02') }} <em>{{ $copy('blog_03') }}</em></h2></div><a class="text-link" href="{{ $url('post') }}">{{ $copy('blog_04') }} <span>↗</span></a></div><div class="blog-grid">@foreach($posts as $item)@include('site.post-card')@endforeach</div></div></section>
@else
<section class="page-heading"><div class="wrap"><div class="breadcrumb"><a href="{{ $url('home') }}">{{ $labels['home'] }}</a><span>/</span>@if($slug)<a href="{{ $url($kind) }}">{{ $labels[$kind] }}</a><span>/</span>@endif<span>{{ $page?->translated('title') ?: $labels[$kind] }}</span></div><span class="eyebrow">{{ $settings->name }} · {{ $t('KLİNİĞİ','CLINIC') }}</span><h1>{{ $page?->translated('title') ?: $labels[$kind] }}</h1>@if($page?->translated('excerpt'))<p>{{ $page->translated('excerpt') }}</p>@elseif($kind === 'treatment')<p>{{ $copy('list_treatments') }}</p>@elseif($kind === 'post')<p>{{ $copy('list_blog') }}</p>@elseif($kind === 'faq')<p>{{ $copy('list_faq') }}</p>@endif</div></section>
<section class="section inner-content"><div class="wrap">
@if($kind === 'contact')
<div class="contact-layout">
    <div class="contact-location">
        <span class="eyebrow">{{ $t('BİZE ULAŞIN','FIND US') }}</span>
        <h2>{{ $t('Adres','Address') }}</h2>
        <p class="contact-address">{{ preg_replace('/\s+/u', ' ', $settings->translated('address')) }}</p>
        @if($settings->map_url && $settings->translated('address'))
            <div class="contact-map">
                <iframe title="{{ $t('Erdem Çevik kliniği konumu','Erdem Çevik clinic location') }}" src="{{ $settings->mapEmbedUrl() }}" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
            </div>
            <a class="text-link" href="{{ $settings->map_url }}" target="_blank" rel="noopener noreferrer">{{ $t('Google Haritalar’da aç','Open in Google Maps') }} <span>↗</span></a>
        @endif
    </div>
    <div class="contact-sidebar">
        <div class="contact-hours">
            <span class="eyebrow">{{ $t('ZİYARETİNİZİ PLANLAYIN','PLAN YOUR VISIT') }}</span>
            <h2>{{ $t('Çalışma saatleri','Opening hours') }}</h2>
            <dl class="weekly-hours">
                @foreach(preg_split('/\R/u', $settings->translated('hours'), -1, PREG_SPLIT_NO_EMPTY) as $line)
                    @php($parts = explode(':', $line, 2))
                    <div><dt>{{ trim($parts[0]) }}</dt><dd>{{ isset($parts[1]) ? trim($parts[1]) : '' }}</dd></div>
                @endforeach
            </dl>
        </div>
        <div class="contact-phone-block">
            @if($settings->phone)
                <h3>{{ $t('Telefon','Phone') }}</h3>
                <a class="contact-phone-number" href="tel:{{ preg_replace('/[^+0-9]/','',$settings->phone) }}">{{ $settings->phone }} <span aria-hidden="true">↗</span></a>
            @endif
            @if($settings->email)<a class="contact-email" href="mailto:{{ $settings->email }}">{{ $settings->email }}</a>@endif
            @if($wa)
                <a class="button contact-whatsapp" href="{{ $wa }}" target="_blank" rel="noopener noreferrer"><span class="contact-wa-icon" aria-hidden="true">@include('site.whatsapp')</span>{{ $t('WhatsApp’tan yazın','Message on WhatsApp') }} <span>↗</span></a>
            @endif
        </div>
    </div>
</div>
@elseif($kind === 'doctor' && $slug)
<div class="doctor-profile">
    <div class="doctor-profile-portrait">
        @if($page->imageUrl())<img src="{{ $page->imageUrl() }}" alt="{{ $page->translated('title') }}">
        @else<div class="doctor-placeholder"><span>eç</span><small>{{ $t('Hekim fotoğrafı yakında eklenecek.','Dentist photograph coming soon.') }}</small></div>@endif
    </div>
    <article class="doctor-profile-info">
        <span class="eyebrow">{{ $labels['doctor'] }}</span>
        <h2>{{ $page->translated('title') }}</h2>
        @if($page->translated('excerpt'))<p class="doctor-profile-role">{{ $page->translated('excerpt') }}</p>@endif
        <div class="prose">{!! str($page->translated('body'))->sanitizeHtml() !!}</div>
        <div class="doctor-profile-contact">
            <h3>{{ $copy('article_title') }}</h3>
            <p>{{ $copy('article_body') }}</p>
            <a class="button" href="{{ $wa ?: $url('contact') }}" @if($wa) target="_blank" rel="noopener noreferrer" @endif>{{ $copy('article_button') }} <span aria-hidden="true">↗</span></a>
        </div>
        <a class="text-link doctor-profile-back" href="{{ $url('doctor') }}">← {{ $labels['doctor'] }}</a>
    </article>
</div>
@elseif($kind === 'about' || $slug)
<div class="article-grid"><article class="prose">@if($page->imageUrl())<img class="article-image" src="{{ $page->imageUrl() }}" alt="{{ $page->translated('title') }}">@elseif($kind === 'doctor')<div class="doctor-placeholder"><span>eç</span><small>{{ $t('Hekim fotoğrafı yakında eklenecek.','Dentist photograph coming soon.') }}</small></div>@endif{!! str($page->translated('body'))->sanitizeHtml() !!}</article><aside class="article-aside"><span class="eyebrow">{{ $copy('article_label') }}</span><h3>{{ $copy('article_title') }}</h3><p>{{ $copy('article_body') }}</p><a class="button" href="{{ $wa ?: $url('contact') }}" @if($wa) target="_blank" rel="noopener noreferrer" @endif>{{ $copy('article_button') }} ↗</a><a class="text-link" href="{{ $url($kind === 'about' ? 'doctor' : $kind) }}">{{ $t('Keşfetmeye devam edin','Keep exploring') }} →</a></aside></div>
@elseif($kind === 'faq')<div class="faq-list standalone">@forelse($items as $item)@include('site.faq-item')@empty<p>{{ $t('Sorular yakında eklenecek.','Questions will be added soon.') }}</p>@endforelse</div>
@elseif($kind === 'treatment')<div class="treatment-grid">@forelse($items as $item)@include('site.treatment-card')@empty<p>{{ $t('Tedaviler yakında eklenecek.','Treatments will be added soon.') }}</p>@endforelse</div>
@elseif($kind === 'post')<div class="blog-grid">@forelse($items as $item)@include('site.post-card')@empty<p>{{ $t('Yazılar yakında eklenecek.','Stories will be added soon.') }}</p>@endforelse</div>
@elseif($kind === 'doctor')<div class="doctors-grid">@forelse($items as $item)<a href="{{ $url('doctor',$item->slug) }}" class="doctor-card"><div class="doctor-placeholder">@if($item->imageUrl())<img src="{{ $item->imageUrl() }}" alt="{{ $item->translated('title') }}">@else<span>eç</span><small>{{ $t('Hekim fotoğrafı yakında eklenecek.','Dentist photograph coming soon.') }}</small>@endif</div><h2>{{ $item->translated('title') }} <span>↗</span></h2><p>{{ $item->translated('excerpt') }}</p></a>@empty<p>{{ $t('Hekimlerimiz yakında eklenecek.','Our dentists will be added soon.') }}</p>@endforelse</div>
@endif
@if($items instanceof \Illuminate\Contracts\Pagination\Paginator && $items->hasPages())<nav class="pagination" aria-label="{{ $t('Sayfalama','Pagination') }}">@if($items->previousPageUrl())<a class="button outline" href="{{ $items->previousPageUrl() }}">← {{ $t('Önceki','Previous') }}</a>@endif<span>{{ $items->currentPage() }} / {{ $items->lastPage() }}</span>@if($items->nextPageUrl())<a class="button outline" href="{{ $items->nextPageUrl() }}">{{ $t('Sonraki','Next') }} →</a>@endif</nav>@endif
</div></section>
@endif
@endsection
