<?php

namespace App\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Models\Service;
use App\Models\Gallery;
use App\Models\User;

class PublicHomepage extends Component
{
    #[Layout('layouts.public')]
    public function render()
    {
        $services = Service::take(6)->get();
        $latest_gallery = Gallery::take(4)->get();
        $user_counts = User::count();

        return view('livewire.public-homepage', [
            'services' => $services,
            'latestGalleries' => $latest_gallery,
            'user_counts' => $user_counts
        ]);
    }
}
