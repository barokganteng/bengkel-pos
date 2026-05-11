<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WaService
{
    protected $apiUrl = 'https://api.fonnte.com/send';

    protected $token;

    public function __construct()
    {
        // Ambil data rahasia dari file .env Anda
        $this->token = env('FONNTE_TOKEN');
    }

    /**
     * Mengirim pesan teks WhatsApp.
     */
    public function sendMessage(string $to, string $message)
    {
        // Pastikan nomor HP formatnya benar (08... -> 628...)
        $receiver = $this->formatPhoneNumber($to);

        $payload = [
            'target' => $receiver,
            'message' => $message,
        ];

        return Http::withHeaders([
            'Authorization' => $this->token,
        ])
            ->post($this->apiUrl, $payload);
    }

    /**
     * Ubah 08... menjadi 628...
     */
    private function formatPhoneNumber($number)
    {
        if (substr($number, 0, 1) === '0') {
            return '62'.substr($number, 1).'@s.whatsapp.net';
        }

        return $number;
    }
}
