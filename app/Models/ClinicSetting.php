<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ClinicSetting extends Model {
 protected $guarded = [];
 protected function casts(): array { return ['address'=>'array','hours'=>'array','site_texts'=>'array','site_images'=>'array','show_preview_notice'=>'boolean','popup_enabled'=>'boolean','popup_alt'=>'array']; }
 public function translated(string $field): string { return (string) (($this->{$field} ?? [])[app()->getLocale()] ?? ''); }
 public function siteText(string $key, ?string $locale = null): string {
  $locale ??= app()->getLocale();
  $saved = $this->site_texts[$locale] ?? [];
  if (array_key_exists($key, $saved) && $saved[$key] !== null) return (string) $saved[$key];
  foreach (config('site-content', []) as $group) {
   if (isset($group['fields'][$key])) return (string) ($group['fields'][$key]['default'][$locale] ?? '');
  }
  return '';
 }
 public function siteImageUrl(string $key, string $fallback): string {
  $path = $this->site_images[$key] ?? null;
  return $path ? asset('storage/'.$path) : asset($fallback);
 }
 public function mapEmbedUrl(): string {
  if ($this->map_embed_url && str_starts_with($this->map_embed_url, 'https://www.google.com/maps/embed?')) return $this->map_embed_url;
  return 'https://www.google.com/maps?q='.rawurlencode('Dişhekimi '.$this->name.' '.$this->translated('address')).'&output=embed';
 }
 public function whatsappUrl(): ?string {
  if ($this->whatsapp_url && filter_var($this->whatsapp_url,FILTER_VALIDATE_URL) && parse_url($this->whatsapp_url,PHP_URL_SCHEME) === 'https') return $this->whatsapp_url;
  $number = preg_replace('/\D/', '', $this->whatsapp ?? '');
  if (!preg_match('/^[1-9][0-9]{9,14}$/', $number)) return null;
  $message = app()->getLocale() === 'tr' ? 'Merhaba, randevu hakkında bilgi almak istiyorum.' : 'Hello, I would like to enquire about an appointment.';
  return 'https://wa.me/'.$number.'?text='.rawurlencode($message);
 }
}
