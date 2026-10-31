<?php
namespace App\Filament\Resources\Posts\Pages;
use App\Filament\Resources\Posts\PostsResource;
use Filament\Resources\Pages\ManageRecords;
use Filament\Actions\CreateAction;
class ManagePosts extends ManageRecords {
 protected static string $resource = PostsResource::class;
 protected function getHeaderActions(): array { return [CreateAction::make()->modalWidth('5xl')->mutateDataUsing(function(array $data): array { $data['kind'] = 'post'; return $data; })]; }
}
