<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Service; // 👈 Gunakan Model Service
use Livewire\Attributes\Layout;
use Livewire\WithPagination;

class ServiceManagement extends Component
{
    use WithPagination;

    // Properti untuk data binding form
    public $name, $price;
    public $service_id; // Untuk menyimpan ID saat edit
    public $search = '';

    // Properti untuk modal
    public $isModalOpen = false;

    // Aturan validasi
    protected function rules()
    {
        return [
            'name' => 'required|string|min:3|unique:services,name,' . $this->service_id,
            'price' => 'required|numeric|min:0',
        ];
    }

    // Jangan lupa tambahkan layout admin
    #[Layout('layouts.admin')]
    public function render()
    {
        $services = Service::where('name', 'like', '%' . $this->search . '%')
            ->paginate(10);

        return view('livewire.service-management', [
            'services' => $services,
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
        $service = Service::findOrFail($id);
        $this->service_id = $id;
        $this->name = $service->name;
        $this->price = $service->price;

        $this->isModalOpen = true;
    }

    // Menyimpan data (Create atau Update)
    public function store()
    {
        $this->validate(); // Validasi data

        Service::updateOrCreate(['id' => $this->service_id], [
            'name' => $this->name,
            'price' => $this->price,
        ]);

        session()->flash(
            'message',
            $this->service_id ? 'Jasa servis berhasil diperbarui.' : 'Jasa servis berhasil ditambahkan.'
        );

        $this->closeModal();
    }

    // Menghapus data
    public function delete($id)
    {
        Service::find($id)->delete();
        session()->flash('message', 'Jasa servis berhasil dihapus.');
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
        $this->price = 0;
        $this->service_id = null;
    }
}
