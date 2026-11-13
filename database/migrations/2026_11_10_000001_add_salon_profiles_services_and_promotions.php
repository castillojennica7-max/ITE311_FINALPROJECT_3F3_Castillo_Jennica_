<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('salons', function (Blueprint $table) {
            $table->string('tagline')->nullable();
            $table->text('about')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('cover_path')->nullable();
            $table->text('opening_hours')->nullable(); // JSON
        });

        Schema::create('salon_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('salon_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('category')->default('other');
            $table->text('description')->nullable();
            $table->decimal('price', 8, 2);
            $table->unsignedInteger('duration_minutes')->default(30);
            $table->string('image_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('salon_promotions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('salon_id')->constrained()->cascadeOnDelete();
            $table->string('type')->default('promo'); // promo | bundle
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('discount_percent')->nullable();
            $table->decimal('bundle_price', 8, 2)->nullable();
            $table->text('included_services')->nullable(); // isang linya bawat serbisyo
            $table->date('valid_until')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salon_promotions');
        Schema::dropIfExists('salon_services');

        Schema::table('salons', function (Blueprint $table) {
            $table->dropColumn(['tagline', 'about', 'logo_path', 'cover_path', 'opening_hours']);
        });
    }
};
