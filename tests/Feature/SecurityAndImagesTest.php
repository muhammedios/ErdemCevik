<?php

namespace Tests\Feature;

use App\Filament\Resources\ClinicSettings\Pages\ManageClinicSettings;
use App\Models\ClinicSetting;
use App\Models\Content;
use App\Models\User;
use App\Support\ResponsiveImage;
use Filament\Actions\Testing\TestAction;
use Filament\Auth\Pages\Login;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class SecurityAndImagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->seed();
    }

    public function test_security_headers_and_private_panel_responses(): void
    {
        $this->get('/tr')->assertOk()->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->assertHeader('Content-Security-Policy');
        $response = $this->get('/admin/login')->assertOk()->assertHeader('X-Robots-Tag', 'noindex, nofollow');
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        foreach (['/.env', '/.git/config', '/database/database.sqlite', '/local-access.md', '/storage/../.env'] as $path) {
            $response = $this->get($path);
            $this->assertContains($response->status(), [403, 404], $path);
        }
    }

    public function test_invalid_external_links_and_active_html_are_not_rendered(): void
    {
        ClinicSetting::first()->update(['instagram' => 'javascript:alert(1)', 'facebook' => 'data:text/html,bad', 'map_url' => 'javascript:alert(2)', 'popup_url' => 'javascript:alert(3)']);
        $this->get('/tr')->assertDontSee('javascript:')->assertDontSee('data:text/html');
        $post = Content::where('kind', 'post')->first();
        $post->update(['body' => ['tr' => '<p>Safe</p><a href="javascript:alert(1)">bad</a><img src="x" onerror="alert(2)"><iframe src="https://evil.example"></iframe>']]);
        $this->get('/tr/blog/'.$post->slug)->assertOk()->assertSee('Safe')->assertDontSee('javascript:')->assertDontSee('onerror=')->assertDontSee('https://evil.example');
    }

    public function test_popup_rejects_active_file_formats(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        foreach (['bad.svg' => '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>', 'bad.php' => '<?php echo 1;'] as $name => $bytes) {
            Livewire::test(ManageClinicSettings::class)->mountAction(TestAction::make('edit')->table(ClinicSetting::first()))
                ->fillForm(['popup_image' => UploadedFile::fake()->createWithContent($name, $bytes)])
                ->callMountedAction()->assertHasActionErrors(['popup_image']);
        }
    }

    public function test_optimized_images_preserve_originals_and_use_responsive_priority(): void
    {
        $path = 'content/performance.jpg';
        Storage::disk('public')->put($path, file_get_contents(public_path('images/clinic-treatment.jpg')));
        $hash = hash_file('sha256', Storage::disk('public')->path($path));
        $home = Content::where('kind', 'page')->where('slug', 'home')->first();
        $home->update(['image' => $path]);
        $image = ResponsiveImage::attributes('/storage/'.$path);
        $this->assertStringContainsString('/storage/optimized/', $image['src']);
        $this->assertStringContainsString('320w', $image['srcset']);
        $this->assertStringContainsString('1440w', $image['srcset']);
        $this->assertSame($hash, hash_file('sha256', Storage::disk('public')->path($path)));
        $this->assertLessThan(Storage::disk('public')->size($path), Storage::disk('public')->size(substr($image['src'], 9)));
        $response = $this->get('/tr')->assertOk()->assertSee('srcset=', false)->assertSee('fetchpriority="high"', false);
        $doc = new \DOMDocument;
        @$doc->loadHTML($response->getContent());
        $xpath = new \DOMXPath($doc);
        $hero = $xpath->query('//div[@class="board-main-photo"]/img')->item(0);
        $this->assertSame('eager', $hero->getAttribute('loading'));
        $this->assertSame($image['src'], $hero->getAttribute('src'));
        $this->assertSame('/storage/missing.jpg', ResponsiveImage::attributes('/storage/missing.jpg')['src']);
        $this->assertNull(ResponsiveImage::attributes('/storage/../../.env')['srcset']);
    }

    public function test_login_attempts_are_rate_limited(): void
    {
        $form = Livewire::test(Login::class)
            ->fillForm(['email' => 'nobody@example.com', 'password' => 'incorrect-password']);
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $form->call('authenticate')->assertHasErrors(['data.email']);
        }
        $form->call('authenticate')->assertNotified();
        $this->assertGuest();
    }

    public function test_livewire_requests_require_a_csrf_token(): void
    {
        $uri = collect(Route::getRoutes())->first(fn ($route) => $route->getName() === 'default-livewire.update')->uri();
        // Laravel normally bypasses CSRF during tests; exercise the real production check.
        $this->app->instance('env', 'production');
        $this->postJson('/'.$uri,[])->assertStatus(419);
    }
}
