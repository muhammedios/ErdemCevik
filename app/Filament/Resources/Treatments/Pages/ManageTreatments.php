<?php
namespace App\Filament\Resources\Treatments\Pages;
use App\Filament\Resources\Treatments\TreatmentsResource;
use Filament\Resources\Pages\ManageRecords;
use Filament\Actions\CreateAction;
class ManageTreatments extends ManageRecords {
 protected static string $resource = TreatmentsResource::class;
 protected function getHeaderActions(): array { return [CreateAction::make()->modalWidth('5xl')->mutateDataUsing(function(array $data): array { $data['kind'] = 'treatment'; return $data; })]; }
}
