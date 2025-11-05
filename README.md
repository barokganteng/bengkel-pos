Tentu, ini adalah draf `README.md` yang merangkum semua yang telah kita bangun. Ini dirancang agar jelas dan profesional untuk repositori Git Anda.

---

# Bengkel POS - Sistem Point of Sale Bengkel

**Bengkel POS** adalah aplikasi web modern yang dirancang untuk mengelola operasi harian bengkel (motor/mobil). Aplikasi ini dibangun sebagai proyek monolitik menggunakan _stack_ TALL (versi Blade/Bootstrap), yang membuatnya cepat, reaktif, dan mudah dikelola.

Aplikasi ini mencakup **Panel Admin** yang komprehensif untuk manajemen dan **Halaman Publik** untuk interaksi dengan pelanggan.

## 🚀 Fitur Utama

Proyek ini dibagi menjadi dua bagian utama: Panel Admin (Backend) dan Halaman Publik (Frontend).

### Panel Admin (Backend)

-   **Dashboard Interaktif:**

    -   Kartu statistik (Pendapatan Hari Ini, Total Pelanggan, Booking Pending, Stok Menipis).
    -   Grafik pendapatan 7 hari terakhir (Chart.js).
    -   Grafik 5 jasa servis terlaris (Chart.js).
    -   Tabel 5 transaksi servis terbaru.

-   **Manajemen Transaksi (POS):**

    -   Halaman Kasir untuk membuat transaksi servis baru.
    -   Pencarian _real-time_ untuk pelanggan, jasa, dan sparepart.
    -   Fitur **"Quick Add"**: Tambah pelanggan atau kendaraan baru langsung dari modal tanpa meninggalkan halaman kasir.
    -   Manajemen keranjang (cart) dengan kalkulasi total otomatis.

-   **Riwayat Transaksi:**

    -   Daftar semua transaksi dengan filter (pencarian, status, tanggal).
    -   **Ubah Status Interaktif:** Ubah status transaksi (Pending, In Progress, Done, Paid) langsung dari tabel.
    -   Modal "Detail Transaksi" untuk melihat rincian item.

-   **Manajemen Data Master (CRUD):**

    -   Manajemen Pelanggan
    -   Manajemen Sparepart (termasuk manajemen stok)
    -   Manajemen Jasa Servis

-   **Manajemen Booking:**

    -   Melihat dan mengelola booking yang masuk dari halaman publik.
    -   Mengubah status booking (Pending, Confirmed, Cancelled, Completed).

-   **Manajemen Galeri:**

    -   CRUD untuk galeri foto bengkel, lengkap dengan _preview upload_ file.

### Halaman Publik (Frontend)

-   **Homepage Dinamis:**

    -   _Hero section_ modern dengan gambar latar.
    -   Menampilkan daftar **Jasa Servis** secara dinamis dari database.
    -   Menampilkan **4 Foto Galeri Terbaru** secara dinamis dari database.
    -   **Geolocation:** Peta Google Maps (Embed) lokasi bengkel dan tombol "Buka di Maps".

-   **Halaman Galeri:**

    -   Menampilkan semua foto yang di-upload dari panel admin.

-   **Formulir Booking Online:**

    -   Formulir bagi pelanggan untuk mendaftarkan janji servis.
    -   Sistem cerdas: Otomatis membuat pelanggan/kendaraan baru jika belum ada di database, atau menggunakan data yang sudah ada.

### 🤖 Fitur Otomatisasi (WhatsApp Gateway)

Integrasi penuh dengan WA Gateway (contoh: `kirimi.id`) untuk otomatisasi:

1.  **Nota (Invoice) Otomatis:**

    -   Menggunakan **Laravel Queues (Antrian)**.
    -   Setelah transaksi disimpan, sistem otomatis membuat PDF nota dan mengirimkannya ke nomor WA pelanggan di latar belakang (tidak membuat admin menunggu).

2.  **Bot Pengingat Servis:**

    -   Menggunakan **Laravel Task Scheduling (Cron Job)**.
    -   Setiap hari, sistem otomatis mengecek database dan mengirimkan WA pengingat ke pelanggan yang belum servis lebih dari 60 hari.

## 🛠️ Teknologi yang Digunakan

-   **Backend:** Laravel 11
-   **Frontend:** Laravel Blade + Livewire 3
-   **Styling:** Bootstrap 5 (Integrasi dengan `laravel/ui` dan template admin SB Admin 2)
-   **Database:** MySQL
-   **Fitur Tambahan:**
    -   `barryvdh/laravel-dompdf` untuk generasi PDF.
    -   `kirimi.id` (atau WA Gateway lain) untuk notifikasi.
    -   Chart.js untuk grafik dashboard.
    -   Laravel Queues (Driver: Database) untuk pengiriman nota.
    -   Laravel Task Scheduling untuk bot pengingat.

## ⚙️ Instalasi dan Setup

1.  **Clone repositori:**

    ```bash
    git clone https://github.com/username/bengkel-pos.git
    cd bengkel-pos
    ```

2.  **Instal dependensi:**

    ```bash
    composer install
    npm install
    ```

3.  **Setup Lingkungan (.env):**

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4.  **Konfigurasi file `.env` Anda:**

    ```env
    # 1. Database
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=bengkel_pos
    DB_USERNAME=root
    DB_PASSWORD=

    # 2. URL Aplikasi (PENTING untuk PDF & WA Gateway)
    # Ganti dengan URL Ngrok Anda jika tes di lokal
    APP_URL=http://localhost:8000

    # 3. WA Gateway (kirimi.id)
    KIRIMI_USER_CODE=...
    KIRIMI_SECRET=...
    KIRIMI_DEVICE_ID=...

    # 4. Google Maps
    GMAPS_SHARE_URL=...
    GMAPS_EMBED_URL=...

    # 5. Set Queue Driver ke database
    QUEUE_CONNECTION=database
    ```

5.  **Migrasi dan Seeding Database:**

    -   Buat _symlink_ agar file galeri/nota bisa diakses publik:
        ```bash
        php artisan storage:link
        ```
    -   Jalankan migrasi (termasuk tabel `jobs` untuk antrian) dan _seeder_ (untuk data dummy):
        ```bash
        php artisan migrate:fresh --seed
        ```

6.  **Kompilasi Aset:**

    ```bash
    npm run dev
    ```

## 🚀 Menjalankan Proyek

Untuk menjalankan proyek ini, Anda perlu menjalankan **3 proses** di terminal terpisah:

1.  **Terminal 1: Server Web**

    ```bash
    php artisan serve
    ```

2.  **Terminal 2: Queue Worker (Pekerja Antrian)**

    -   (Wajib untuk mengirim nota WA)

    <!-- end list -->

    ```bash
    php artisan queue:work
    ```

3.  **(Opsional) Ngrok untuk Tes WA Gateway di Lokal:**

    ```bash
    ngrok http 8000
    ```

    _(Jangan lupa update `APP_URL` di `.env` dengan URL Ngrok Anda)_

## 🔑 Akun Admin (Dummy)

Akun admin bawaan dari _seeder_:

-   **Email:** `admin@bengkel.com`
-   **Password:** `password`

---
