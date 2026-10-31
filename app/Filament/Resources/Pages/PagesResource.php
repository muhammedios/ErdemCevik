<?php
namespace App\Filament\Resources\Pages;
use App\Filament\Resources\Contents\ContentResource;
use App\Filament\Resources\Pages\Pages\ManagePages;
use Filament\Support\Icons\Heroicon;
use BackedEnum;
class PagesResource extends ContentResource {
 protected static bool $shouldRegisterNavigation = true;
 protected static ?string $slug = 'pages';
 protected static ?string $navigationLabel = 'Sayfalar';
 protected static ?string $modelLabel = 'Sayfa';
 protected static ?string $pluralModelLabel = 'Sayfalar';
 protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;
 public static string $contentKind = 'page';
 public static function getPages(): array { return ['index'=>ManagePages::route('/')]; }
}
