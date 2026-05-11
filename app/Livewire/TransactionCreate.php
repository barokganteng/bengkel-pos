<?php

namespace App\Livewire;

use App\Jobs\SendNotaWaJob;
use App\Models\Booking; // Model User (untuk Pelanggan & Mekanik)
use App\Models\Service; // Model Kendaraan
use App\Models\ServiceDetail; // Model Jasa Servis
use App\Models\ServiceHistory; // Model Sparepart
use App\Models\Sparepart; // Model Booking
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Component;

class TransactionCreate extends Component
{
    // State tahap UI: true jika customer/kendaraan sudah dimasukkan ke antrean
    public $is_queue_stage_done = false;

    // Menyimpan ID draft antrean yang sudah tersimpan di database
    public $queued_service_history_id = null;

    // Properti Modal Pelanggan Baru
    public $isNewCustomerModalOpen = false;

    public $new_name;

    public $new_email;

    public $new_phone;

    public $new_password = 'password'; // Password default untuk pelanggan baru

    // Properti Modal Kendaraan Baru
    public $isNewVehicleModalOpen = false;

    public $new_license_plate;

    public $new_brand;

    public $new_model;

    public $new_year;

    // Bagian Pelanggan & Kendaraan
    public $customer_search = '';

    public $customers = []; // Hasil pencarian pelanggan

    public $selected_customer_id;

    public $selected_customer_name;

    public $selected_vehicle_id;

    public $vehicles = []; // Kendaraan milik pelanggan

    // Bagian Mekanik
    public $mechanics = [];

    public $selected_mechanic_id;

    // Bagian Item
    public $item_search = '';

    public $item_results = [];

    // Bagian Keranjang
    public $cart = [];

    public $total = 0;

    public $notes = '';

    /**
     * Dijalankan saat komponen dimuat pertama kali
     */
    public function mount()
    {
        // Langsung muat daftar mekanik
        $this->mechanics = User::mechanic()->get();

        if (request()->has('queue_id')) {
            $this->loadQueuedServiceHistory(request('queue_id'));

            return;
        }

        // Cek apakah ada parameter 'booking_id' dari URL
        if (request()->has('booking_id')) {
            $this->loadBookingData(request('booking_id'));
        }
    }

    public function loadQueuedServiceHistory($queueId)
    {
        $queue = ServiceHistory::with(['customer', 'vehicle'])
            ->where('id', $queueId)
            ->whereIn('status', ['pending', 'in_progress'])
            ->first();

        if (! $queue) {
            session()->flash('error', 'Data antrean tidak ditemukan atau sudah masuk tahap transaksi/pembayaran.');

            return;
        }

        $this->queued_service_history_id = $queue->id;
        $this->selected_customer_id = $queue->customer_id;
        $this->selected_customer_name = $queue->customer?->name;
        $this->selected_vehicle_id = $queue->vehicle_id;
        $this->notes = $queue->notes;
        $this->is_queue_stage_done = true;

        $customer = User::find($queue->customer_id);
        $this->vehicles = $customer ? $customer->vehicles : [];
    }

    public function loadBookingData($bookingId)
    {
        $booking = Booking::find($bookingId);

        if ($booking) {
            $customer = User::findOrFail($booking->customer_id);
            // 1. Isi Data Pelanggan (sesuaikan nama variabel property Anda, misal $customer_id atau $selectedUser)
            $this->selected_customer_id = $customer->id;
            $this->selected_customer_name = $customer->name;

            // Trigger update jika ada logic livewire (updatedCustomerId)
            // $this->updatedCustomerId();

            $vehicles = $customer->vehicles;
            // 2. Isi Data Kendaraan
            $this->vehicles = $vehicles;
            $this->selected_vehicle_id = $booking->vehicle_id;

            // Booking yang dimuat tetap harus dikonfirmasi antreannya dari UI
            $this->is_queue_stage_done = false;
            $this->queued_service_history_id = null;

        }
    }

    public function enterQueue()
    {
        if (! $this->selected_customer_id || ! $this->selected_vehicle_id) {
            session()->flash('error', 'Pilih pelanggan dan kendaraan terlebih dahulu untuk masuk antrean.');

            return;
        }

        if ($this->queued_service_history_id) {
            $existingQueue = ServiceHistory::find($this->queued_service_history_id);
            if ($existingQueue) {
                $this->is_queue_stage_done = true;
                session()->flash('message', 'Data antrean sudah tersimpan sebelumnya.');

                return;
            }
        }

        $queue = ServiceHistory::create([
            'customer_id' => $this->selected_customer_id,
            'vehicle_id' => $this->selected_vehicle_id,
            'mechanic_id' => null,
            'total_price' => 0,
            'status' => 'pending',
            'service_date' => now(),
            'notes' => $this->notes,
        ]);

        $this->queued_service_history_id = $queue->id;
        $this->is_queue_stage_done = true;
        session()->flash('message', 'Data customer sudah dimasukkan ke antrean dan tersimpan di database.');
    }

