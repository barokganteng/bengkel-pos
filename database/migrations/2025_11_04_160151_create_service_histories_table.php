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
        Schema::create('service_histories', function (Blueprint $table) {
            $table->id();

            // Relasi ke Pelanggan (User)
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');

            // Relasi ke Mekanik (User)
            $table->foreignId('mechanic_id')->nullable()->constrained('users')->onDelete('set null');

            // Relasi ke Kendaraan
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');

            $table->unsignedBigInteger('total_price')->default(0);
            $table->enum('status', ['pending', 'in_progress', 'done', 'paid'])->default('pending');
            $table->timestamp('service_date')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_histories');
    }
};
