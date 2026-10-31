<?php
namespace App\Filament\Resources\Posts;
use App\Filament\Resources\Contents\ContentResource;
use App\Filament\Resources\Posts\Pages\ManagePosts;
use Filament\Support\Icons\Heroicon;
use BackedEnum;
class PostsResource extends ContentResource {
 protected static bool $shouldRegisterNavigation = true;
 protected static ?string $slug = 'posts';
 protected static ?string $navigationLabel = 'Blog yazıları';
 protected static ?string $modelLabel = 'Blog yazısı';
 protected static ?string $pluralModelLabel = 'Blog yazıları';
 protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPencilSquare;
 public static string $contentKind = 'post';
 public static function getPages(): array { return ['index'=>ManagePosts::route('/')]; }
}
