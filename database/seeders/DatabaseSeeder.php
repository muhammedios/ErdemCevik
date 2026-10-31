<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Content;
use App\Models\ClinicSetting;
use Illuminate\Support\Facades\Storage;
class DatabaseSeeder extends Seeder {
 public function run(): void {
  // Keep bundled demo photos available to Filament's public-disk uploader.
  foreach (['clinic.jpg','care.jpg','dental.jpg'] as $filename) {
   $path = 'images/'.$filename;
   if (!Storage::disk('public')->exists($path)) Storage::disk('public')->put($path,file_get_contents(public_path($path)));
  }

  ClinicSetting::firstOrCreate(['id'=>1], json_decode(file_get_contents(database_path('seeders/clinic-contact.json')),true));
  foreach (json_decode(file_get_contents(database_path('seeders/clinic-content.json')),true) as $item) {
   Content::firstOrCreate(['kind'=>$item['kind'],'slug'=>$item['slug']],$item);
  }
 }
}
