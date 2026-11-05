<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Support\SiteSeo;

class SeoController extends Controller
{
    public function robots()
    {
        $text = "User-agent: *\n";
        $text .= SiteSeo::indexable()
            ? "Allow: /\nDisallow: /admin\nDisallow: /livewire\nSitemap: ".rtrim(config('seo.url'), '/')."/sitemap.xml\n"
            : "Disallow: /\n";

        return response($text, 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }

    public function sitemap()
    {
        $entries = [];
        $pages = Content::visible()->where('kind', 'page')->whereIn('slug', ['home', 'about', 'contact'])->get();
        foreach ($pages as $page) {
            $entries[] = ['kind' => $page->slug, 'slug' => null, 'updated' => $page->updated_at];
        }
        foreach (['doctor', 'treatment', 'post', 'faq'] as $kind) {
            $entries[] = ['kind' => $kind, 'slug' => null, 'updated' => Content::visible()->where('kind', $kind)->max('updated_at')];
        }
        foreach (Content::visible()->whereIn('kind', ['doctor', 'treatment', 'post'])->get() as $item) {
            $entries[] = ['kind' => $item->kind, 'slug' => $item->slug, 'updated' => $item->updated_at];
        }

        return response()->view('site.sitemap', compact('entries'), 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }
}
