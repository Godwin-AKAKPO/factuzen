<?php

// Importation des contrôleurs nécessaires et des classes Laravel
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\InvoiceController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Route d'accueil affichant la page "Welcome" avec quelques infos système
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),           // Vérifie si la route de connexion existe
        'canRegister' => Route::has('register'),     // Vérifie si la route d'inscription existe
        'laravelVersion' => Application::VERSION,    // Version de Laravel
        'phpVersion' => PHP_VERSION,                 // Version de PHP
    ]);
})->name('welcome')->middleware('guest');

// Groupe de routes protégées par le middleware d'authentification
Route::middleware('auth')->group(function () {
    
    // Tableau de bord (Dashboard)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Gestion du profil utilisateur
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');      // Affiche le formulaire d'édition du profil
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update'); // Met à jour le profil
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy'); // Supprime le profil
    
    // Gestion des clients (CRUD)
    Route::resource('clients', ClientController::class);
    
    // Gestion des factures (CRUD)
    Route::resource('invoices', InvoiceController::class);
    
    // // Routes supplémentaires pour les factures
    // Route::post('/invoices/{invoice}/send', [InvoiceController::class, 'send'])->name('invoices.send'); // Envoi d'une facture
    // Route::get('/invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('invoices.pdf');     // Génération du PDF d'une facture
    // Route::patch('/invoices/{invoice}/status', [InvoiceController::class, 'updateStatus'])->name('invoices.status'); // Mise à jour du statut d'une facture
    
    // Gestion des devis (quotes)
    Route::get('/quotes', [InvoiceController::class, 'quotes'])->name('quotes.index');                  // Liste des devis
    Route::get('/quotes/create', [InvoiceController::class, 'createQuote'])->name('quotes.create');     // Création d'un devis
    Route::post('/quotes/{quote}/convert', [InvoiceController::class, 'convertToInvoice'])->name('quotes.convert'); // Conversion d'un devis en facture

    Route::get('/invoices/{invoice}/pdf', [InvoiceController::class, 'downloadPdf'])
        ->name('invoices.pdf.download');
    
    // Route::get('/invoices/{invoice}/pdf/preview', [InvoiceController::class, 'previewPdf'])
    //     ->name('invoices.pdf.preview');
    
    Route::post('/invoices/{invoice}/send-email', [InvoiceController::class, 'sendEmail'])
        ->name('invoices.send-email');
});

// Inclusion des routes d'authentification générées par Laravel Breeze ou Jetstream
require __DIR__.'/auth.php';

