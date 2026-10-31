<?php
namespace Tests\Feature;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\{Content,ClinicSetting,User};
use App\Filament\Resources\Doctors\Pages\ManageDoctors;
use App\Filament\Resources\Posts\Pages\ManagePosts;
use App\Filament\Resources\ClinicSettings\Pages\ManageClinicSettings;
use Livewire\Livewire;
use Illuminate\Support\Facades\Storage;
use App\Filament\Resources\Pages\PagesResource;
use App\Filament\Resources\Pages\Pages\ManagePages;
use App\Filament\Resources\SiteTexts\Pages\ManageSiteTexts;
use Filament\Actions\Testing\TestAction;
class ClinicSiteTest extends TestCase {
 use RefreshDatabase;
 protected function setUp(): void { parent::setUp(); $this->seed(); }
 public function test_public_pages_render_in_both_languages(): void {
  $this->get('/')->assertRedirect('/tr');
  foreach (['/tr','/en','/tr/hakkimizda','/en/about','/tr/doktorlarimiz','/en/doctors','/tr/tedavilerimiz','/en/treatments','/tr/blog','/en/blog','/tr/sss','/en/faq','/tr/iletisim','/en/contact','/tr/doktorlarimiz/erdem-cevik','/en/treatments/implant','/tr/blog/ilk-ziyaret'] as $path) $this->get($path)->assertOk();
 }
 public function test_language_switch_keeps_the_current_content(): void {
  $this->get('/tr/tedavilerimiz/implant')->assertSee('/en/treatments/implant')->assertSee('İmplant tedavisi');
  $this->get('/en/treatments/implant')->assertSee('Dental implants')->assertSee('lang="en"',false);
 }
 public function test_drafts_and_unknown_paths_are_not_public(): void {
  Content::where('slug','implant')->update(['published'=>false]);
  $this->get('/tr/tedavilerimiz/implant')->assertNotFound();
  $this->get('/tr/tedavilerimiz')->assertDontSee('İmplant tedavisi');
  foreach (['/de','/tr/unknown','/tr/about','/tr/hakkimizda/extra','/tr/sss/randevu'] as $path) $this->get($path)->assertNotFound();
 }
 public function test_whatsapp_uses_configured_number_and_localized_message(): void {
  ClinicSetting::first()->update(['whatsapp'=>null]);
  $this->get('/tr')->assertDontSee('https://wa.me/');
  ClinicSetting::first()->update(['whatsapp'=>'905551112233']);
  $this->get('/tr')->assertSee('https://wa.me/905551112233?text=Merhaba');
  $this->get('/en')->assertSee('https://wa.me/905551112233?text=Hello');
 }
 public function test_panel_is_protected_and_admin_pages_render(): void {
  $this->get('/admin/doctors')->assertRedirect('/admin/login');
  $user=User::factory()->create();
  $this->actingAs($user)->get('/admin/doctors')->assertForbidden();
  $user->forceFill(['is_admin'=>true])->save();
  foreach (['/admin','/admin/doctors','/admin/pages','/admin/treatments','/admin/posts','/admin/faqs','/admin/clinic-settings'] as $path) $this->actingAs($user)->get($path)->assertOk();
 }
 public function test_admin_can_create_translate_and_publish_a_blog_post(): void {
  $admin=User::factory()->create(['is_admin'=>true]);
  $this->actingAs($admin);
  Livewire::test(ManagePosts::class)->callAction('create',data:[
   'slug'=>'yeni-yazi','title'=>['tr'=>'Yeni yazı','en'=>'New story'],
   'excerpt'=>['tr'=>'Türkçe açıklama','en'=>'English description'],
   'body'=>['tr'=>'<p>Türkçe içerik</p>','en'=>'<p>English content</p>'],
   'published'=>true,'sort_order'=>0,
  ])->assertHasNoActionErrors();
  $this->assertDatabaseHas('contents',['slug'=>'yeni-yazi','kind'=>'post','published'=>1]);
  $this->get('/tr/blog/yeni-yazi')->assertSee('Türkçe içerik');
  $this->get('/en/blog/yeni-yazi')->assertSee('English content');
 }
 public function test_admin_forms_open_and_queries_keep_content_types_separate(): void {
  $this->actingAs(User::factory()->create(['is_admin'=>true]));
  Livewire::test(ManageDoctors::class)->assertCanSeeTableRecords(Content::where('kind','doctor')->get())->assertCanNotSeeTableRecords(Content::where('kind','post')->get())->mountAction(TestAction::make('edit')->table(Content::where('kind','doctor')->first()))->assertActionMounted(TestAction::make('edit')->table(Content::where('kind','doctor')->first()));
  Livewire::test(ManageClinicSettings::class)->mountAction(TestAction::make('edit')->table(ClinicSetting::first()))->assertActionMounted(TestAction::make('edit')->table(ClinicSetting::first()));
 }
 public function test_verified_contact_details_are_available_in_both_languages(): void {
  $this->get('/tr/iletisim')->assertSee('Fatih Caddesi No:62')->assertSee('+90 533 717 69 56')->assertSeeInOrder(['Pazartesi', 'Salı', 'Çarşamba', 'Perşembe', 'Cuma', 'Cumartesi', 'Pazar', 'Kapalı', 'Telefon'])->assertSee('https://share.google/IrvIzsdhPTohB9MxH')->assertSee('https://wa.me/905337176956');
  $this->get('/en/contact')->assertSeeInOrder(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday', 'Phone']);
 }
 public function test_faq_section_grows_with_new_published_questions(): void {
  for ($i=1; $i<=7; $i++) {
   Content::create(['kind'=>'faq','slug'=>'ek-soru-'.$i,'title'=>['tr'=>'Ek soru '.$i,'en'=>'Extra question '.$i],'body'=>['tr'=>'Yanıt','en'=>'Answer'],'published'=>true,'sort_order'=>50+$i]);
  }
  $this->get('/tr')->assertSee('Ek soru 7');
  $this->get('/tr/sss')->assertSee('Ek soru 7');
  $this->get('/en/faq')->assertSee('Extra question 7');
 }
 public function test_admin_can_edit_site_copy_in_both_languages_without_changing_other_fields(): void {
  $this->actingAs(User::factory()->create(['is_admin'=>true]));
  $settings = ClinicSetting::first();
  $action = TestAction::make('edit')->table($settings);
  Livewire::test(ManageSiteTexts::class)
   ->mountAction($action)
   ->assertActionDataSet(['site_texts.tr.hero_01'=>'GÜLÜŞÜNÜZ, SİZİN HİKÂYENİZ','site_texts.en.cta_button'=>'Get in touch'])
   ->fillForm(['site_texts.tr.hero_01'=>'Size özel bir başlangıç','site_texts.en.hero_01'=>'A personal beginning','site_texts.tr.cta_title'=>'Birlikte tanışalım','site_texts.en.cta_title'=>'Let’s meet','show_preview_notice'=>false])
   ->callMountedAction()->assertHasNoActionErrors();
  $settings->refresh();
  $this->assertSame('Size özel bir başlangıç',$settings->siteText('hero_01','tr'));
  $this->assertSame('A personal beginning',$settings->siteText('hero_01','en'));
  $this->assertSame('+90 533 717 69 56',$settings->phone);
  $this->get('/tr')->assertSee('Size özel bir başlangıç')->assertSee('Birlikte tanışalım')->assertSee('Her gülüşe')->assertDontSee('Önizleme · Örnek içerikler');
  $this->get('/en')->assertSee('A personal beginning')->assertSee('Let’s meet');
  $this->get('/tr/iletisim')->assertSee('Birlikte tanışalım');
 }
 public function test_site_copy_is_escaped_and_image_path_is_used(): void {
  ClinicSetting::first()->update(['site_texts'=>['tr'=>['hero_01'=>'<script>alert(1)</script>']], 'site_images'=>['home_about'=>'site/clinic.webp']]);
  $this->get('/tr')->assertDontSee('<script>alert(1)</script>',false)->assertSee('/storage/site/clinic.webp');
 }
 public function test_editing_home_keeps_its_image_and_line_breaks(): void {
  Storage::fake('public');
  $this->seed();
  $this->actingAs(User::factory()->create(['is_admin'=>true]));
  $home = Content::where('kind','page')->where('slug','home')->first();
  $originalImage = $home->image;
  Storage::disk('public')->assertExists($originalImage);
  Livewire::test(ManagePages::class)->mountAction(TestAction::make('edit')->table($home))
   ->callMountedAction()->assertHasNoActionErrors();
  $home->refresh();
  $this->assertSame($originalImage,$home->image);
  $this->assertSame("İyi bir gülüş,\niyi hissettirir.",$home->translated('title','tr'));
  $this->get('/tr')->assertSee('/images/clinic.jpg');
 }
 public function test_social_links_can_be_saved_from_panel_and_appear_in_footer(): void {
  $this->actingAs(User::factory()->create(['is_admin'=>true]));
  Livewire::test(ManageClinicSettings::class)
   ->mountAction(TestAction::make('edit')->table(ClinicSetting::first()))
   ->fillForm(['instagram'=>'https://www.instagram.com/clinic','facebook'=>'https://www.facebook.com/clinic','whatsapp_url'=>'https://wa.me/905551112233'])
   ->callMountedAction()->assertHasNoActionErrors();
  $this->get('/tr')->assertSee('class="footer-social"',false)->assertSee('aria-label="Instagram"',false)->assertSee('aria-label="Facebook"',false)
   ->assertSee('https://www.instagram.com/clinic')->assertSee('https://www.facebook.com/clinic')->assertSee('https://wa.me/905551112233');
  ClinicSetting::first()->update(['instagram'=>null,'facebook'=>null,'whatsapp_url'=>null]);
  $this->get('/tr')->assertDontSee('aria-label="Instagram"',false)->assertDontSee('aria-label="Facebook"',false)->assertSee('https://wa.me/905337176956');
 }
 public function test_rich_text_is_sanitized(): void {
  $post=Content::where('kind','post')->first();
  $post->update(['body'=>['tr'=>'<p>Güvenli içerik</p><script>alert("xss")</script>','en'=>'Safe content']]);
  $this->get('/tr/blog/'.$post->slug)->assertSee('Güvenli içerik')->assertDontSee('<script>alert',false);
 }
}
