<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Fill only missing or initial placeholder values; preserve subsequent panel edits.
        $settings = DB::table('clinic_settings')->where('id', 1)->first();
        if (! $settings) {
            return;
        }
        $values = [];
        foreach (['phone' => '+90 533 717 69 56', 'whatsapp' => '905337176956', 'map_url' => 'https://share.google/IrvIzsdhPTohB9MxH'] as $field => $value) {
            if (empty($settings->{$field})) {
                $values[$field] = $value;
            }
        }
        foreach ([
            'address' => ['tr' => "İslambey Mahallesi, Fatih Caddesi No:62\n34275 Arnavutköy / İstanbul", 'en' => "İslambey Mahallesi, Fatih Caddesi No:62\n34275 Arnavutköy / İstanbul, Türkiye"],
            'hours' => ['tr' => "Pazartesi – Cumartesi: 10:00 – 19:00\nPazar: Kapalı", 'en' => "Monday – Saturday: 10:00 – 19:00\nSunday: Closed"],
        ] as $field => $translations) {
            $current = json_decode($settings->{$field} ?? '{}', true) ?: [];
            foreach ($translations as $locale => $value) {
                if (empty($current[$locale]) || str_contains($current[$locale], 'yakında eklenecek') || str_contains($current[$locale], 'will be added soon')) {
                    $current[$locale] = $value;
                }
            }
            $values[$field] = json_encode($current, JSON_UNESCAPED_UNICODE);
        }
        DB::table('clinic_settings')->where('id', 1)->update($values);
    }

    public function down(): void
    {
        // Contact details are user-editable content; rolling back must not erase them.
    }
};
