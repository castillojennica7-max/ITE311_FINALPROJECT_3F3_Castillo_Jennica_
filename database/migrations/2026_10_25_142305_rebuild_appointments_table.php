<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    // Buburahin ang lumang appointments table (test data lang ang laman)
    Schema::dropIfExists('appointments');

    Schema::create('appointments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->foreignId('staff_id')->nullable()->constrained('staff')->nullOnDelete();
        $table->string('service_document_id');
        $table->string('service_name');
        $table->decimal('service_price', 8, 2);
        $table->unsignedInteger('service_duration_minutes');
        $table->dateTime('appointment_at');
        $table->string('status')->default('pending');
        $table->text('notes')->nullable();
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('appointments');
}
};
