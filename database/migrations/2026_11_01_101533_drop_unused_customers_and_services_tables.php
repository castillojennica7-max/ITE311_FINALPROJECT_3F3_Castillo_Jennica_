<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Napalitan na ang mga ito: ang customers ay mga user na ngayon, at ang services ay nasa Strapi
    public function up(): void
    {
        Schema::dropIfExists('customers');
        Schema::dropIfExists('services');
    }

    public function down(): void
    {
        // Hindi na ibinabalik
    }
};