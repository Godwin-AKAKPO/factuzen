<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    //Dashbord
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    //Gestion du profil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //Gestion des clients
    Route::ressource('clients', ClientController::class);

    //Gestion des factures
    Route::ressource('invoices', InvoiceController::class);

    //Route spéciale pour les factures 
    Route::post('/invoices/{invoices}/send', [InvoiceController::class, 'send'])->name('invoices.send');
    Route::get('/invoices/{invoices}/pdf', [InvoiceController::class, 'pdf'])->name('invoices.pdf');
    Route::patch('/invoices/{invoices}/status', [InvoiceController::class, 'updateStatus'])->name('invoices.status');

    //Devis(quotes)
    Route::get('/quotes', [InvoiceController::class, 'quotes'])->name('quotes.index');
    Route::get('/quotes/create', [InvoiceController::class, 'createQuote'])->name('quotes.create');
    Route::post('quotes/{quotes}/convert', [InvoiceController::class, 'convertToInvoice'])->name('quotes.convert')
});

require __DIR__.'/auth.php';
