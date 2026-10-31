<?php
namespace App\Filament\Resources\ClinicSettings;
use App\Filament\Resources\ClinicSettings\Pages\ManageClinicSettings;
use App\Models\ClinicSetting;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\{Section,Tabs};
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Components\{TextInput,Textarea,FileUpload};
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use BackedEnum;
class ClinicSettingResource extends Resource {
 protected static ?string $model = ClinicSetting::class;
 protected static ?string $navigationLabel = 'Klinik ayarları';
 protected static ?string $modelLabel = 'Klinik ayarları';
 protected static ?string $pluralModelLabel = 'Klinik ayarları';
 protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;
 public static function canCreate(): bool { return false; }
 public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool { return false; }
 public static function form(Schema $schema): Schema {
  return $schema->components([
   Tabs::make('Klinik ayarları')->tabs([
    Tab::make('Genel bilgiler')->schema([
     Section::make('Klinik kimliği')->description('Sitenizde görünen marka adı ve logo.')->schema([
      TextInput::make('name')->label('Klinik / marka adı')->required()->maxLength(120),
      FileUpload::make('logo')->label('Logo')->image()->imagePreviewHeight('120')->disk('public')->directory('branding')->visibility('public')->maxSize(2048)->helperText('PNG, JPG veya WebP. En fazla 2 MB.'),
     ]),
    ]),
    Tab::make('İletişim')->schema([
     Section::make('İletişim bilgileri')->schema([
      TextInput::make('phone')->label('Telefon')->tel()->maxLength(30)->placeholder('+90 533 717 69 56'),
      TextInput::make('email')->label('E-posta')->email()->maxLength(180)->placeholder('iletisim@klinik.com'),
      TextInput::make('whatsapp')->label('WhatsApp numarası')->regex('/^[1-9][0-9]{9,14}$/')->maxLength(15)->helperText('Ülke koduyla, boşluksuz: 905xxxxxxxxx. Randevu butonlarında kullanılır.')->columnSpanFull(),
     ])->columns(['default'=>1,'md'=>2]),
    ]),
    Tab::make('Adres ve saatler')->schema([
     Tabs::make('Adres dilleri')->tabs([
      Tab::make('Türkçe')->schema([
       Textarea::make('address.tr')->label('Adres')->rows(3)->maxLength(1000),
       Textarea::make('hours.tr')->label('Çalışma saatleri')->rows(7)->maxLength(1000)->helperText('Her günü ayrı satıra yazın. Örnek: Pazartesi: 10:00 – 19:00. Pazar: Kapalı.'),
      ]),
      Tab::make('English')->schema([
       Textarea::make('address.en')->label('Address')->rows(3)->maxLength(1000),
       Textarea::make('hours.en')->label('Opening hours')->rows(7)->maxLength(1000)->helperText('One day per line. Example: Monday: 10:00 – 19:00. Sunday: Closed.'),
      ]),
     ]),
    ]),
    Tab::make('Harita')->schema([
     Section::make('Google Haritalar')->description('Konum bağlantısı ve sayfa içinde görüntülenen harita.')->schema([
      TextInput::make('map_url')->label('Harita bağlantısı')->url()->startsWith('https://')->maxLength(1000)->helperText('Haritada aç bağlantısının yönlendireceği adres.'),
      Textarea::make('map_embed_url')->label('Harita yerleştirme adresi')->rows(4)->rules(['nullable','url','starts_with:https://www.google.com/maps/embed?'])->maxLength(4000)->helperText('Google Haritalar → Paylaş → Harita yerleştirme bölümündeki iframe src adresini yapıştırın.'),
     ]),
    ]),
    Tab::make('Sosyal medya')->schema([
     Section::make('Sosyal medya bağlantıları')->description('Bağlantı eklediğiniz platformlar footer’da ikon olarak görünür.')->schema([
      TextInput::make('instagram')->label('Instagram')->url()->startsWith('https://')->maxLength(500)->placeholder('https://www.instagram.com/kullaniciadi'),
      TextInput::make('facebook')->label('Facebook')->url()->startsWith('https://')->maxLength(500)->placeholder('https://www.facebook.com/sayfaadi'),
      TextInput::make('whatsapp_url')->label('Özel WhatsApp bağlantısı')->url()->startsWith('https://')->maxLength(500)->placeholder('https://wa.me/905xxxxxxxxx')->helperText('İsteğe bağlı. Boşsa İletişim sekmesindeki numara kullanılır; girilirse tüm WhatsApp ve randevu butonları bu bağlantıyı açar.'),
     ]),
    ]),
   ])->columnSpanFull(),
  ])->columns(1);
 }
 public static function table(Table $table): Table {
  return $table->columns([TextColumn::make('name')->label('Klinik'),TextColumn::make('phone')->label('Telefon'),TextColumn::make('whatsapp')->label('WhatsApp')])->recordActions([EditAction::make()->modalWidth('4xl')->stickyModalHeader()->stickyModalFooter()]);
 }
 public static function getPages(): array { return ['index'=>ManageClinicSettings::route('/')]; }
}
