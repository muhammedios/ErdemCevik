<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
return new class extends Migration {
    public function up(): void {
        $record = DB::table('clinic_settings')->where('id', 1)->first();
        if (! $record) return;
        $hours = json_decode($record->hours ?? '{}', true) ?: [];
        $days = ['tr' => ['Pazartesi', 'Salı', 'Çarşamba', 'Perşembe', 'Cuma', 'Cumartesi'], 'en' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']];
        $originals = ['tr' => "Pazartesi – Cumartesi: 10:00 – 19:00\nPazar: Kapalı", 'en' => "Monday – Saturday: 10:00 – 19:00\nSunday: Closed"];
        foreach ($days as $locale => $names) {
            if (($hours[$locale] ?? '') === $originals[$locale]) {
                $hours[$locale] = implode("\n", array_map(fn ($day) => $day.': 10:00 – 19:00', $names))."\n".($locale === 'tr' ? 'Pazar: Kapalı' : 'Sunday: Closed');
            }
        }
        DB::table('clinic_settings')->where('id', 1)->update(['hours' => json_encode($hours, JSON_UNESCAPED_UNICODE)]);
    }
    public function down(): void {}
};
