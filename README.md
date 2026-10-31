# Erdem Çevik — Klinik web sitesi

Laravel 12 + Filament 5 ile Türkçe / İngilizce klinik sitesi. İlk tasarım; kırık beyaz, koyu yeşil, serif başlıklar ve klinik fotoğrafları üzerine kuruldu.

## Yerel çalıştırma

PHP 8.2+, Composer ve SQLite gerekir. CSS ve JavaScript doğrudan `public/` üzerinden sunulur; mevcut ziyaretçi arayüzü için Node/Vite derlemesi gerekmez.

```sh
composer install
cp .env.example .env
php artisan key:generate
```

`database/database.sqlite` dosyasını oluşturduktan sonra:

```sh
php artisan migrate --seed
php artisan storage:link
php artisan serve --host=127.0.0.1 --port=8000
```

Mevcut çalışma klasöründe kurulum, veritabanı ve örnek içerikler hazırdır. `.env` dosyasını yeniden oluşturmak gerekmez.

- Türkçe: http://127.0.0.1:8000/tr
- İngilizce: http://127.0.0.1:8000/en
- Yönetim paneli: http://127.0.0.1:8000/admin
- Bu bilgisayarda oluşturulan yerel yönetici hesabı: `local-access.md` (Git dışında).

Yeni bir yönetici oluşturmak veya mevcut hesabın şifresini değiştirmek için:

```sh
php artisan clinic:admin yonetici@example.com --name="Klinik Yöneticisi"
```

Şifre terminalde gizli olarak sorulur. Genel kullanıcı hesapları panele erişemez; yönetici bayrağı gerekir. `make:filament-user` komutu bu projede tek başına panel yetkisi vermez; yukarıdaki komutu kullanın.

## Panel

- **Sayfalar:** Ana sayfa karşılama başlığı/açıklaması/görseli, hakkımızda, iletişim giriş metni ve SEO alanları. Başlıklarda satır sonu kullanılabilir.
- **Site metinleri ve görseller:** Ana sayfanın açılış, hızlı erişim, tedavi, klinik tanıtımı, doktor, SSS ve blog bölüm metinleri; üst şerit, buton yazıları, marka alt başlığı, alt iletişim bandı, footer tanıtımı, liste girişleri ve detay sayfasındaki iletişim kutusu. Türkçe ve İngilizce ayrı sekmelerdedir. Klinik tanıtım görseli ve görsellerin alternatif metinleri de düzenlenebilir. Önizleme notu buradan kapatılabilir.
- **Doktorlarımız:** Ad, iki dilde unvan, özgeçmiş, fotoğraf, sıralama ve yayın durumu.
- **Tedavilerimiz:** İki dilde başlık, açıklama, detay içeriği ve SEO alanları.
- **Blog yazıları:** İki dilde yazı, kapak görseli, SEO ve taslak/yayın durumu.
- **Sık sorulan sorular:** İki dilde soru/yanıt ve sıralama.
- **Klinik ayarları:** Marka adı, logo, telefon, WhatsApp, e-posta, adres, çalışma saatleri, Instagram/Facebook bağlantıları, isteğe bağlı WhatsApp URL ve harita bağlantısı. Linki tanımlanan sosyal medya ikonları footer’da görünür; WhatsApp URL boşken numara kullanılır.

Her kayıt Türkçe ve İngilizce sekmelerinden düzenlenir. Dil değişimi ziyaretçinin bulunduğu detay sayfasını korur. URL kısa adı iki dilde ortaktır; sayfa yolları Türkçe/İngilizce yerelleştirilmiştir. Sistem sayfalarının kısa adı değiştirilemez ve bu sayfalar silinemez.

Randevu bir WhatsApp yönlendirmesidir. Klinik ayarlarına ülke koduyla gerçek numara girilince butonlar otomatik olarak WhatsApp’a bağlanır. Numara boşken iletişim sayfasına yönlenir. Randevu takvimi veya hasta verisi kaydı bulunmaz.

Tanıtım metinleri `Klinik ayarları` kaydının `site_texts` alanında saklanır. Eski kayıtlar için `config/site-content.php` mevcut tasarımı koruyan başlangıç metinlerini sağlar; form açıldığında bu metinler otomatik doldurulur. Tasarım yerleşimi, menüdeki sistem sayfa adları ve teknik arayüz mesajları kodda kalır. Tedavi, doktor, blog ve SSS kartları panel verilerinden otomatik gelir. Bu kategorilerde sıralama panelden sürükle/bırak ile değiştirilebilir.

## Doğrulama

```sh
php artisan test
php artisan view:cache
```

Testler; iki dilde sayfa yanıtları, dil değişimi, taslakların gizliliği, hatalı URL’ler, WhatsApp bağlantıları, panel yetkileri, içerik oluşturma/yayınlama, panel formları ve HTML temizlemeyi kapsar. Test veritabanı bellekte SQLite’tır; yerel içerikler etkilenmez.

## Gerçek içerikler ve yayına hazırlık

Bu sürüm bir tasarım ve geliştirme önizlemesidir. Tedaviler, blog metinleri ve SSS örnek içeriktir. Hekimin özgeçmişi ve fotoğrafı henüz sağlanmadı. Adres, telefon ve çalışma saatleri 31 Ekim 2026 tarihinde kullanıcının paylaştığı Google işletme kaydından doğrulandı: https://share.google/IrvIzsdhPTohB9MxH. WhatsApp bağlantısı kayıttaki cep telefonu üzerinden yapılandırıldı; WhatsApp hesabına mesaj gönderilmedi. E-posta ve Instagram bilgisi kayıtta bulunmadığı için eklenmedi. Stok görseller kliniğin gerçek fotoğrafları değildir; gerçek klinik görselleriyle değiştirilmelidir. Görsellerin kaynakları aşağıdadır.

Yayına geçmeden önce gerçek içerik, klinikte sunulan tedaviler, çeviriler ve gerekli bilgilendirme metinleri tamamlanmalı. Alt bilgideki önizleme notu gerçek içerikler tamamlandıktan sonra Site metinleri ve görseller panelinden kapatılabilir.

PHP çalıştıran bir Laravel sunucusu gerekir. Üretimde `APP_ENV=production`, `APP_DEBUG=false`, gerçek HTTPS `APP_URL`, güvenli oturum çerezleri ve veritabanı/yüklemeler için yedekleme düzeni yapılandırılmalıdır. Web sunucusunun kök dizini `public/` olmalı. Yerel yönetici hesabı yerine üretime özel hesap oluşturulmalı. Filament dosyaları `php artisan filament:assets` ile yayınlanabilir. Sunucu veya alan adına yayın yapılmadı.

Ziyaretçi arayüzünün ortak responsive kuralları `public/css/responsive.css` dosyasındadır. Safari responsive tasarım modunda 320 ve 390 piksel telefon, 768 piksel tablet ve 1440 piksel masaüstü görünümleri kontrol edildi. Mobil menü, blog kartları, iletişim bilgileri ve gömülü harita ayrıca incelendi. Ana sayfanın açılışı tek fotoğraftır; görseller ekran genişliğine göre orantılı boyutlanır.

## Kaynaklar

- Laravel: https://laravel.com/docs/12.x
- Filament: https://filamentphp.com/docs/5.x
- Stok görseller: https://images.unsplash.com/photo-1629909613654-28e377c37b09, https://images.unsplash.com/photo-1606811971618-4486d14f3f99, https://images.unsplash.com/photo-1609840114035-3c981b782dfe
