<?php

use App\Http\Controllers\Admin\LinkController as AdminLinkController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LinkRedirectController;
use App\Http\Controllers\LogoController;
use App\Support\AdminAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/logos/platforms/{platform}', [LogoController::class, 'platform'])->name('logos.platforms.show');
Route::get('/logos/links/{link}', [LogoController::class, 'link'])->name('logos.links.show');

Route::livewire('/admin/login', 'pages::admin.login')->name('admin.login');

Route::post('/admin/logout', function (Request $request, AdminAccess $adminAccess): RedirectResponse {
    $adminAccess->logout($request);

    return redirect()->route('admin.login');
})->middleware('admin')->name('admin.logout');

Route::middleware('admin')->group(function (): void {
    Route::view('/admin', 'admin.dashboard')->name('admin.dashboard');
    Route::get('/admin/links', [AdminLinkController::class, 'index'])->name('admin.links.index');
    Route::get('/admin/links/create', [AdminLinkController::class, 'create'])->name('admin.links.create');
    Route::post('/admin/links', [AdminLinkController::class, 'store'])->name('admin.links.store');
    Route::get('/admin/links/{link}', [AdminLinkController::class, 'show'])->name('admin.links.show');
    Route::get('/admin/links/{link}/edit', [AdminLinkController::class, 'edit'])->name('admin.links.edit');
    Route::put('/admin/links/{link}', [AdminLinkController::class, 'update'])->name('admin.links.update');
    Route::delete('/admin/links/{link}', [AdminLinkController::class, 'destroy'])->name('admin.links.destroy');
});

Route::get('/{link:slug}', LinkRedirectController::class)->name('links.redirect');
