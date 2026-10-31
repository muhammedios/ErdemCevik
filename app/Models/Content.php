<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
class Content extends Model {
 protected $guarded = [];
 protected function casts(): array { return ['title'=>'array','excerpt'=>'array','body'=>'array','seo_title'=>'array','seo_description'=>'array','published'=>'boolean']; }
 public function translated(string $field, ?string $locale = null): string { return (string) (($this->{$field} ?? [])[$locale ?? app()->getLocale()] ?? ''); }
 public function scopeVisible(Builder $query): Builder { return $query->where('published', true)->orderBy('sort_order')->orderBy('id'); }
 public function imageUrl(): ?string { return $this->image ? (str_starts_with($this->image, 'images/') ? asset($this->image) : asset('storage/'.$this->image)) : null; }
}
