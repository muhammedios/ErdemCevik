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
})->purpose('Klinik yönetici hesabını oluştur veya güncelle');
