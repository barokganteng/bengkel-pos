<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Gallery; // 👈 Gunakan Model Gallery
use Livewire\WithFileUploads; // 👈 1. IMPORT Trait File Uploads
use Illuminate\Support\Facades\Storage; // 👈 2. IMPORT Storage Facade
use Livewire\Attributes\Layout;

class GalleryManagement extends Component
{
    use WithFileUploads; // 👈 3. GUNAKAN Trait

    // Properti untuk form
    public $image; // Properti untuk file upload
    public $caption;

    // Aturan validasi
    protected $rules = [
        'image' => 'required|image|max:2048', // Maks 2MB, harus gambar
        'caption' => 'nullable|string|max:255',
    ];

    #[Layout('layouts.admin')]
    public function render()
    {
        $galleries = Gallery::latest()->get(); // Ambil semua gambar

        return view('livewire.gallery-management', [
            'galleries' => $galleries
        ]);
    }

    /**
     * Menyimpan gambar baru
     */
    public function store()
    {
        $this->validate();

        // Simpan gambar ke 'public/gallery'
        // 'storage:link' akan membuatnya bisa diakses via 'storage/gallery'
        $path = $this->image->store('gallery', 'public');

        // Simpan path ke database
        Gallery::create([
            'image_path' => $path,
            'caption' => $this->caption
        ]);

        session()->flash('message', 'Gambar berhasil diunggah.');
        $this->resetForm();
    }

    /**
     * Menghapus gambar
     */
    public function delete($id)
    {
        $gallery = Gallery::find($id);
        if ($gallery) {
            // 1. Hapus file dari storage
            Storage::disk('public')->delete($gallery->image_path);

            // 2. Hapus record dari database
            $gallery->delete();
            session()->flash('message', 'Gambar berhasil dihapus.');
        }
    }

    /**
     * Reset form setelah upload
     */
    private function resetForm()
    {
        $this->image = null;
        $this->caption = null;
        // Kita perlu mereset error validasi file upload secara manual
        $this->resetErrorBag('image');
    }
}
