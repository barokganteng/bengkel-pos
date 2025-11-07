<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

// 1. --- IMPORT SEMUA YANG KITA BUTUHKAN ---
use App\Models\ServiceHistory;
use App\Models\ServiceDetail;
use App\Services\WaService;      // Service WA kita
use Barryvdh\DomPDF\Facade\Pdf; // Pembuat PDF
use Illuminate\Support\Facades\Storage; // Penyimpanan file
use Illuminate\Support\Facades\Log; // Untuk mencatat error

class SendNotaWaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $serviceHistoryId; // Properti untuk menyimpan ID transaksi

    /**
     * Create a new job instance.
     * Kita akan mengirim ID transaksi ke job ini
     */
    public function __construct(int $serviceHistoryId)
    {
        $this->serviceHistoryId = $serviceHistoryId;
    }

    /**
     * Execute the job.
     * Di sinilah semua keajaiban terjadi (di latar belakang)
     */
    public function handle(): void
    {
        try {
            $waService = new WaService();

            // 1. Ambil semua data transaksi dari database
            $tx = ServiceHistory::with(['customer', 'vehicle', 'mechanic'])
                ->findOrFail($this->serviceHistoryId);

            $details = ServiceDetail::with('itemable')
                ->where('service_history_id', $tx->id)
                ->get();

            // 2. Generate PDF dari view blade yang kita buat
            $pdf = Pdf::loadView('pdf.nota', [
                'tx' => $tx,
                'details' => $details
            ]);

            // 3. Simpan PDF ke folder 'storage/app/public/notas'
            // Pastikan Anda sudah menjalankan "php artisan storage:link"
            $filename = 'notas/nota-' . $tx->id . '-' . time() . '.pdf';
            Storage::disk('public')->put($filename, $pdf->output());

            // 4. Dapatkan URL publik ke file PDF tersebut
            // Penting: APP_URL di .env harus benar!
            $publicUrl = config('app.url') . Storage::url($filename);

            // 5. Siapkan pesan (caption) untuk WA
            $caption = "Terima kasih, " . $tx->customer->name . "!\n\n" .
                "Servis untuk kendaraan " . $tx->vehicle->license_plate . " telah selesai. Berikut kami lampirkan nota digital Anda.\n\n" .
                "Total Tagihan: Rp " . number_format($tx->total_price) . "\n\n" .
                "Terima kasih atas kepercayaan Anda.";

            // 6. Kirim WA menggunakan WaService
            $waService->sendMessage(
                $tx->customer->phone, // Nomor HP pelanggan
                $caption,              // Pesan
                $publicUrl,           // URL ke PDF
            );

            // 7. (Opsional) Catat jika berhasil
            Log::info("Nota WA berhasil terkirim untuk ServiceHistory ID: " . $tx->id);
        } catch (\Exception $e) {
            // 8. Catat jika ada error
            Log::error("Gagal mengirim nota WA untuk ID: " . $this->serviceHistoryId . ". Error: " . $e->getMessage());
            // Beritahu antrian bahwa job ini gagal (agar bisa di-retry)
            $this->fail($e);
        }
    }
}
