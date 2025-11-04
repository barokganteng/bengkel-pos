<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User; // Model User (untuk Pelanggan & Mekanik)
use App\Models\Vehicle; // Model Kendaraan
use App\Models\Service; // Model Jasa Servis
use App\Models\Sparepart; // Model Sparepart
use App\Models\ServiceHistory;
use App\Models\ServiceDetail;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;

class TransactionCreate extends Component
{
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

    //Bagian Item
    public $item_search = '';
    public $item_results = [];


    // Bagian Keranjang
    public $cart = [];
    public $total = 0;

    /**
     * Dijalankan saat komponen dimuat pertama kali
     */
    public function mount()
    {
        // Langsung muat daftar mekanik
        $this->mechanics = User::mechanic()->get();
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
        $this->selected_customer_id = null;
        $this->selected_customer_name = null;
        $this->selected_vehicle_id = null;
        $this->vehicles = [];
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
                'stock' => null // Jasa tidak punya stok
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
                'stock' => $sparepart->stock
            ];
        }
    }

    /**
     * Dijalankan saat item (jasa/sparepart) dipilih dari hasil pencarian
     */
    public function addItemToCart($type, $id)
    {
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
                'type' => 'service'
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
                'type' => 'sparepart'
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
        // 1. Validasi Sederhana
        if (count($this->cart) == 0 || !$this->selected_customer_id || !$this->selected_vehicle_id || !$this->selected_mechanic_id) {
            session()->flash('error', 'Harap lengkapi semua data (Pelanggan, Kendaraan, Mekanik, dan Item).');
            return;
        }

        // 2. Mulai Database Transaction
        DB::beginTransaction();

        try {
            // 3. Buat Catatan ServiceHistory (Transaksi Induk)
            $serviceHistory = ServiceHistory::create([
                'customer_id' => $this->selected_customer_id,
                'vehicle_id' => $this->selected_vehicle_id,
                'mechanic_id' => $this->selected_mechanic_id,
                'total_price' => $this->total,
                'status' => 'pending', // Atau 'done' jika Anda mau, kita set 'pending' untuk status "dikerjakan"
                'service_date' => now(),
            ]);

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
                        throw new \Exception('Stok untuk ' . $sparepart->name . ' tidak mencukupi.');
                    }
                    $sparepart->decrement('stock', $item['quantity']);
                }
            }

            // 5. Jika semua berhasil, commit transaksi
            DB::commit();

            // 6. Reset state komponen dan kirim pesan sukses
            session()->flash('message', 'Transaksi berhasil disimpan.');
            $this->resetAll();
        } catch (\Exception $e) {
            // 7. Jika ada error, rollback semua
            DB::rollBack();
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Reset semua state setelah transaksi berhasil
     */
    private function resetAll()
    {
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
