<?php
namespace App\Filament\Resources\SiteTexts;
use App\Filament\Resources\SiteTexts\Pages\ManageSiteTexts;
use App\Models\ClinicSetting;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\{Section,Tabs};
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Components\{TextInput,Textarea,FileUpload,Toggle};
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
class SiteTextsResource extends Resource {
 protected static ?string $model = ClinicSetting::class;
 protected static ?string $slug = 'site-texts';
 protected static ?string $navigationLabel = 'Site metinleri ve görseller';
 protected static ?string $modelLabel = 'Site içeriği';
 protected static ?string $pluralModelLabel = 'Site metinleri ve görseller';
 protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLanguage;
 public static function canCreate(): bool { return false; }
 public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool { return false; }
 public static function form(Schema $schema): Schema {
  $tabs = [];
  foreach (['tr'=>'Türkçe', 'en'=>'English'] as $locale=>$language) {
   $sections = [];
   foreach (config('site-content') as $group) {
    $fields = [];
    foreach ($group['fields'] as $key=>$definition) {
     $component = $definition['multiline'] ? Textarea::make('site_texts.'.$locale.'.'.$key)->rows(3) : TextInput::make('site_texts.'.$locale.'.'.$key);
     $fields[] = $component->label($definition['label'])->default($definition['default'][$locale])->maxLength($definition['multiline'] ? 2000 : 300);
    }
    $sections[] = Section::make($group['label'])->schema($fields)->columns(2)->collapsible()->collapsed();
   }
   $tabs[] = Tab::make($language)->schema($sections);
  }
  return $schema->components([
   Section::make('Ana sayfa görselleri')->description('Ana karşılama görseli ve başlığı Sayfalar → Ana sayfa kaydından düzenlenir.')->schema([
    FileUpload::make('site_images.home_about')->label('Tanıtım bölümü görseli')->image()->disk('public')->directory('site')->visibility('public')->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])->preventFilePathTampering(allowFilePathUsing: fn (string $file, ?ClinicSetting $record): bool => $file === (($record?->site_images ?? [])['home_about'] ?? null))->maxSize(5120),
    Toggle::make('show_preview_notice')->label('Footer’da örnek içerik / önizleme notunu göster')->helperText('Gerçek içerik ve görseller tamamlandıktan sonra kapatabilirsiniz.'),
   ]),
   Tabs::make('Diller')->tabs($tabs),
  ]);
 }
 public static function table(Table $table): Table {
  return $table->columns([TextColumn::make('name')->label('Site'),TextColumn::make('updated_at')->label('Son güncelleme')->dateTime('d.m.Y H:i')])->recordActions([EditAction::make()->mutateRecordDataUsing(function(array $data): array {
   foreach (['tr','en'] as $locale) {
    $defaults = [];
    foreach (config('site-content') as $group) foreach ($group['fields'] as $key=>$definition) $defaults[$key] = $definition['default'][$locale];
    $data['site_texts'][$locale] = array_replace($defaults, $data['site_texts'][$locale] ?? []);
   }
   return $data;
  })->modalWidth('7xl')->label('İçerikleri düzenle')]);
 }
 public static function getPages(): array { return ['index'=>ManageSiteTexts::route('/')]; }
}
