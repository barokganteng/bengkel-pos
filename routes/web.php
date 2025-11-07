<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\CustomerManagement;
use App\Livewire\SparepartManagement;
use App\Livewire\ServiceManagement;
use App\Livewire\TransactionCreate;
use App\Livewire\TransactionList;
use App\Livewire\BookingManagement;
use App\Livewire\GalleryManagement;
use App\Livewire\PublicHomepage;
use App\Livewire\PublicGallery;
use App\Livewire\PublicBookingForm;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Auth;


Route::get('/', PublicHomepage::class)->name('public.home');
Route::get('/galeri-kami', PublicGallery::class)->name('public.gallery');
Route::get('/booking-online', PublicBookingForm::class)->name('public.booking');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/pelanggan', CustomerManagement::class)->name('pelanggan.index');
    Route::get('/sparepart', SparepartManagement::class)->name('sparepart.index');
    Route::get('/jasa-servis', ServiceManagement::class)->name('service.index');

    Route::prefix('transaksi')->name('transaksi.')->group(function () {
        Route::get('/baru', TransactionCreate::class)->name('create');
        Route::get('/', TransactionList::class)->name('index');
    });
    Route::get('/booking', BookingManagement::class)->name('booking.index');
    Route::get('/galeri', GalleryManagement::class)->name('gallery.index');
});

Route::get('/last-service', function () {
    $lastService = Vehicle::find(1)->latestService();

    dd($lastService);
});
