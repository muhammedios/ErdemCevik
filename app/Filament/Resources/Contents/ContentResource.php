<?php
namespace App\Filament\Resources\Contents;
use App\Models\Content;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Components\{TextInput,Textarea,RichEditor,FileUpload,Toggle,Hidden};
use Filament\Tables\Table;
use Filament\Tables\Columns\{TextColumn,IconColumn};
use Filament\Actions\{EditAction,DeleteAction};
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\Unique;
class ContentResource extends Resource {
 protected static ?string $model = Content::class;
 protected static bool $shouldRegisterNavigation = false;
 public static string $contentKind = 'page';
 public static function getEloquentQuery(): Builder { return parent::getEloquentQuery()->where('kind', static::$contentKind); }
 public static function form(Schema $schema): Schema {
  $tabs = [];
  foreach (['tr'=>'Türkçe','en'=>'English'] as $locale=>$label) {
   $tabs[] = Tab::make($label)->schema([
    Textarea::make('title.'.$locale)->label('Başlık')->rows(2)->required()->maxLength(200),
    Textarea::make('excerpt.'.$locale)->label(static::$contentKind === 'doctor' ? 'Hekim unvanı' : 'Kısa açıklama')->rows(3)->maxLength(1000),
    static::$contentKind === 'faq'
      ? Textarea::make('body.'.$locale)->label('Yanıt')->rows(6)->required()
      : RichEditor::make('body.'.$locale)->label('İçerik')->visible(fn (?Content $record) => static::$contentKind !== 'page' || $record?->slug === 'about')->toolbarButtons(['bold','italic','underline','link','h2','h3','bulletList','orderedList','blockquote','undo','redo']),
    TextInput::make('seo_title.'.$locale)->label('SEO başlığı')->maxLength(160),
    Textarea::make('seo_description.'.$locale)->label('SEO açıklaması')->maxLength(320)->rows(2),
   ]);
  }
  return $schema->components([
   Hidden::make('kind')->default(static::$contentKind),
   TextInput::make('slug')->label('URL kısa adı')->required()->maxLength(180)->regex('/^[a-z0-9]+(?:-[a-z0-9]+)*$/')
    ->unique(ignoreRecord:true, modifyRuleUsing: fn(Unique $rule) => $rule->where('kind',static::$contentKind))
    ->disabled(fn (?Content $record) => $record?->kind === 'page')->dehydrated()
    ->helperText(static::$contentKind === 'page' ? 'Sistem sayfalarının URL adları sabittir.' : 'Örnek: estetik-dis-hekimligi. İki dilde aynı kayıt kullanılır.'),
   Toggle::make('published')->label('Yayında')->default(false),
   TextInput::make('sort_order')->label('Sıralama')->numeric()->minValue(0)->default(0)->required(),
   FileUpload::make('image')->label('Görsel')->image()->disk('public')->directory('content')->visibility('public')->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])->preventFilePathTampering()->maxSize(5120)
    ->imageEditor(static::$contentKind === 'doctor')
    ->imageEditorAspectRatios([null, '3:4', '4:5', '1:1'])
    ->helperText(static::$contentKind === 'doctor'
     ? 'JPG, PNG veya WebP. En fazla 5 MB. Fotoğrafı yükledikten sonra kalem simgesinden kırpabilir, yakınlaştırabilir ve uzaklaştırabilirsiniz. Önce düzenleyicide işlemi uygulayın, ardından kaydı kaydedin.'
     : 'JPG, PNG veya WebP. En fazla 5 MB. Stok görsellerin yerine kendi fotoğraflarınızı yükleyebilirsiniz.'),
   Tabs::make('Diller')->tabs($tabs)->columnSpanFull(),
  ])->columns(2);
 }
 public static function table(Table $table): Table {
  return $table->columns([
   TextColumn::make('title.tr')->label('Başlık')->searchable()->sortable(),
   TextColumn::make('title.en')->label('English')->searchable()->toggleable(),
   IconColumn::make('published')->label('Yayında')->boolean(),
   TextColumn::make('sort_order')->label('Sıra')->sortable(),
   TextColumn::make('updated_at')->label('Güncellenme')->dateTime('d.m.Y H:i')->sortable(),
  ])->defaultSort('sort_order')->reorderable('sort_order')->recordActions([
   EditAction::make()->modalWidth('5xl'),
   DeleteAction::make()->hidden(fn (Content $record) => $record->kind === 'page'),
  ]);
 }
 public static function canCreate(): bool { return static::$contentKind !== 'page'; }
 public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool { return $record->kind !== 'page'; }
 public static function getPages(): array { return []; }
}
