<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Contrôleur de gestion des sessions d'authentification
 * Gère la connexion, la déconnexion et l'affichage du formulaire de login
 */
class AuthenticatedSessionController extends Controller
{
    // Affiche la page de connexion
    public function create(): Response
    {
        // Rendu de la vue de connexion avec les données nécessaires
        return Inertia::render('Auth/Login', [
            'canResetPassword' => route('password.request') ? true : false,
            'status' => session('status'),
        ]);
    }

    // Gère la connexion
    public function store(LoginRequest $request)
    {
        try {
            $request->authenticate();
            $request->session()->regenerate();

            // Si c'est une requête AJAX (Axios)
            if ($request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => true,
                    'redirect' => route('dashboard')
                ]);
            }

            // Sinon redirection classique
            return redirect()->intended(route('dashboard'));

        } catch (ValidationException $e) {

            // Pour Axios : gérer throttling et erreurs validation
            if ($request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {

                // Trop de tentatives
                if ($e->status === 429) {
                    $retryAfter = RateLimiter::availableIn($request->throttleKey());
                    return response()->json([
                        'errors' => $e->errors(),
                        'retry_after' => $retryAfter,
                        'message' => 'Trop de tentatives. Veuillez réessayer dans ' . $retryAfter . ' secondes.'
                    ], 429);
                }

                // Autres erreurs de validation
                return response()->json([
                    'errors' => $e->errors(),
                    'message' => 'Données invalides'
                ], 422);
            }

            // Pour Inertia classique
            throw $e;
        }
    }

    // Déconnexion
    public function destroy(Request $request)
    {
        // Déconnecte l'utilisateur du guard 'web'
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirige vers la page d'accueil
        return redirect('/');
    }
}
