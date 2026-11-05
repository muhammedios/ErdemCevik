<?php
use Illuminate\Support\Facades\Artisan;
use App\Models\User;
Artisan::command('clinic:admin {email} {--name=Yönetici}', function () {
 $email = $this->argument('email');
 if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $this->error('Geçerli bir e-posta girin.'); return 1; }
 $password = $this->secret('Şifre (en az 12 karakter)');
 if (strlen($password ?? '') < 12) { $this->error('Şifre en az 12 karakter olmalı.'); return 1; }
 $user = User::firstOrNew(['email'=>$email]);
 $user->name = $this->option('name'); $user->password = $password; $user->is_admin = true; $user->save();
 $this->info('Yönetici hesabı hazır. /admin üzerinden giriş yapabilirsiniz.');
})->purpose('Site yönetici hesabını oluştur veya güncelle');

Artisan::command('media:optimize', function () {
 $count = 0;
 foreach ([public_path('images'), storage_path('app/public')] as $root) {
  foreach (\Illuminate\Support\Facades\File::allFiles($root) as $file) {
   if (str_contains($file->getPathname(), DIRECTORY_SEPARATOR.'optimized'.DIRECTORY_SEPARATOR)) continue;
   if (!in_array(strtolower($file->getExtension()), ['jpg','jpeg','png','webp'], true)) continue;
   $prefix = $root === public_path('images') ? '/images/' : '/storage/';
   \App\Support\ResponsiveImage::generate($prefix.str_replace(DIRECTORY_SEPARATOR, '/', $file->getRelativePathname()));
   $count++;
  }
 }
 $this->info($count.' görsel için ekran boyutuna uygun WebP kopyaları hazırlandı.');
})->purpose('Görsellerin hızlı yüklenen ekran boyutlarına uygun kopyalarını oluştur');
