<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Test data lang ang laman ng mga table na ito kaya muling binubuo.
        // Mauna ang mga table na may foreign key.
        Schema::dropIfExists('appointments');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('staff');

        Schema::create('salons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->timestamps();
        });

        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('salon_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('role');
            $table->string('phone')->nullable();
            $table->timestamps();
        });

        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('salon_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('plan_document_id');
            $table->string('plan_name');
            $table->decimal('price', 8, 2);
            $table->unsignedInteger('max_staff')->nullable();
            $table->unsignedInteger('max_monthly_appointments')->nullable();
            $table->string('salon_name');
            $table->string('status')->default('active');
            $table->string('reference_no')->unique();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->dateTime('cancelled_at')->nullable();
            $table->timestamps();
        });

        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('salon_id')->constrained()->cascadeOnDelete();
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
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('staff');
        Schema::dropIfExists('salons');
    }
};