    public function resetQueueStage()
    {
        $this->discardQueuedDraft();
        $this->is_queue_stage_done = false;
        $this->item_search = '';
        $this->item_results = [];
        $this->cart = [];
        $this->total = 0;
        $this->selected_mechanic_id = null;
    }

    private function discardQueuedDraft(): void
    {
        if (! $this->queued_service_history_id) {
            return;
        }

        $draft = ServiceHistory::withCount('details')->find($this->queued_service_history_id);
        if ($draft && $draft->status === 'pending' && (int) $draft->total_price === 0 && $draft->details_count === 0) {
            $draft->delete();
        }

        $this->queued_service_history_id = null;
    }

    /**
     * Membuka modal untuk menambah pelanggan baru
     */
    public function openNewCustomerModal()
    {
        $this->resetErrorBag(); // Bersihkan error validasi lama
        $this->reset(['new_name', 'new_email', 'new_phone', 'new_password']);
        $this->new_password = 'password'; // Set default
        $this->isNewCustomerModalOpen = true;
    }

    /**
     * Menyimpan pelanggan baru dari modal
     */
    public function saveNewCustomer()
    {
        // Validasi data pelanggan baru
        $validated = $this->validate([
            'new_name' => 'required|string|min:3',
            'new_email' => 'nullable|email|unique:users,email',
            'new_phone' => 'nullable|string',
            'new_password' => 'required|min:6',
        ]);

        $customer = User::create([
            'name' => $this->new_name,
            'email' => $this->new_email,
            'phone' => $this->new_phone,
            'password' => Hash::make($this->new_password),
            'role' => 'pelanggan',
        ]);

        // Tutup modal
        $this->isNewCustomerModalOpen = false;

        // LANGSUNG PILIH PELANGGAN YANG BARU DIBUAT
        $this->selectCustomer($customer->id);

        session()->flash('message', 'Pelanggan baru berhasil ditambahkan dan dipilih.');
    }

    /**
     * Membuka modal untuk menambah kendaraan baru
     * (Hanya bisa dipanggil jika pelanggan sudah dipilih)
     */
    public function openNewVehicleModal()
    {
        if (! $this->selected_customer_id) {
            session()->flash('error', 'Pilih pelanggan terlebih dahulu.');

            return;
        }
        $this->resetErrorBag();
        $this->reset(['new_license_plate', 'new_brand', 'new_model', 'new_year']);
        $this->isNewVehicleModalOpen = true;
    }

    /**
     * Menyimpan kendaraan baru dari modal
     */
    public function saveNewVehicle()
    {
        // Validasi data kendaraan baru
        $validated = $this->validate([
            'new_license_plate' => 'required|string|unique:vehicles,license_plate',
            'new_brand' => 'required|string',
            'new_model' => 'required|string',
            'new_year' => 'nullable|numeric|digits:4|min:1990',
        ]);

        $vehicle = Vehicle::create([
            'user_id' => $this->selected_customer_id, // Link ke pelanggan yang dipilih
            'license_plate' => strtoupper($this->new_license_plate),
            'brand' => $this->new_brand,
            'model' => $this->new_model,
            'year' => $this->new_year,
        ]);

        // Tutup modal
        $this->isNewVehicleModalOpen = false;

        // Refresh daftar kendaraan untuk pelanggan ini
        $this->vehicles = User::find($this->selected_customer_id)->vehicles;

        // LANGSUNG PILIH KENDARAAN YANG BARU DIBUAT
        $this->selected_vehicle_id = $vehicle->id;

        session()->flash('message', 'Kendaraan baru berhasil ditambahkan dan dipilih.');
    }

    /**
     * Dijalankan setiap kali properti $customer_search berubah
     */
    public function updatedCustomerSearch($value)
    {
        if (strlen($value) < 2) {
            $this->customers = []; // Kosongkan hasil jika input < 2 huruf

            return;
        }

        // Cari pelanggan
        $this->customers = User::customer()
            ->where(function ($query) use ($value) {
                $query->where('name', 'like', "%{$value}%")
                    ->orWhere('email', 'like', "%{$value}%")
                    ->orWhere('phone', 'like', "%{$value}%");
            })
            ->take(5) // Ambil 5 hasil teratas
            ->get();
    }

