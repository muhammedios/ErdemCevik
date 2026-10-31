<?php
namespace App\Filament\Widgets;
use App\Models\Content;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
class ContentOverview extends StatsOverviewWidget {
 protected function getStats(): array {
  return [Stat::make('Doktorlar',Content::where('kind','doctor')->count()),Stat::make('Tedaviler',Content::where('kind','treatment')->count()),Stat::make('Blog yazıları',Content::where('kind','post')->count()),Stat::make('Sorular',Content::where('kind','faq')->count())];
 }
}
