@props(['src', 'alt' => '', 'sizes' => '100vw', 'loading' => 'lazy', 'priority' => false])
@php($image = \App\Support\ResponsiveImage::attributes($src))
<img src="{{ $image['src'] }}" @if($image['srcset']) srcset="{{ $image['srcset'] }}" sizes="{{ $sizes }}" @endif alt="{{ $alt }}" loading="{{ $priority ? 'eager' : $loading }}" decoding="async" @if($priority) fetchpriority="high" @endif @if($image['width']) width="{{ $image['width'] }}" height="{{ $image['height'] }}" @endif {{ $attributes }}>
