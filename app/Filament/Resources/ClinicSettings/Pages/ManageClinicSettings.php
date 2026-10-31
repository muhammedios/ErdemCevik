<?php
namespace App\Filament\Resources\ClinicSettings\Pages;
use App\Filament\Resources\ClinicSettings\ClinicSettingResource;
use Filament\Resources\Pages\ManageRecords;
class ManageClinicSettings extends ManageRecords {
 protected static string $resource = ClinicSettingResource::class;
 protected function getHeaderActions(): array { return []; }
}
