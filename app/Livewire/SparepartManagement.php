<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Sparepart;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Livewire\Attributes\Validate;

class SparepartManagement extends Component
{
    use WithPagination;

    // Properti untuk data binding form
    #[Validate('required|min:3')]
    public $name;
    #[Validate('nullable|unique:spareparts,sku')]
    public $sku;
    #[Validate('required|integer|min:0')]
    public $stock;
    #[Validate('required|numeric|min:0')]
    public $sale_price;
    // Properti untuk menyimpan ID saat edit
    public $sparepart_id; // Untuk menyimpan ID saat edit
    public $search = '';

    // Properti untuk modal
    public $isModalOpen = false;

    // Jangan lupa tambahkan layout admin
    #[Layout('layouts.admin')]
    public function render()
    {
        $spareparts = Sparepart::where(function ($query) {
            $query->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('sku', 'like', '%' . $this->search . '%');
        })
            ->paginate(10);
        return view('livewire.sparepart-management', [
            'spareparts' => $spareparts,
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
        $sparepart = Sparepart::findOrFail($id);
        $this->sparepart_id = $id;
        $this->name = $sparepart->name;
        $this->sku = $sparepart->sku;
        $this->stock = $sparepart->stock;
        $this->sale_price = $sparepart->sale_price;

        $this->isModalOpen = true;
    }

    // Menyimpan data (Create atau Update)
    public function store()
    {
        $this->validate(); // Validasi data

        $respone = Sparepart::updateOrCreate([
            'id' => $this->sparepart_id,
        ], [
            'name' => $this->name,
            'sku' => $this->sku,
            'stock' => $this->stock,
            'sale_price' => $this->sale_price,
        ]);

        session()->flash(
            'message',
            $this->sparepart_id ? 'Spare part berhasil diperbarui.' : 'Spare part berhasil ditambahkan.'
        );

        $this->closeModal();
    }

    // Menghapus data
    public function delete($id)
    {
        Sparepart::find($id)->delete();
        session()->flash('message', 'Spare part berhasil dihapus.');
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
        $this->sku = '';
        $this->stock = 0;
        $this->sale_price = 0;
        $this->sparepart_id = null;
    }
}
