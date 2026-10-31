<?php
namespace App\Filament\Resources\SiteTexts\Pages;
use App\Filament\Resources\SiteTexts\SiteTextsResource;
use Filament\Resources\Pages\ManageRecords;
class ManageSiteTexts extends ManageRecords {
 protected static string $resource = SiteTextsResource::class;
 protected function getHeaderActions(): array { return []; }
}
