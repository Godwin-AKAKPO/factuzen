<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class ClientController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->get('search');
        
        $clients = Client::where('user_id', $request->user()->id)
                        ->when($search, function ($query, $search) {
                            return $query->where(function ($q) use ($search) {
                                $q->where('name', 'like', "%{$search}%")
                                  ->orWhere('company', 'like', "%{$search}%")
                                  ->orWhere('email', 'like', "%{$search}%");
                            });
                        })
                        ->withCount('invoices')
                        ->orderBy('created_at', 'desc')
                        ->paginate(15)
                        ->withQueryString();
        
        return Inertia::render('Clients/Index', [
            'clients' => $clients,
            'search' => $search,
        ]);
    }
    
    public function create(): Response
    {
        return Inertia::render('Clients/Create');
    }
    
    public function store(StoreClientRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['user_id'] = $request->user()->id;
        
        Client::create($validated);
        
        return redirect()->route('clients.index')
                        ->with('message', 'Client créé avec succès.');
    }
    
    public function show(Client $client): Response
    {
        $this->authorize('view', $client);
        
        $client->load([
            'invoices' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }
        ]);
        
        return Inertia::render('Clients/Show', [
            'client' => $client,
        ]);
    }
    
    public function edit(Client $client): Response
    {
        $this->authorize('update', $client);
        
        return Inertia::render('Clients/Edit', [
            'client' => $client,
        ]);
    }
    
    public function update(UpdateClientRequest $request, Client $client): RedirectResponse
    {
        $client->update($request->validated());
        
        return redirect()->route('clients.index')
                        ->with('message', 'Client modifié avec succès.');
    }
    
    public function destroy(Client $client): RedirectResponse
    {
        $this->authorize('delete', $client);
        
        // Vérifier s'il y a des factures associées
        if ($client->invoices()->count() > 0) {
            return back()->withErrors([
                'delete' => 'Impossible de supprimer ce client car il a des factures associées.'
            ]);
        }
        
        $client->delete();
        
        return redirect()->route('clients.index')
                        ->with('message', 'Client supprimé avec succès.');
    }
}