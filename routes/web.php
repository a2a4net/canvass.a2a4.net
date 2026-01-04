<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Dashboard;

Route::redirect('/', '/dashboard');

Route::get('/dashboard', Dashboard::class)->name('dashboard');
