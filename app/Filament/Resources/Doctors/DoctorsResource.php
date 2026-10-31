<?php
namespace App\Filament\Resources\Doctors;
use App\Filament\Resources\Contents\ContentResource;
use App\Filament\Resources\Doctors\Pages\ManageDoctors;
use Filament\Support\Icons\Heroicon;
use BackedEnum;
class DoctorsResource extends ContentResource {
 protected static bool $shouldRegisterNavigation = true;
 protected static ?string $slug = 'doctors';
 protected static ?string $navigationLabel = 'Doktorlarımız';
 protected static ?string $modelLabel = 'Doktor';
 protected static ?string $pluralModelLabel = 'Doktorlarımız';
 protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;
 public static string $contentKind = 'doctor';
 public static function getPages(): array { return ['index'=>ManageDoctors::route('/')]; }
}
