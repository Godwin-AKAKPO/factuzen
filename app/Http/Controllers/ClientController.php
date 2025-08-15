<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

// Contrôleur pour gérer les clients
class ClientController extends Controller
{
    // Affiche la liste des clients avec recherche et pagination
    public function index(Request $request): Response
    {
        // Récupère le terme de recherche depuis la requête
        $search = $request->get('search');
        
        // Récupère les clients de l'utilisateur connecté, filtre si recherche, compte les factures, trie et pagine
        $clients = Client::where('user_id', $request->user()->id)
                        ->when($search, function ($query, $search) {
                            // Filtre par nom, société ou email si recherche présente
                            return $query->where(function ($q) use ($search) {
                                $q->where('name', 'like', "%{$search}%")
                                  ->orWhere('company', 'like', "%{$search}%")
                                  ->orWhere('email', 'like', "%{$search}%");
                            });
                        })
                        ->withCount('invoices') // Compte le nombre de factures par client
                        ->orderBy('created_at', 'desc') // Trie par date de création décroissante
                        ->paginate(15) // Paginer par 15 résultats
                        ->withQueryString(); // Garde les paramètres de requête dans la pagination
        
        // Retourne la vue Inertia avec les clients et la recherche
        return Inertia::render('Clients/Index', [
            'clients' => $clients,
            'search' => $search,
        ]);
    }
    
    // Affiche le formulaire de création d'un client
    public function create(): Response
    {
        return Inertia::render('Clients/Create');
    }
    
    // Enregistre un nouveau client en base de données
    public function store(Request $request): RedirectResponse
    {
        // Valide les données du formulaire
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);
        
        // Ajoute l'id de l'utilisateur connecté
        $validated['user_id'] = $request->user()->id;
        
        // Crée le client
        Client::create($validated);
        
        // Redirige vers la liste avec un message de succès
        return redirect()->route('clients.index')
                        ->with('message', 'Client créé avec succès.');
    }
    
    // Affiche les détails d'un client
    public function show(Client $client): Response
    {
        // Vérifie l'autorisation de voir ce client
        $this->authorize('view', $client);
        
        // Charge les factures du client, triées par date de création décroissante
        $client->load([
            'invoices' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }
        ]);
        
        // Retourne la vue Inertia avec le client
        return Inertia::render('Clients/Show', [
            'client' => $client,
        ]);
    }
    
    // Affiche le formulaire d'édition d'un client
    public function edit(Client $client): Response
    {
        // Vérifie l'autorisation de modifier ce client
        $this->authorize('update', $client);
        
        // Retourne la vue Inertia avec le client à éditer
        return Inertia::render('Clients/Edit', [
            'client' => $client,
        ]);
    }
    
    // Met à jour les informations d'un client
    public function update(Request $request, Client $client): RedirectResponse
    {
        // Vérifie l'autorisation de modifier ce client
        $this->authorize('update', $client);
        
        // Valide les données du formulaire
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);
        
        // Met à jour le client avec les données validées
        $client->update($validated);
        
        // Redirige vers la liste avec un message de succès
        return redirect()->route('clients.index')
                        ->with('message', 'Client modifié avec succès.');
    }
    
    // Supprime un client
    public function destroy(Client $client): RedirectResponse
    {
        // Vérifie l'autorisation de supprimer ce client
        $this->authorize('delete', $client);
        
        // Supprime le client
        $client->delete();
        
        // Redirige vers la liste avec un message de succès
        return redirect()->route('clients.index')
                        ->with('message', 'Client supprimé avec succès.');
    }
}