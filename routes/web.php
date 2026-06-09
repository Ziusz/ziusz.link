<?php

use App\Support\AdminAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::livewire('/admin/login', 'pages::admin.login')->name('admin.login');

Route::post('/admin/logout', function (Request $request, AdminAccess $adminAccess): RedirectResponse {
    $adminAccess->logout($request);

    return redirect()->route('admin.login');
})->middleware('admin')->name('admin.logout');

Route::middleware('admin')->group(function (): void {
    Route::view('/admin', 'admin.dashboard')->name('admin.dashboard');
});
