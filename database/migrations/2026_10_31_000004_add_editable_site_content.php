<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::table('clinic_settings', function (Blueprint $table) {
   $table->json('site_texts')->nullable();
   $table->json('site_images')->nullable();
   $table->boolean('show_preview_notice')->default(true);
  });
 }
 public function down(): void {
  Schema::table('clinic_settings', fn(Blueprint $table) => $table->dropColumn(['site_texts','site_images','show_preview_notice']));
 }
};
