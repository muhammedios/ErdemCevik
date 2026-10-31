<?php
namespace App\Filament\Resources\Faqs\Pages;
use App\Filament\Resources\Faqs\FaqsResource;
use Filament\Resources\Pages\ManageRecords;
use Filament\Actions\CreateAction;
class ManageFaqs extends ManageRecords {
 protected static string $resource = FaqsResource::class;
 protected function getHeaderActions(): array { return [CreateAction::make()->modalWidth('5xl')->mutateDataUsing(function(array $data): array { $data['kind'] = 'faq'; return $data; })]; }
}
