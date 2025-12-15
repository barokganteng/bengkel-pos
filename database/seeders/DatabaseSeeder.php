<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Service;
use App\Models\Sparepart;
use App\Models\ServiceHistory;
use App\Models\ServiceDetail;
use App\Models\Booking;
use App\Models\Gallery;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('Memulai proses seeding...');

        // 1. Truncate semua tabel (PostgreSQL - non-superuser version)
        $this->command->info('Menghapus semua data lama (truncate)...');

        $tables = [
            'service_details', 'service_histories', 'bookings', 'vehicles', 'users',
            'galleries', 'services', 'spareparts', 'jobs', 'failed_jobs', 'job_batches',
            'password_reset_tokens'
        ];

        // Bungkus nama tabel dengan kutip ganda untuk PostgreSQL
        $quotedTables = collect($tables)->map(fn($t) => '"'.$t.'"');

        // Nonaktifkan trigger untuk setiap tabel
        // Ini membutuhkan hak milik (ownership) atas tabel, bukan superuser
        $this->command->info('Menonaktifkan trigger...');
        foreach ($tables as $table) {
             DB::statement("ALTER TABLE \"{$table}\" DISABLE TRIGGER ALL;");
        }

        // Truncate semua tabel sekaligus
        $this->command->info('Melakukan truncate...');
        DB::statement('TRUNCATE TABLE ' . $quotedTables->implode(', ') . ' RESTART IDENTITY CASCADE;');

        // Aktifkan kembali trigger
        $this->command->info('Mengaktifkan kembali trigger...');
        foreach ($tables as $table) {
            DB::statement("ALTER TABLE \"{$table}\" ENABLE TRIGGER ALL;");
        }

        $this->command->info('Tabel berhasil di-truncate.');

        // 2. Buat Admin Utama
        $this->command->info('Membuat Admin...');
        User::create([
            'name' => 'Admin Bengkel',
            'email' => 'admin@bengkel.com',
            'password' => Hash::make('password'), // password: "password"
            'phone' => '081234567890',
            'role' => 'admin'
        ]);

        // 3. Buat Master Data
        $this->command->info('Membuat Data Master (Jasa, Sparepart, Mekanik)...');
        $services = Service::factory(10)->create();
        $spareparts = Sparepart::factory(20)->create();
        $mechanics = User::factory(4)->mekanik()->create();
        $allVehicles = collect(); // Koleksi untuk menampung semua kendaraan

        // 4. Buat Pelanggan dan Kendaraan mereka
        $this->command->info('Membuat Pelanggan dan Kendaraan...');
        $customers = User::factory(50)->create()->each(function ($customer) use ($allVehicles) {
            $vehicles = Vehicle::factory(rand(1, 2))->create([
                'user_id' => $customer->id // Link kendaraan ke pelanggan
            ]);
            $allVehicles->push(...$vehicles); // Tambahkan kendaraan ke koleksi
        });

        // 5. Buat Booking & Galeri Dummy
        $this->command->info('Membuat Booking dan Galeri...');
        Booking::factory(15)->create([
            'customer_id' => $customers->random()->id,
            'vehicle_id' => $allVehicles->random()->id,
        ]);
        Gallery::factory(8)->create();

        // 6. BUAT TRANSAKSI DUMMY (BAGIAN UTAMA)
        $this->command->info('Membuat 150 Transaksi Servis (ini mungkin perlu waktu)...');
        $progressBar = $this->command->getOutput()->createProgressBar(150);

        for ($i = 0; $i < 150; $i++) {
            $customer = $customers->random();
            $vehicle = $customer->vehicles->random(); // Pastikan kendaraan milik pelanggan
            $mechanic = $mechanics->random();
            // Tanggal acak dalam 90 hari terakhir (penting untuk grafik dashboard)
            $serviceDate = Carbon::now()->subDays(rand(0, 90))->subHours(rand(1, 24));

            // Buat transaksi induk
            $tx = ServiceHistory::create([
                'customer_id' => $customer->id,
                'vehicle_id' => $vehicle->id,
                'mechanic_id' => $mechanic->id,
                'service_date' => $serviceDate,
                'status' => ['pending', 'in_progress', 'done', 'paid'][rand(0, 3)],
                'total_price' => 0, // Akan kita hitung dan update
                'notes' => fake()->boolean(25) ? fake()->sentence() : null, // 25% chance of notes
            ]);

            // Simulasikan bot pengingat sudah berjalan
            if ($serviceDate < Carbon::now()->subDays(60) && fake()->boolean()) {
                $vehicle->last_reminder_sent_at = $serviceDate->addDays(rand(60, 65));
                $vehicle->save();
            }

            $total = 0;
            $itemCount = rand(1, 4); // Setiap transaksi punya 1-4 item

            // Buat detail transaksi
            for ($j = 0; $j < $itemCount; $j++) {
                if (fake()->boolean(70)) { // 70% chance add Service
                    $item = $services->random();
                    $qty = 1;
                    $price = $item->price;
                    $type = Service::class;
                } else { // 30% chance add Sparepart
                    $item = $spareparts->random();
                    $qty = rand(1, 2);
                    $price = $item->sale_price;
                    $type = Sparepart::class;
                }

                ServiceDetail::create([
                    'service_history_id' => $tx->id,
                    'itemable_id' => $item->id,
                    'itemable_type' => $type,
                    'quantity' => $qty,
                    'price_at_transaction' => $price,
                ]);

                $total += ($price * $qty);
            }

            // Update total harga di transaksi induk
            $tx->total_price = $total;
            $tx->save();

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->command->info("\n✅ Seeding semua data dummy berhasil diselesaikan.");
    }
}
