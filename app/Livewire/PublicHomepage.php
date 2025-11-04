<?php

namespace App\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Component;

class PublicHomepage extends Component
{
    #[Layout('layouts.public')]
    public function render()
    {
        return view('livewire.public-homepage'); // 👈 Gunakan layout publik
    }
}
