{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach($entries as $entry)
@foreach(['tr', 'en'] as $lang)
    <url>
        <loc>{{ \App\Support\SiteSeo::url($lang, $entry['kind'], $entry['slug']) }}</loc>
        @if($entry['updated'])<lastmod>{{ \Illuminate\Support\Carbon::parse($entry['updated'])->toAtomString() }}</lastmod>@endif
    </url>
@endforeach
@endforeach
</urlset>
