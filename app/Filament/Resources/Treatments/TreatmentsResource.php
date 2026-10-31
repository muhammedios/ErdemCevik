<?php
namespace App\Filament\Resources\Treatments;
use App\Filament\Resources\Contents\ContentResource;
use App\Filament\Resources\Treatments\Pages\ManageTreatments;
use Filament\Support\Icons\Heroicon;
use BackedEnum;
class TreatmentsResource extends ContentResource {
 protected static bool $shouldRegisterNavigation = true;
 protected static ?string $slug = 'treatments';
 protected static ?string $navigationLabel = 'Tedavilerimiz';
 protected static ?string $modelLabel = 'Tedavi';
 protected static ?string $pluralModelLabel = 'Tedavilerimiz';
 protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHeart;
 public static string $contentKind = 'treatment';
 public static function getPages(): array { return ['index'=>ManageTreatments::route('/')]; }
}
