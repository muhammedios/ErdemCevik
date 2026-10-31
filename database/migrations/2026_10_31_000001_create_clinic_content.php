<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::table('users', fn (Blueprint $table) => $table->boolean('is_admin')->default(false));
  Schema::create('contents', function (Blueprint $table) {
   $table->id(); $table->string('kind')->index(); $table->string('slug');
   $table->json('title'); $table->json('excerpt')->nullable(); $table->json('body')->nullable();
   $table->string('image')->nullable(); $table->json('seo_title')->nullable(); $table->json('seo_description')->nullable();
   $table->boolean('published')->default(false); $table->unsignedInteger('sort_order')->default(0); $table->timestamps();
   $table->unique(['kind', 'slug']);
  });
  Schema::create('clinic_settings', function (Blueprint $table) {
   $table->id(); $table->string('name')->default('Erdem Çevik'); $table->string('email')->nullable();
   $table->string('phone')->nullable(); $table->string('whatsapp')->nullable();
   $table->json('address')->nullable(); $table->json('hours')->nullable();
   $table->string('instagram')->nullable(); $table->string('map_url')->nullable();
   $table->string('logo')->nullable(); $table->timestamps();
  });
 }
 public function down(): void {
  Schema::dropIfExists('clinic_settings'); Schema::dropIfExists('contents');
  Schema::table('users', fn (Blueprint $table) => $table->dropColumn('is_admin'));
 }
};
