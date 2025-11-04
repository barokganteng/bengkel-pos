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
        Schema::create('service_details', function (Blueprint $table) {
            $table->id();

            // Relasi ke Transaksi
            $table->foreignId('service_history_id')->constrained('service_histories')->onDelete('cascade');

            // Relasi Polimorfik
            // $table->morphs('itemable');
            // ATAU lebih eksplisit:
            $table->unsignedBigInteger('itemable_id');
            $table->string('itemable_type'); // (Akan berisi 'App\Models\Service' atau 'App\Models\Sparepart')

            $table->integer('quantity');
            $table->unsignedBigInteger('price_at_transaction'); // Menyimpan harga saat itu
            $table->timestamps();

            // Index untuk polimorfik
            $table->index(['itemable_id', 'itemable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_details');
    }
};
