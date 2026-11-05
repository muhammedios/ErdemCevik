# Güvenlik ve görsel performansı kontrolü — 5 Kasım 2026

Bu inceleme yerel Laravel uygulamasını, paket kilit dosyasını ve mevcut görselleri kapsar. Canlı bir hosting, alan adı, TLS sertifikası, sunucu erişim izinleri, yedeklerin geri yüklenebilirliği veya gerçek mobil ağ gecikmesi bu ortamda doğrulanmadı. Sıfır açık veya her bağlantıda anında yükleme garantisi değildir.

## Güvenlik

- Composer güvenlik taramasındaki beş Symfony paketi 7.4.22'den 7.4.23'e güncellendi: html-sanitizer, http-foundation, http-kernel, routing ve yaml. Son `composer audit --locked --format=json` çıktısında `advisories` ve `abandoned` boş.
- Panel erişimi yalnızca yönetici hesaplarına açık. Hatalı girişlerde beş denemeden sonra hız sınırı uygulanması test edildi.
- Livewire işlemlerinde CSRF belirteci kontrolü test edildi; belirteçsiz istek 419 dönüyor.
- Görsel yüklemeleri JPEG, PNG ve WebP ile sınırlı; SVG ve PHP yükleme girişimleri reddediliyor. Dosya boyutu sınırları korunuyor.
- Dosya yolu manipülasyonu engellendi. Her görsel alanında başka kayda ait dosyanın yolunu gönderme denemesi reddediliyor. JSON içindeki tanıtım görselinde yalnızca kayıtta zaten bulunan yol ayrıca yetkilendiriliyor.
- Zengin metin temizleme, JavaScript/data bağlantılarının gösterilmemesi, taslak içeriğin gizliliği ve panel yetkileri test edildi.
- Yanıtlara nosniff, SAMEORIGIN, referrer ve permissions başlıkları eklendi. Ziyaretçi sayfalarında CSP etkin; Filament/Livewire panelinin kendi JavaScript gereksinimlerine müdahale edilmedi. Panel ve Livewire yanıtları önbelleğe alınmıyor.
- HTTPS üretim yanıtlarında HSTS etkin. Üretimde oturum çerezlerinin güvenli varsayılanı açık; yerel HTTP çalışması korunuyor.
- `.env`, SQLite ve yerel erişim dosyası Git dışında, web kökünün dışında. `.env` ve veritabanının bu bilgisayardaki izinleri 600 yapıldı. HTTP ile gizli dosyaların erişilebilir olmadığı kontrol edildi.
- Apache kurallarında yükleme dizininden aktif dosya çalıştırılması engellendi. Nginx kullanılırsa eşdeğer kural ayrıca sunucuda tanımlanmalıdır.

## Görseller

Orijinaller ve paneldeki düzenleme/silme akışı korunur. `media:optimize` komutu mevcut yerel fotoğraflardan 320, 640, 960 ve 1440 piksel genişlikte WebP kopyaları üretir. Orijinalden büyük kopya oluşturulmaz. Yeni kaydedilen/değiştirilen görseller için aynı işlem model kaydedilirken otomatik çalışır. Sayfa isteği sırasında görsel dönüştürme yapılmaz. GD/WebP desteği bulunmayan veya bozuk/aşırı büyük görsellerde orijinal dosyaya geri dönülür; dönüştürme 12 milyon piksel ile sınırlıdır.

Sayfalarda `srcset` ve `sizes` ile ekrana uygun dosya seçilir. İlk ana görsel ve içerik sayfasının kapak görseli yüksek öncelikli yüklenir. Diğer fotoğraflar lazy loading kullanır. Boyut bilgileri yerleşim kaymalarını azaltır; çözümleme async yapılır. Apache yapılandırmasına normal görseller için bir günlük, adında sürüm anahtarı bulunan WebP kopyaları için bir yıllık immutable önbellek kuralı eklendi. Bu kurallar `php artisan serve` tarafından uygulanmaz; üretim sunucusunda doğrulanmalıdır.

Ana sayfadaki tedavi odası fotoğrafı için dosya boyutları:

| Sürüm | Bayt |
| --- | ---: |
| Orijinal JPEG | 325217 |
| 320 px WebP | 12450 |
| 640 px WebP | 34736 |
| 960 px WebP | 62206 |
| 1440 px WebP | 109574 |

640 px kopya yaklaşık %89, 1440 px kopya yaklaşık %66 daha küçüktür. Bunlar dosya boyutu ölçümleridir; gerçek yükleme süresi ölçümü değildir. Safari'de ana fotoğrafın görünümü ve güvenlik başlıkları etkin durumdayken mobil menünün açılıp kapanması doğrulandı.

## Yayın sırasında doğrulanacaklar

Web kökü yalnızca `public/` olmalı. Gerçek HTTPS alan adıyla `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL`, `SESSION_SECURE_COOKIE=true` ve uygun oturum/yedek ayarları kullanılmalı. Üretimde ayrı bir yönetici hesabı ve üretime özel sırlar oluşturulmalı. Statik dosya önbellek başlıkları, yükleme dizininde kod çalıştırma yasağı ve gizli dosyalara erişim engeli gerçek web sunucusunda kontrol edilmeli. Yayın ortamında `php artisan storage:link` ve `php artisan media:optimize` çalıştırılmalı.

Yerel PHP paketleri ve uygulama testleri kontrol edildi. Node/Vite bağımlılıkları ziyaretçi arayüzünü çalıştırmak için kullanılmıyor; Node paketleri için kilit dosyası olmayan bu projede ayrı bir npm güvenlik taraması sonucu verilmedi.

## Doğrulama sonucu

`php artisan test`: 37 test, 438 doğrulama başarılı. `php artisan view:cache` ve `git diff --check` başarılı. Gerçek yerel HTTP sunucusunda ana sayfa ve WebP dosyası 200, `/.env` 404 döndü. Site içeriği ve yüklenen orijinal fotoğraflar korunmuştur.

Başvuru kaynakları: [Symfony güvenlik düzeltmesi](https://symfony.com/cve-2026-118517), [Filament dosya yükleme güvenliği](https://filamentphp.com/docs/5.x/forms/file-upload), [MDN responsive görsel özellikleri](https://developer.mozilla.org/en-US/docs/Web/HTML/Reference/Elements/img).
