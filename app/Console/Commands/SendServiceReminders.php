<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Vehicle;
use App\Services\WaService; // Service WA kita
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SendServiceReminders extends Command
{
    /**
     * Nama dan "tanda tangan" (signature) dari perintah konsol.
     * Kita akan memanggilnya dengan: php artisan app:send-service-reminders
     */
    protected $signature = 'app:send-service-reminders';

    /**
     * Deskripsi perintah konsol.
     */
    protected $description = 'Cek database dan kirim pengingat servis via WA ke pelanggan';

    /**
     * Jalankan logika perintah.
     */
    public function handle(WaService $waService)
    {
        $this->info('Mulai menjalankan Pengecek Pengingat Servis...');
        Log::info('Job Pengingat Servis Dimulai.');

        // Tentukan batas waktu (misal: 60 hari yang lalu)
        // $reminderThreshold = Carbon::now()->subDays(60);
        $reminderThreshold = Carbon::now()->subMinutes(1);
        $count = 0;

        // Ambil SEMUA kendaraan, beserta relasi pelanggan dan servis terakhirnya
        // Eager loading sangat penting di sini
        $vehicles = Vehicle::with(['owner', 'latestService'])->get();
        $this->info($vehicles->count());


        foreach ($vehicles as $vehicle) {

            // Cek 1: Apakah kendaraan ini punya pelanggan?
            if (!$vehicle->owner) {
                continue; // Lewati jika tidak ada data pelanggan
            }

            // Cek 2: Apakah kendaraan ini pernah servis?
            if ($vehicle->latestService) {
                $lastServiceDate = $vehicle->latestService->service_date;

                // Cek 3: Apakah servis terakhirnya sudah LEBIH DARI 60 hari lalu?
                if ($lastServiceDate < $reminderThreshold) {

                    // Cek 4: (Anti-Spam) Apakah kita BELUM PERNAH kirim pengingat,
                    // ATAU pengingat terakhir kita sudah LEBIH DARI 60 hari lalu?
                    if ($vehicle->last_reminder_sent_at == null || $vehicle->last_reminder_sent_at < $reminderThreshold) {

                        // --- SEMUA SYARAT TERPENUHI, KIRIM WA! ---

                        $message = "Halo " . $vehicle->owner->name . ",\n\n" .
                            "Kami dari " . config('app.name') . " ingin mengingatkan.\n" .
                            "Kendaraan Anda (" . $vehicle->license_plate . ") terakhir kali servis pada " . $lastServiceDate->format('d M Y') . ".\n\n" .
                            "Sudah waktunya untuk servis rutin. Ayo booking jadwal Anda sekarang!\n\n" .
                            "Terima kasih.";

                        try {
                            $waService->sendMessage($vehicle->owner->phone, $message);

                            // UPDATE DATABASE agar tidak kirim spam
                            $vehicle->last_reminder_sent_at = Carbon::now();
                            $vehicle->save();

                            $this->info('Mengirim pengingat ke: ' . $vehicle->owner->name);
                            $count++;
                        } catch (\Exception $e) {
                            $this->error('Gagal mengirim ke: ' . $vehicle->owner->name);
                            Log::error('Gagal kirim pengingat: ' . $e->getMessage());
                        }
                    }
                }
            }
        }

        $this->info("Selesai. Total $count pengingat terkirim.");
        Log::info("Job Pengingat Servis Selesai. Total terkirim: $count.");
        return 0;
    }
}
