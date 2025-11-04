<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Gallery; // 👈 Gunakan Model Gallery
use Livewire\Attributes\Layout;

class PublicGallery extends Component
{
    #[Layout('layouts.public')]
    public function render()
    {
        $galleries = Gallery::latest()->get(); // Ambil semua gambar

        return view('livewire.public-gallery', [
            'galleries' => $galleries
        ]);
    }
}