    /**
     * Dijalankan saat pelanggan dipilih dari hasil pencarian
     */
    public function selectCustomer($id)
    {
        $customer = User::findOrFail($id);
        $this->selected_customer_id = $customer->id;
        $this->selected_customer_name = $customer->name;
        $this->resetQueueStage();

        // Muat kendaraan milik pelanggan
        $this->vehicles = $customer->vehicles;

        // Reset pencarian
        $this->customer_search = '';
        $this->customers = [];
    }

    /**
     * Dijalankan saat tombol "Ganti" pelanggan diklik
     */
    public function resetCustomer()
    {
        $this->resetQueueStage();
        $this->selected_customer_id = null;
        $this->selected_customer_name = null;
        $this->selected_vehicle_id = null;
        $this->vehicles = [];
    }

    public function updatedSelectedVehicleId($value)
    {
        // Jika kendaraan diganti setelah antrean aktif, paksa antrean dikonfirmasi ulang.
        if (! empty($value)) {
            $this->resetQueueStage();
        }
    }

    /**
     * Dijalankan setiap kali properti $item_search berubah
     */
    public function updatedItemSearch($value)
    {
        if (strlen($value) < 2) {
            $this->item_results = [];

            return;
        }

        $this->item_results = [];

        // 1. Cari Jasa Servis
        $services = Service::where('name', 'like', "%{$value}%")->get();
        foreach ($services as $service) {
            $this->item_results[] = [
                'id' => $service->id,
                'name' => $service->name,
                'price' => $service->price,
                'type' => 'service',
                'stock' => null, // Jasa tidak punya stok
            ];
        }

        // 2. Cari Sparepart
        $spareparts = Sparepart::where('name', 'like', "%{$value}%")
            ->orWhere('sku', 'like', "%{$value}%")
            ->get();
        foreach ($spareparts as $sparepart) {
            $this->item_results[] = [
                'id' => $sparepart->id,
                'name' => $sparepart->name,
                'price' => $sparepart->sale_price,
                'type' => 'sparepart',
                'stock' => $sparepart->stock,
            ];
        }
    }

    /**
     * Dijalankan saat item (jasa/sparepart) dipilih dari hasil pencarian
     */
    public function addItemToCart($type, $id)
    {
        if (! $this->is_queue_stage_done) {
            session()->flash('error', 'Masukkan customer ke antrean terlebih dahulu sebelum menambah item.');

            return;
        }

        // Cek duplikat
        foreach ($this->cart as $key => $item) {
            if ($item['id'] == $id && $item['type'] == $type) {
                // Jika item adalah sparepart, cek stok dan tambahkan kuantitas
                if ($type == 'sparepart') {
                    $sparepart = Sparepart::find($id);
                    if ($sparepart->stock > $this->cart[$key]['quantity']) {
                        $this->cart[$key]['quantity']++;
                        $this->calculateTotal();
                        $this->resetItemSearch();

                        return;
                    } else {
                        session()->flash('error', 'Stok sparepart tidak mencukupi.');

                        return;
                    }
                } else {
                    // Jika jasa, jangan tambahkan (karena jasa tidak perlu kuantitas > 1)
                    session()->flash('error', 'Jasa servis sudah ada di keranjang.');

                    return;
                }
            }
        }

        // Jika tidak duplikat, tambahkan item baru ke keranjang
        if ($type == 'service') {
            $item = Service::find($id);
            $this->cart[] = [
                'id' => $item->id,
                'name' => $item->name,
                'price' => $item->price,
                'quantity' => 1,
                'type' => 'service',
            ];
        } else { // type == 'sparepart'
            $item = Sparepart::find($id);
            if ($item->stock < 1) {
                session()->flash('error', 'Stok sparepart habis.');

                return;
            }
            $this->cart[] = [
                'id' => $item->id,
                'name' => $item->name,
                'price' => $item->sale_price,
                'quantity' => 1,
                'type' => 'sparepart',
            ];
        }

        $this->calculateTotal();
        $this->resetItemSearch();
    }

    /**
     * Menghapus item dari keranjang
     */
    public function removeItemFromCart($key)
    {
        unset($this->cart[$key]);
        $this->cart = array_values($this->cart); // Re-index array
        $this->calculateTotal();
    }

    /**
     * Menghitung ulang Grand Total
     */
    private function calculateTotal()
    {
        $this->total = 0;
        foreach ($this->cart as $item) {
            $this->total += $item['price'] * $item['quantity'];
        }
    }

    /**
     * Reset pencarian item
     */
    private function resetItemSearch()
    {
        $this->item_search = '';
        $this->item_results = [];
    }

