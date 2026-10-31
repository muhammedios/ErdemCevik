<?php
namespace App\Filament\Resources\Pages\Pages;
use App\Filament\Resources\Pages\PagesResource;
use Filament\Resources\Pages\ManageRecords;
use Filament\Actions\CreateAction;
class ManagePages extends ManageRecords {
 protected static string $resource = PagesResource::class;
 protected function getHeaderActions(): array { return [CreateAction::make()->modalWidth('5xl')->mutateDataUsing(function(array $data): array { $data['kind'] = 'page'; return $data; })]; }
}
