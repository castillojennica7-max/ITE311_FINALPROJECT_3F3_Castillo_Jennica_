<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->string('plan_document_id');
    $table->string('plan_name');
    $table->decimal('price', 8, 2);
    $table->string('salon_name');
    $table->string('status')->default('active');
    $table->string('reference_no')->unique();
    $table->dateTime('starts_at');
    $table->dateTime('ends_at');
    $table->dateTime('cancelled_at')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
