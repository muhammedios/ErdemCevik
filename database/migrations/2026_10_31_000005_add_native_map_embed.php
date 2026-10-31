<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{Schema,DB};
return new class extends Migration {
 public function up(): void {
  Schema::table('clinic_settings', fn(Blueprint $table) => $table->text('map_embed_url')->nullable());
  DB::table('clinic_settings')->where('id',1)->where('map_url','https://share.google/IrvIzsdhPTohB9MxH')->update(['map_embed_url'=>'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3002.6102067385277!2d28.73626137611445!3d41.18666830842135!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14caabdd14e19761%3A0xe10353e29e09771d!2sDi%C5%9Fhekimi%20Erdem%20%C3%87evik!5e0!3m2!1str!2str!4v1793467222179!5m2!1str!2str']);
 }
 public function down(): void { Schema::table('clinic_settings', fn(Blueprint $table) => $table->dropColumn('map_embed_url')); }
};
