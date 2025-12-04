<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Contrôleur de gestion des sessions d'authentification
 * Gère la connexion, la déconnexion et l'affichage du formulaire de login
 */
class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     * Affiche la page de connexion avec Inertia
     */
    public function create(): Response
    {
        // Rendu de la vue de connexion avec les données nécessaires
        return Inertia::render('Auth/Login', [
            // Vérifie si la route de réinitialisation de mot de passe existe
            'canResetPassword' => Route::has('password.request'),
            // Récupère le message de statut de la session (ex: après reset password)
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     * Traite la requête de connexion de l'utilisateur
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Authentifie l'utilisateur avec les credentials fournis
        // Lance une exception si l'authentification échoue
        $request->authenticate();

        // Régénère l'ID de session pour prévenir la fixation de session
        $request->session()->regenerate();

        // Redirige vers la page initialement demandée ou vers le dashboard par défaut
        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     * Déconnecte l'utilisateur et détruit sa session
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Déconnecte l'utilisateur du guard 'web'
        Auth::guard('web')->logout();

        // Invalide la session actuelle
        $request->session()->invalidate();

        // Régénère le token CSRF pour la sécurité
        $request->session()->regenerateToken();

        // Redirige vers la page d'accueil
        return redirect('/');
    }
}
