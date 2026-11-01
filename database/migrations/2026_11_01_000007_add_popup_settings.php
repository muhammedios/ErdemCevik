<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void { Schema::table('clinic_settings', function (Blueprint $table) {
  $table->boolean('popup_enabled')->default(false);
  $table->string('popup_image')->nullable();
  $table->string('popup_url', 1000)->nullable();
  $table->json('popup_alt')->nullable();
 }); }
 public function down(): void { Schema::table('clinic_settings', fn (Blueprint $table) => $table->dropColumn(['popup_enabled','popup_image','popup_url','popup_alt'])); }
};
