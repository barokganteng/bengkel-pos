<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\CustomerManagement;
use App\Livewire\SparepartManagement;
use App\Livewire\ServiceManagement;
use Illuminate\Support\Facades\Auth;


Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/pelanggan', CustomerManagement::class)->name('pelanggan.index');
    Route::get('/sparepart', SparepartManagement::class)->name('sparepart.index');
    Route::get('/jasa-servis', ServiceManagement::class)->name('service.index');
});