    public function saveTransaction()
    {
        if (! $this->is_queue_stage_done) {
            session()->flash('error', 'Customer belum masuk antrean. Selesaikan tahap antrean terlebih dahulu.');

            return;
        }

        // 1. Validasi Sederhana
        if (count($this->cart) == 0 || ! $this->selected_customer_id || ! $this->selected_vehicle_id || ! $this->selected_mechanic_id) {
            session()->flash('error', 'Harap lengkapi semua data (Pelanggan, Kendaraan, Mekanik, dan Item).');

            return;
        }

        // 2. Mulai Database Transaction
        DB::beginTransaction();

        try {
            // 3. Gunakan draft antrean yang sudah ada, fallback create jika ID draft tidak tersedia.
            if ($this->queued_service_history_id) {
                $serviceHistory = ServiceHistory::find($this->queued_service_history_id);

                if (! $serviceHistory) {
                    throw new \Exception('Draft antrean tidak ditemukan. Silakan masukkan antrean ulang.');
                }

                if ((int) $serviceHistory->customer_id !== (int) $this->selected_customer_id || (int) $serviceHistory->vehicle_id !== (int) $this->selected_vehicle_id) {
                    throw new \Exception('Data customer/kendaraan berbeda dengan draft antrean yang dipilih.');
                }

                if ($serviceHistory->details()->exists()) {
                    throw new \Exception('Draft antrean ini sudah memiliki detail item. Gunakan data antrean lain atau reset antrean.');
                }

                $serviceHistory->update([
                    'mechanic_id' => $this->selected_mechanic_id,
                    'total_price' => $this->total,
                    'status' => 'done',
                    'service_date' => now(),
                    'notes' => $this->notes,
                ]);
            } else {
                $serviceHistory = ServiceHistory::create([
                    'customer_id' => $this->selected_customer_id,
                    'vehicle_id' => $this->selected_vehicle_id,
                    'mechanic_id' => $this->selected_mechanic_id,
                    'total_price' => $this->total,
                    'status' => 'done',
                    'service_date' => now(),
                    'notes' => $this->notes,
                ]);
                $this->queued_service_history_id = $serviceHistory->id;
            }

            // 4. Loop Keranjang dan Simpan Detail + Kurangi Stok
            foreach ($this->cart as $item) {
                // Tentukan model 'itemable' berdasarkan tipe
                $itemableType = ($item['type'] == 'service') ? Service::class : Sparepart::class;

                // 4a. Buat ServiceDetail (Polimorfik)
                ServiceDetail::create([
                    'service_history_id' => $serviceHistory->id,
                    'itemable_id' => $item['id'],
                    'itemable_type' => $itemableType,
                    'quantity' => $item['quantity'],
                    'price_at_transaction' => $item['price'],
                ]);

                // 4b. Jika itu sparepart, kurangi stok
                if ($item['type'] == 'sparepart') {
                    $sparepart = Sparepart::find($item['id']);
                    if ($sparepart->stock < $item['quantity']) {
                        // Jika stok tidak cukup, batalkan semua
                        throw new \Exception('Stok untuk '.$sparepart->name.' tidak mencukupi.');
                    }
                    $sparepart->decrement('stock', $item['quantity']);
                }
            }

            // 5. Jika semua berhasil, commit transaksi
            DB::commit();

            // 6. Kirim job ke antiran
            SendNotaWaJob::dispatch($serviceHistory->id);

            // Cek jika transaksi ini berasal dari booking
            if (request()->has('booking_id')) {
                $booking = Booking::find(request('booking_id'));
                if ($booking) {
                    $booking->update([
                        'status' => 'completed', // atau 'processed'
                    ]);
                }
            }

            // 7. Reset state komponen dan kirim pesan sukses
            session()->flash('message', 'Transaksi berhasil disimpan.');
            $this->resetAll();
        } catch (\Exception $e) {
            // 8. Jika ada error, rollback semua
            DB::rollBack();
            session()->flash('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    /**
     * Reset semua state setelah transaksi berhasil
     */
    private function resetAll()
    {
        $this->is_queue_stage_done = false;
        $this->queued_service_history_id = null;
        $this->customer_search = '';
        $this->customers = [];
        $this->selected_customer_id = null;
        $this->selected_customer_name = null;
        $this->selected_vehicle_id = null;
        $this->vehicles = [];
        $this->selected_mechanic_id = null;
        $this->item_search = '';
        $this->item_results = [];
        $this->cart = [];
        $this->total = 0;
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        return view('livewire.transaction-create');
    }
}
