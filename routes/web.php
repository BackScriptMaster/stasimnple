<?php

use App\Livewire\Admin\Logs;
use App\Livewire\Tradep2p;
use App\Livewire\PaymentProcess;
use App\Livewire\Trader\Orders;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/logs', Logs::class)->name('admin.logs');

    Route::get('/tradep2p', Tradep2p::class)->name('tradep2p');

    Route::get('/payment_process/{datetime}', PaymentProcess::class)->name('payment.process');

    Route::get('/trader/orders', Orders::class)->name('trader.orders');
});
