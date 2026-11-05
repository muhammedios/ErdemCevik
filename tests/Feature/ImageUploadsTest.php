<?php
namespace Tests\Feature;

use App\Filament\Resources\ClinicSettings\Pages\ManageClinicSettings;
use App\Filament\Resources\Doctors\Pages\ManageDoctors;
use App\Filament\Resources\Pages\Pages\ManagePages;
use App\Filament\Resources\Posts\Pages\ManagePosts;
use App\Filament\Resources\SiteTexts\Pages\ManageSiteTexts;
use App\Filament\Resources\Treatments\Pages\ManageTreatments;
use App\Models\{ClinicSetting,Content,User};
use Filament\Actions\Testing\TestAction;
use Filament\Forms\Components\FileUpload;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ImageUploadsTest extends TestCase
{
 use RefreshDatabase;

 public static function imageFields(): array
 {
  return [
   'popup'=>[ManageClinicSettings::class,'settings','popup_image'],
   'logo'=>[ManageClinicSettings::class,'settings','logo'],
   'home introduction'=>[ManageSiteTexts::class,'settings','site_images.home_about'],
   'home'=>[ManagePages::class,'home','image'],
   'about'=>[ManagePages::class,'about','image'],
   'doctor'=>[ManageDoctors::class,'erdem-cevik','image'],
   'post'=>[ManagePosts::class,'ilk-ziyaret','image'],
   'treatment'=>[ManageTreatments::class,'implant','image'],
  ];
 }

 #[\PHPUnit\Framework\Attributes\DataProvider('imageFields')]
 public function test_image_fields_can_reopen_replace_and_remove_saved_uploads(string $page,string $slug,string $field): void
 {
  // Deliberately retain an obsolete application host: previews must stay local.
  config(['app.url'=>'https://old-preview.example']);
  Storage::fake('public');
  $this->seed();
  $this->actingAs(User::factory()->create(['is_admin'=>true]));
  $record=$slug==='settings' ? ClinicSetting::first() : Content::where('slug',$slug)->firstOrFail();
   $record->refresh();
   $original='test/'.md5($page.$field).'.jpg';
   Storage::disk('public')->put($original,file_get_contents(public_path('images/cevik-logo.jpeg')));
   if(str_contains($field,'.')) {
    $images=$record->site_images ?? []; $images['home_about']=$original;
    $record->update(['site_images'=>$images]);
   } else $record->update([$field=>$original]);
   $action=TestAction::make('edit')->table($record);
   // The persisted file must load with metadata and a same-origin preview URL.
   $form=Livewire::test($page)->mountAction($action);
   $form->assertFormFieldExists($field,function(FileUpload $component) use ($original) {
    $this->assertContains($original,$component->getRawState());
    $file=$component->getUploadedFile($original,null);
    $this->assertSame('/storage/'.$original,$file['url']);
    $this->assertSame('image/jpeg',$file['type']);
    return true;
   });
   // Saving an untouched form must not drop the existing image.
   $form->callMountedAction()->assertHasNoActionErrors();
   $this->assertSame($original,data_get($record->fresh(),$field));
   $upload=UploadedFile::fake()->createWithContent('replacement.jpg',file_get_contents(public_path('images/cevik-logo.jpeg')));
   Livewire::test($page)->mountAction($action)->fillForm([$field=>[]])->fillForm([$field=>$upload])->callMountedAction()->assertHasNoActionErrors();
   $replacement=data_get($record->fresh(),$field);
   $this->assertNotSame($original,$replacement);
   Storage::disk('public')->assertExists($replacement);
   // Remove through the actual FileUpload component, then save and reopen.
   $form=Livewire::test($page)->mountAction($action);
   $componentKey=null;$fileKey=null;
   $form->assertFormFieldExists($field,function(FileUpload $component) use ($replacement,&$componentKey,&$fileKey) {
    $files=$component->getRawState();
    $this->assertContains($replacement,$files);
    $componentKey=$component->getKey();
    $fileKey=array_search($replacement,$files,true);
    return true;
   });
   $form->call('callSchemaComponentMethod',$componentKey,'deleteUploadedFile',['fileKey'=>$fileKey]);
   $form->callMountedAction()->assertHasNoActionErrors();
   $this->assertNull(data_get($record->fresh(),$field));
   if($field==='popup_image') {
    $record->update(['popup_enabled'=>true]);
    $this->get('/tr')->assertDontSee('<dialog class="announcement-popup"',false);
   }
   Livewire::test($page)->mountAction($action)->assertFormFieldExists($field,function(FileUpload $component) {
    $this->assertSame([],$component->getRawState());return true;
   });
 }
 #[\PHPUnit\Framework\Attributes\DataProvider('imageFields')]
 public function test_existing_image_paths_cannot_be_replaced_with_another_records_file(string $page,string $slug,string $field): void
 {
  Storage::fake('public');$this->seed();
  $this->actingAs(User::factory()->create(['is_admin'=>true]));
  $record=$slug==='settings' ? ClinicSetting::first() : Content::where('slug',$slug)->firstOrFail();
  $original=data_get($record,$field);
  Storage::disk('public')->put('other-record/secret.jpg',file_get_contents(public_path('images/cevik-logo.jpeg')));
  Livewire::test($page)->mountAction(TestAction::make('edit')->table($record))
   ->fillForm([$field=>['other-record/secret.jpg']])->callMountedAction()->assertHasActionErrors([$field]);
  $this->assertSame($original,data_get($record->fresh(),$field));
 }

}
