<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::table('clinic_settings', function(Blueprint $table) {
   $table->string('facebook',500)->nullable();
   $table->string('whatsapp_url',500)->nullable();
  });
 }
 public function down(): void { Schema::table('clinic_settings', fn(Blueprint $table) => $table->dropColumn(['facebook','whatsapp_url'])); }
};
