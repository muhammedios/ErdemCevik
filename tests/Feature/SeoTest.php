<?php

namespace Tests\Feature;

use App\Models\ClinicSetting;
use App\Models\Content;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->seed();
        config(['seo.url' => 'https://dterdemcevik.com', 'app.url' => 'https://dterdemcevik.com', 'seo.indexable' => true]);
    }

    private function document(string $html): \DOMXPath
    {
        $doc = new \DOMDocument;
        @$doc->loadHTML('<?xml encoding="UTF-8">'.$html);

        return new \DOMXPath($doc);
    }

    public function test_local_search_metadata_and_structured_data_use_the_permanent_domain(): void
    {
        $response = $this->get('/tr?utm_source=test')->assertOk();
        $dom = $this->document($response->getContent());
        $this->assertSame('Diş Hekimi Erdem Çevik | Arnavutköy, İstanbul', $dom->evaluate('string(//title)'));
        $this->assertStringContainsString('Arnavutköy diş hekimi Erdem Çevik', $dom->evaluate('string(//meta[@name="description"]/@content)'));
        $this->assertSame('https://dterdemcevik.com/tr', $dom->evaluate('string(//link[@rel="canonical"]/@href)'));
        $this->assertSame('https://dterdemcevik.com/en', $dom->evaluate('string(//link[@hreflang="en"]/@href)'));
        $this->assertStringStartsWith('index, follow', $dom->evaluate('string(//meta[@name="robots"]/@content)'));
        $graph = json_decode($dom->evaluate('string(//script[@type="application/ld+json"])'), true, 512, JSON_THROW_ON_ERROR);
        $business = $graph['@graph'][0];
        $this->assertSame('Dentist', $business['@type']);
        $this->assertSame('Diş Hekimi Erdem Çevik', $business['name']);
        $this->assertStringContainsString('Arnavutköy', $business['address']);
        $this->assertArrayNotHasKey('aggregateRating', $business);
        $this->assertStringStartsWith('https://dterdemcevik.com/', $business['image']);
    }

    public function test_inner_pages_have_distinct_titles_and_matching_language_links(): void
    {
        $tr = $this->document($this->get('/tr/tedavilerimiz/implant')->assertOk()->getContent());
        $en = $this->document($this->get('/en/treatments/implant')->assertOk()->getContent());
        $this->assertStringContainsString('İmplant tedavisi', $tr->evaluate('string(//title)'));
        $this->assertStringContainsString('Dental implants', $en->evaluate('string(//title)'));
        $this->assertSame('https://dterdemcevik.com/en/treatments/implant', $tr->evaluate('string(//link[@hreflang="en"]/@href)'));
        $this->assertSame('https://dterdemcevik.com/tr/tedavilerimiz/implant', $en->evaluate('string(//link[@hreflang="tr"]/@href)'));
        $this->get('/tr/blog?page=999')->assertNotFound();
    }

    public function test_preview_is_not_indexable_but_production_robots_advertises_sitemap(): void
    {
        $this->get('/robots.txt')->assertOk()->assertSee('Sitemap: https://dterdemcevik.com/sitemap.xml')->assertSee('Disallow: /admin');
        config(['seo.indexable' => false]);
        $this->get('/robots.txt')->assertSee('Disallow: /')->assertDontSee('Sitemap:');
        $this->get('/tr')->assertSee('content="noindex, nofollow"', false);
        config(['seo.indexable' => true, 'app.url' => 'http://127.0.0.1:8000']);
        $this->get('/robots.txt')->assertSee('Disallow: /')->assertDontSee('Sitemap:');
    }

    public function test_sitemap_is_valid_xml_and_excludes_drafts_admin_and_local_addresses(): void
    {
        Content::where('slug', 'implant')->update(['published' => false]);
        $response = $this->get('/sitemap.xml')->assertOk()->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
        $xml = simplexml_load_string($response->getContent());
        $this->assertNotFalse($xml);
        $urls = array_map(fn ($node) => (string) $node->loc, iterator_to_array($xml->url, false));
        $this->assertContains('https://dterdemcevik.com/tr', $urls);
        $this->assertContains('https://dterdemcevik.com/en/about', $urls);
        $this->assertContains('https://dterdemcevik.com/tr/doktorlarimiz/erdem-cevik', $urls);
        $this->assertNotContains('https://dterdemcevik.com/tr/tedavilerimiz/implant', $urls);
        $response->assertDontSee('/admin')->assertDontSee('127.0.0.1')->assertDontSee('trycloudflare');
    }

    public function test_seo_fields_remain_editable_and_json_cannot_break_out_of_script(): void
    {
        $home = Content::where('kind', 'page')->where('slug', 'home')->first();
        $home->update(['seo_title' => ['tr' => 'Özel başlık'], 'seo_description' => ['tr' => 'Özel açıklama']]);
        $dom = $this->document($this->get('/tr')->getContent());
        $this->assertSame('Özel başlık', $dom->evaluate('string(//title)'));
        $this->assertSame('Özel açıklama', $dom->evaluate('string(//meta[@name="description"]/@content)'));
        ClinicSetting::first()->update(['name' => '</script><script>alert(1)</script>']);
        $response = $this->get('/tr')->assertDontSee('</script><script>alert(1)</script>', false);
        $dom = $this->document($response->getContent());
        $this->assertNotNull(json_decode($dom->evaluate('string(//script[@type="application/ld+json"])'),true));
    }
}
