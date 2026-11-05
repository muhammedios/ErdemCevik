# dterdemcevik.com — SEO ve yayın hazırlığı

Hedeflenen aramalar: Erdem Çevik, diş hekimi Erdem Çevik, Arnavutköy dişçi. Sitede doğru Türkçe yazım olan “diş hekimi” korunur. Kelime doldurma, gizli metin, sahte değerlendirme veya sıralama garantisi kullanılmaz.

## Hazırlanan teknik SEO

- Marka ve Arnavutköy/İstanbul odaklı ana sayfa başlığı ve açıklaması; iç sayfalarda içerik/konu bazında ayrı başlıklar ve açıklamalar.
- Paneldeki SEO başlığı/açıklaması alanları önceliklidir; boş alanlara otomatik varsayılanlar uygulanır.
- Kalıcı alan adına işaret eden canonical adresler; Türkçe/İngilizce karşılıklı hreflang ve Türkçe x-default.
- Sayfalama adresleri kendi sayfasına canonical verir; mevcut olmayan sayfalar 404 döner.
- `/sitemap.xml`: yayındaki sayfalar, hekimler, tedaviler, blog yazıları ve liste sayfaları. Taslaklar ve yönetim paneli dahil edilmez. İçerik kayıtları değiştikçe dinamik güncellenir.
- `/robots.txt`: yerelde tarama kapalı; kalıcı üretim ortamında tarama açık ve sitemap adresi gösterilir. Panelde noindex başlığı ve giriş koruması vardır.
- Dentist, WebSite, WebPage ve BreadcrumbList JSON-LD verileri; ad, mevcut iletişim/adres bilgileri ve gerçek fotoğraf kullanılır. Sahte puan, yorum, uzmanlık veya fiyat eklenmez.
- Open Graph ve sosyal paylaşım başlık/açıklama/görselleri. JSON-LD verileri script etiketi kırılmasına karşı güvenli kodlanır.
- Daha önce eklenen responsive WebP, öncelikli ilk görsel ve lazy loading korunur.

## Yayına geçiş için gerekenler

Alan adı henüz satın alınmadı; hosting/sunucu bilgisi henüz sağlanmadı. Bu nedenle DNS, HTTPS, gerçek Google Search Console doğrulaması, sitemap gönderimi ve canlı dizine ekleme isteği yapılmadı.

1. Alan adını satın alın; hostingin PHP/Composer, SQLite (veya alternatif veritabanı kurulumu), GD/WebP ve Laravel çalıştırma gereksinimlerini doğrulayın.
2. Dosyaları ve mevcut içerik veritabanını güvenli biçimde taşıyın; `public/` web kökü olsun. Veritabanını ve `.env` dosyasını public içine koymayın. Yerel erişim notlarını sunucuya yüklemeyin.
3. `deploy/production.env.example` dosyasını gerçek sunucu yollarıyla doldurun. `APP_KEY` sunucuda `php artisan key:generate` ile oluşturulur; anahtar sonradan rastgele değiştirilmez. `APP_URL` ve `SITE_URL` gerçek HTTPS alan adı olmalı. `APP_ENV=production`, `APP_DEBUG=false`, `SEO_INDEXABLE=true` kullanılmalı.
4. Alan adının DNS kayıtlarını hostinge bağlayın. Ana alan adı için HTTPS kurun. `www` kullanılacaksa aynı siteye 301 yönlendirin; e-posta DNS kayıtlarını değiştirmeyin.
5. Sunucuda `composer install --no-dev --optimize-autoloader`, gerekli veritabanı geçişleri (`php artisan migrate --force`), `php artisan storage:link`, `php artisan media:optimize`, `php artisan optimize` uygulanır. Mevcut içerikleri koruyun; üretim veritabanını demo seed verileriyle değiştirmeyin.
6. Gerçek alan adında ana sayfa, panel, görseller, `/robots.txt` ve `/sitemap.xml` yanıtlarını kontrol edin. Ziyaretçi sayfalarında `noindex` kalmamalı. `robots.txt` içeriği `Disallow: /` olmamalı; `/admin` engeli normaldir. Canonical/OG/JSON-LD görsellerinde localhost veya test alan adı kalmamalı.
7. Google Search Console'da `dterdemcevik.com` alan adı mülkü eklenir. Google'ın verdiği TXT kaydı DNS'e eklenerek mülkiyet doğrulanır. Alternatif URL mülkü/HTML etiketi yöntemi için verilen content değeri `GOOGLE_SITE_VERIFICATION` ile kullanılabilir.
8. Doğrulama sonrasında `https://dterdemcevik.com/sitemap.xml` gönderilir. URL Denetimi'nden ana sayfa için dizine ekleme isteği yapılır. Sonuçlar “Sayfa dizine ekleme” raporundan izlenir.
9. Varsa Google İşletme Profili'ndeki web sitesi adresini kalıcı adrese güncelleyin; ad/adres/telefon siteyle tutarlı olsun.

Google'ın tarama/dizine ekleme ve sıralama kararı kendisine aittir. Alan adı satın almak veya sitemap göndermek tek başına görünürlük ya da belirli bir sıra garantisi vermez. İlk sonuçlar canlı yayın ve Google'ın taramasından sonra değerlendirilebilir.

Kaynaklar: [Google dizine ekleme açıklaması](https://developers.google.com/search/help/crawling-index-faq), [yeniden tarama isteği](https://developers.google.com/search/docs/crawling-indexing/ask-google-to-recrawl), [çok dilli sayfalar](https://developers.google.com/search/docs/specialty/international/localized-versions), [başlık bağlantıları](https://developers.google.com/search/docs/appearance/title-link), [Dentist şeması](https://schema.org/Dentist).
