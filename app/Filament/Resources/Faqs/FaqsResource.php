<?php
namespace App\Filament\Resources\Faqs;
use App\Filament\Resources\Contents\ContentResource;
use App\Filament\Resources\Faqs\Pages\ManageFaqs;
use Filament\Support\Icons\Heroicon;
use BackedEnum;
class FaqsResource extends ContentResource {
 protected static bool $shouldRegisterNavigation = true;
 protected static ?string $slug = 'faqs';
 protected static ?string $navigationLabel = 'Sık sorulan sorular';
 protected static ?string $modelLabel = 'Soru';
 protected static ?string $pluralModelLabel = 'Sık sorulan sorular';
 protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQuestionMarkCircle;
 public static string $contentKind = 'faq';
 public static function getPages(): array { return ['index'=>ManageFaqs::route('/')]; }
}
