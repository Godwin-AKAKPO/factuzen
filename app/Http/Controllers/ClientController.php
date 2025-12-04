<?php

namespace App\Http\Controllers;

// Importation des modèles et classes nécessaires
use App\Models\Client;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

// Déclaration du contrôleur ClientController
class ClientController extends Controller
{
    use AuthorizesRequests; // Permet d'utiliser les méthodes d'autorisation

    // Affiche la liste des clients
    public function index(Request $request): Response
    {
        $search = $request->get('search'); // Récupère le terme de recherche depuis la requête

        // Récupère les clients de l'utilisateur connecté, filtre selon la recherche, compte les factures, trie et pagine
        $clients = Client::where('user_id', $request->user()->id) // Filtre les clients de l'utilisateur courant
            ->when($search, function ($query, $search) { // Si une recherche est présente
                return $query->where(function ($q) use ($search) { // Filtre sur le nom, la société ou l'email
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('company', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->withCount('invoices') // Ajoute le nombre de factures associées à chaque client
            ->orderBy('created_at', 'desc') // Trie par date de création décroissante
            ->paginate(15) // Paginer les résultats, 15 par page
            ->withQueryString(); // Conserve les paramètres de la requête dans la pagination

        // Retourne la vue Inertia avec les clients et le terme de recherche
        return Inertia::render('Clients/Index', [
            'clients' => $clients,
            'search' => $search,
        ]);
    }

    // Affiche le formulaire de création d'un client
    public function create(): Response
    {
        return Inertia::render('Clients/Create'); // Retourne la vue de création
    }

    // Enregistre un nouveau client dans la base de données
    public function store(StoreClientRequest $request): RedirectResponse
    {
        $validated = $request->validated(); // Valide les données du formulaire
        $validated['user_id'] = $request->user()->id; // Associe le client à l'utilisateur connecté

        Client::create($validated); // Crée le client en base de données

        // Redirige vers la liste avec un message de succès
        return redirect()->route('clients.index')
            ->with('message', 'Client créé avec succès.');
    }

    // Affiche les détails d'un client
    public function show(Client $client): Response
    {
        $this->authorize('view', $client); // Vérifie si l'utilisateur peut voir ce client

        // Charge les factures du client, triées par date de création décroissante
        $client->load([
            'invoices' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }
        ]);

        // Retourne la vue avec les détails du client
        return Inertia::render('Clients/Show', [
            'client' => $client,
        ]);
    }

    // Affiche le formulaire d'édition d'un client
    public function edit(Client $client): Response
    {
        $this->authorize('update', $client); // Vérifie si l'utilisateur peut modifier ce client

        // Retourne la vue d'édition avec les données du client
        return Inertia::render('Clients/Edit', [
            'client' => $client,
        ]);
    }

    // Met à jour les informations d'un client
    public function update(UpdateClientRequest $request, Client $client): RedirectResponse
    {
        $client->update($request->validated()); // Met à jour le client avec les données validées

        // Redirige vers la liste avec un message de succès
        return redirect()->route('clients.index')
            ->with('message', 'Client modifié avec succès.');
    }

    // Supprime un client
    public function destroy(Client $client): RedirectResponse
    {
        
        $this->authorize('delete', $client); // Vérifie si l'utilisateur peut supprimer ce client
        
        // Vérifie s'il y a des factures associées au client
        if ($client->invoices()->count() > 0) {
            // Retourne une erreur si le client a des factures
            return back()->withErrors([
                'delete' => 'Impossible de supprimer ce client car il a des factures associées.'
            ]);
        }
        
        $client->delete(); // Supprime le client

        // Redirige vers la liste avec un message de succès
        return redirect()->route('clients.index')
            ->with('message', 'Client supprimé avec succès.');
    }
}