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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();

            // Relasi ke User (Pemilik)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            $table->string('license_plate')->unique(); // No. Polisi
            $table->string('brand'); // Merk (e.g., Honda)
            $table->string('model'); // Model (e.g., Vario 150)
            $table->string('year')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
