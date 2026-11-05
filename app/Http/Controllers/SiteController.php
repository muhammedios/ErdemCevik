<?php
namespace App\Http\Controllers;
use App\Models\Content;
use App\Models\ClinicSetting;
class SiteController extends Controller {
 public function show(string $locale, string $section = 'home', ?string $slug = null) {
  app()->setLocale($locale);
  if (request()->segment(2) === 'home' && $slug === null) return redirect('/'.$locale, 301);
  $segments = ['home'=>'home','hakkimizda'=>'about','about'=>'about','doktorlarimiz'=>'doctor','doctors'=>'doctor','tedavilerimiz'=>'treatment','treatments'=>'treatment','blog'=>'post','sss'=>'faq','faq'=>'faq','iletisim'=>'contact','contact'=>'contact'];
  abort_unless(isset($segments[$section]),404);
  $kind = $segments[$section];
  $paths = \App\Support\SiteSeo::PATHS;
  abort_unless($section === ($paths[$locale][$kind] ?: 'home'), 404);
  $url = fn(string $key, ?string $itemSlug = null, ?string $lang = null) => url('/'.($lang ?? $locale).($paths[$lang ?? $locale][$key] ? '/'.$paths[$lang ?? $locale][$key] : '').($itemSlug ? '/'.$itemSlug : ''));
  $settings = ClinicSetting::firstOrFail();
  $page = null; $items = collect();
  if (in_array($kind, ['home','about','contact'])) {
   abort_if($slug !== null,404);
   $page = Content::visible()->where('kind','page')->where('slug',$kind)->firstOrFail();
  } elseif ($slug) {
   abort_if($kind === 'faq',404);
   $page = Content::visible()->where('kind',$kind)->where('slug',$slug)->firstOrFail();
  } else { $query = Content::visible()->where('kind',$kind); $items = $kind === 'faq' ? $query->get() : $query->paginate(9); if ($items instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator) abort_if($items->currentPage() > $items->lastPage(), 404); }
   $tr = $locale === 'tr';
  $t = fn($a,$b) => $tr ? $a : $b;
  $labels = ['home'=>$t('Ana sayfa','Home'),'about'=>$t('Hakkımızda','About us'),'treatment'=>$t('Tedavilerimiz','Treatments'),'doctor'=>$t('Doktorlarımız','Our dentists'),'post'=>'Blog','faq'=>$t('Sık sorulan sorular','FAQs'),'contact'=>$t('İletişim','Contact')];
  $wa = $settings->whatsappUrl();
  $copy = fn(string $key) => $settings->siteText($key);
  $data = compact('copy','t','tr','labels','wa','locale','kind','page','items','settings','url','slug');
  $data['alternateUrl'] = $url($kind,$slug,$locale === 'tr' ? 'en' : 'tr');
  $data['treatments'] = Content::visible()->where('kind','treatment')->limit(6)->get();
  $data['doctors'] = Content::visible()->where('kind','doctor')->limit(3)->get();
  $data['posts'] = Content::visible()->where('kind','post')->limit(3)->get();
  $data['faqs'] = Content::visible()->where('kind','faq')->get();
  $pageNumber = $items instanceof \Illuminate\Contracts\Pagination\Paginator ? $items->currentPage() : 1;
  $data['seo'] = \App\Support\SiteSeo::metadata($locale, $kind, $page, $slug, $settings, $labels, $pageNumber);
  return view('site.page', $data);
 }
}
