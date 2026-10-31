<?php
namespace App\Filament\Resources\Doctors\Pages;
use App\Filament\Resources\Doctors\DoctorsResource;
use Filament\Resources\Pages\ManageRecords;
use Filament\Actions\CreateAction;
class ManageDoctors extends ManageRecords {
 protected static string $resource = DoctorsResource::class;
 protected function getHeaderActions(): array { return [CreateAction::make()->modalWidth('5xl')->mutateDataUsing(function(array $data): array { $data['kind'] = 'doctor'; return $data; })]; }
}
