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
        $this->apiUrl = 'https://' . env('WA_SENDER_AUTH_TOKEN') . '@' . env('WA_SENDER_API_URL');
    }

    /**
     * Mengirim pesan teks WhatsApp.
     */
    public function sendMessage(string $to, string $message)
    {
        // Pastikan nomor HP formatnya benar (08... -> 628...)
        $receiver = $this->formatPhoneNumber($to);

        $payload = [
            'phone'  => $receiver,
            'message'   => $message,
        ];

        return Http::asJson()->post($this->apiUrl . '/send/message', $payload);
    }
    // /**
    //  * Mengirim file PDF melalui WhatsApp.
    //  */
    // public function sendDocument(string $to, string $caption, string $file)
    // {
    //     // Pastikan nomor HP formatnya benar (08... -> 628...)
    //     $receiver = $this->formatPhoneNumber($to);

    //     $payload = [
    //         'phone'  => $receiver,
    //         'caption'   => $caption,
    //         'file'  => $file,
    //     ];

    //     return Http::asJson()->post($this->apiUrl . '/send/document', $payload);
    // }

    /**
     * Mengirim file melalui WhatsApp.
     */
    public function sendFile(string $to, string $caption, $fileContents, string $filename, bool $is_forwarded = false, ?int $duration = null)
    {
        $receiver = $this->formatPhoneNumber($to);

        $multipart = [
            [
                'name' => 'phone',
                'contents' => $receiver,
            ],
            [
                'name' => 'caption',
                'contents' => $caption,
            ],
            [
                'name' => 'is_forwarded',
                'contents' => $is_forwarded,
            ],
            [
                'name' => 'file',
                'contents' => $fileContents,
                'filename' => $filename,
            ],
        ];

        if ($duration) {
            $multipart[] = [
                'name' => 'duration',
                'contents' => $duration,
            ];
        }

        return Http::asMultipart()->post($this->apiUrl . '/send/file', $multipart);
    }




    /**
     * Ubah 08... menjadi 628...
     */
    private function formatPhoneNumber($number)
    {
        if (substr($number, 0, 1) === '0') {
            return '62' . substr($number, 1) . '@s.whatsapp.net';
        }
        return $number;
    }
}
