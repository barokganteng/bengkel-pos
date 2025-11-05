<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WaService
{
    protected $apiUrl;
    protected $userCode;
    protected $secret;
    protected $deviceId;

    public function __construct()
    {
        // Ambil data rahasia dari file .env Anda
        $this->apiUrl = 'https://api.kirimi.id/v1'; // Sesuai dokumentasi mereka
        $this->userCode = env('KIRIMI_USER_CODE');
        $this->secret = env('KIRIMI_SECRET');
        $this->deviceId = env('KIRIMI_DEVICE_ID');
    }

    /**
     * Mengirim pesan teks WhatsApp.
     */
    public function sendText(string $to, string $message)
    {
        // Pastikan nomor HP formatnya benar (08... -> 628...)
        $receiver = $this->formatPhoneNumber($to);

        $payload = [
            'user_code' => $this->userCode,
            'secret'    => $this->secret,
            'device_id' => $this->deviceId,
            'receiver'  => $receiver,
            'message'   => $message,
        ];

        return Http::asJson()->post($this->apiUrl . '/send-text', $payload);
    }

    /**
     * Mengirim file PDF (untuk Nota).
     * kirimi.id meminta URL, jadi PDF kita harus bisa diakses publik.
     */
    public function sendDocument(string $to, string $fileUrl, string $caption = '')
    {
        $receiver = $this->formatPhoneNumber($to);

        $payload = [
            'user_code' => $this->userCode,
            'secret'    => $this->secret,
            'device_id' => $this->deviceId,
            'receiver'  => $receiver,
            'media'     => $fileUrl, // URL ke PDF
            'message'   => $caption, // Caption untuk PDF
        ];

        return Http::asJson()->post($this->apiUrl . '/send-document', $payload);
    }

    /**
     * Ubah 08... menjadi 628...
     */
    private function formatPhoneNumber($number)
    {
        if (substr($number, 0, 1) === '0') {
            return '62' . substr($number, 1);
        }
        return $number;
    }
}
