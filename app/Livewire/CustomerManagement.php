<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User; // Model User kita
use Livewire\WithPagination; // Untuk pagination
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;

class CustomerManagement extends Component
{
    use WithPagination;

    // Properti untuk data binding form
    public $name, $email, $phone, $password;
    public $customer_id; // Untuk menyimpan ID saat edit
    public $search = ''; // Untuk pencarian

    // Properti untuk modal
    public $isModalOpen = false;

    // Aturan validasi
    protected function rules()
    {
        // Password hanya wajib saat membuat baru
        $passwordRule = $this->customer_id ? 'nullable|min:6' : 'required|min:6';

        return [
            'name' => 'required|string|min:3',
            'email' => 'required|email|unique:users,email,' . $this->customer_id,
            'phone' => 'nullable|string',
            'password' => $passwordRule,
        ];
    }

    // Render view
    #[Layout('layouts.admin')]
    public function render()
    {
        // Ambil data pelanggan dengan pencarian dan pagination
        $customers = User::customer() // Menggunakan scope yg kita buat
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->paginate(10); // 10 data per halaman

        return view('livewire.customer-management', [
            'customers' => $customers,
        ]);
    }

    // Membuka modal untuk membuat data baru
    public function create()
    {
        $this->resetForm();
        $this->isModalOpen = true;
    }

    // Membuka modal untuk edit data
    public function edit($id)
    {
        $customer = User::findOrFail($id);
        $this->customer_id = $id;
        $this->name = $customer->name;
        $this->email = $customer->email;
        $this->phone = $customer->phone;
        $this->password = ''; // Kosongkan password saat edit

        $this->isModalOpen = true;
    }

    // Menyimpan data (Create atau Update)
    public function store()
    {
        $this->validate(); // Validasi data

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'role' => 'pelanggan' // Pastikan rolenya pelanggan
        ];

        // Hanya update password jika diisi
        if (!empty($this->password)) {
            $data['password'] = Hash::make($this->password);
        }

        // Cek apakah ini create atau update
        User::updateOrCreate(['id' => $this->customer_id], $data);

        // Kirim flash message
        session()->flash(
            'message',
            $this->customer_id ? 'Pelanggan berhasil diperbarui.' : 'Pelanggan berhasil ditambahkan.'
        );

        $this->closeModal();
    }

    // Menghapus data
    public function delete($id)
    {
        User::find($id)->delete();
        session()->flash('message', 'Pelanggan berhasil dihapus.');
    }

    // Menutup modal
    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetForm();
    }

    // Reset form
    private function resetForm()
    {
        $this->name = '';
        $this->email = '';
        $this->phone = '';
        $this->password = '';
        $this->customer_id = null;
    }
}
