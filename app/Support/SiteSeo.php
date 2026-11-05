<?php

namespace App\Support;

use App\Models\ClinicSetting;
use App\Models\Content;
use Illuminate\Support\Str;

class SiteSeo
{
    public const PATHS = [
        'tr' => ['home' => '', 'about' => 'hakkimizda', 'doctor' => 'doktorlarimiz', 'treatment' => 'tedavilerimiz', 'post' => 'blog', 'faq' => 'sss', 'contact' => 'iletisim'],
        'en' => ['home' => '', 'about' => 'about', 'doctor' => 'doctors', 'treatment' => 'treatments', 'post' => 'blog', 'faq' => 'faq', 'contact' => 'contact'],
    ];

    public static function url(string $locale, string $kind = 'home', ?string $slug = null, ?int $page = null): string
    {
        $path = '/'.$locale.(self::PATHS[$locale][$kind] ? '/'.self::PATHS[$locale][$kind] : '').($slug ? '/'.$slug : '');

        return rtrim(config('seo.url'), '/').$path.($page && $page > 1 ? '?page='.$page : '');
    }

    public static function indexable(): bool
    {
        $host = parse_url(config('app.url'), PHP_URL_HOST) ?: '';

        return (bool) config('seo.indexable') && $host !== '' && ! in_array($host, ['localhost', '127.0.0.1', '::1'], true)
            && ! str_ends_with($host, '.trycloudflare.com');
    }

    public static function metadata(string $locale, string $kind, ?Content $content, ?string $slug, ClinicSetting $settings, array $labels, int $page = 1): array
    {
        $tr = $locale === 'tr';
        $brand = ($tr ? 'Diş Hekimi ' : 'Dentist ').$settings->name;
        $location = config('seo.location.'.$locale);
        $subject = $slug ? $content?->translated('title', $locale) : ($kind === 'home' ? $location : $labels[$kind]);
        $title = $content?->translated('seo_title', $locale) ?: ($kind === 'doctor' && $slug ? $brand.' | '.$location : $brand.' | '.$subject);
        $defaults = $tr ? [
            'home' => "$brand, $location. Ağız ve diş sağlığı, muayene ve randevu bilgileri. Adres ve iletişim bilgilerine ulaşın.",
            'about' => "$brand hakkında bilgi edinin. Çalışma yaklaşımımızı, hekimimizi ve muayene ortamını tanıyın.",
            'doctor' => "$brand ile tanışın. Hekim bilgileri, muayene ve randevu için iletişim bilgilerine ulaşın.",
            'treatment' => "Ağız ve diş sağlığı tedavileri hakkında bilgi edinin. Kişisel muayene ve değerlendirme için $brand ile iletişime geçin.",
            'post' => "$brand web sitesinde ağız ve diş sağlığına ilişkin bilgilendirme yazılarını ve randevu süreciyle ilgili notları okuyun.",
            'faq' => 'Randevu, ilk görüşme ve muayene süreci hakkında sık sorulan soruların yanıtlarını inceleyin.',
            'contact' => "$brand iletişim bilgileri: $location adresi, telefon, çalışma saatleri ve konum. Randevu için bize ulaşın.",
        ] : [
            'home' => "$brand in $location. Oral and dental health, appointment information, address and contact details.",
            'about' => "Get to know $brand, our approach to dental care and the setting for your visit.",
            'doctor' => "Meet $brand. Find dentist information and contact details for appointments and individual assessment.",
            'treatment' => "Learn about oral and dental care. Contact $brand for an individual examination and assessment.",
            'post' => "Read dental health information and notes about the appointment process from $brand.",
            'faq' => 'Find answers to frequently asked questions about appointments, your first visit and the examination process.',
            'contact' => "Contact $brand in $location. Find our address, phone number, opening hours and location for your visit.",
        ];
        $description = $content?->translated('seo_description', $locale) ?: ($slug ? $content?->translated('excerpt', $locale) : null);
        $description = $description ?: $defaults[$kind];
        if ($slug && $kind !== 'doctor' && ! $content?->translated('seo_description', $locale)) {
            $description = $subject.'. '.$description;
        }
        if ($page > 1) {
            $title .= $tr ? ' | Sayfa '.$page : ' | Page '.$page;
        }
        $canonical = self::url($locale, $kind, $slug, $page);
        $imagePath = $content?->imageUrl() ?: ($settings->logo ? asset('storage/'.$settings->logo) : asset('images/cevik-logo.jpeg'));
        $image = rtrim(config('seo.url'), '/').(parse_url($imagePath, PHP_URL_PATH) ?: '/images/cevik-logo.jpeg');

        return [
            'title' => self::plain($title),
            'description' => Str::limit(self::plain($description), 165, '…'),
            'canonical' => $canonical,
            'alternates' => ['tr' => self::url('tr', $kind, $slug, $page), 'en' => self::url('en', $kind, $slug, $page)],
            'image' => $image,
            'graph' => self::graph($locale, $brand, $settings, $kind, $content, $slug, $labels, $canonical, $image),
        ];
    }

    private static function plain(string $value): string
    {
        return trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags($value), ENT_QUOTES, 'UTF-8')));
    }

    private static function graph(string $locale, string $brand, ClinicSetting $settings, string $kind, ?Content $content, ?string $slug, array $labels, string $canonical, string $image): array
    {
        $home = self::url($locale);
        $base = rtrim(config('seo.url'), '/');
        $business = array_filter([
            '@type' => 'Dentist', '@id' => $base.'/#dentist', 'name' => $brand, 'url' => $home,
            'telephone' => $settings->phone, 'email' => $settings->email,
            'address' => self::plain($settings->translated('address')),
            'image' => $base.'/images/clinic-reception.jpg', 'hasMap' => $settings->externalUrl('map_url'),
            'sameAs' => array_values(array_filter([$settings->externalUrl('instagram'), $settings->externalUrl('facebook')])),
        ]);
        $graph = [
            $business,
            ['@type' => 'WebSite', '@id' => $base.'/#website', 'url' => self::url('tr'), 'name' => 'Diş Hekimi '.$settings->name, 'inLanguage' => ['tr', 'en'], 'publisher' => ['@id' => $business['@id']]],
            ['@type' => 'WebPage', '@id' => $canonical.'#webpage', 'url' => $canonical, 'name' => $slug ? $content?->translated('title', $locale) : $labels[$kind], 'inLanguage' => $locale, 'isPartOf' => ['@id' => $base.'/#website'], 'about' => ['@id' => $business['@id']]],
        ];
        if ($kind !== 'home') {
            $crumbs = [['@type' => 'ListItem', 'position' => 1, 'name' => $labels['home'], 'item' => $home]];
            $crumbs[] = ['@type' => 'ListItem', 'position' => 2, 'name' => $labels[$kind], 'item' => self::url($locale, $kind)];
            if ($slug) {
                $crumbs[] = ['@type' => 'ListItem', 'position' => 3, 'name' => $content->translated('title', $locale), 'item' => $canonical];
            }
            $graph[] = ['@type' => 'BreadcrumbList', 'itemListElement' => $crumbs];
        }

        return ['@context' => 'https://schema.org', '@graph' => $graph];
    }
}
