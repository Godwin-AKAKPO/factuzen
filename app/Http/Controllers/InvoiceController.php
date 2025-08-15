<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Client;
use App\Models\InvoiceItem;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->get('search');
        $status = $request->get('status');
        $type = $request->get('type', 'invoice'); // Par défaut les factures
        
        $invoices = Invoice::where('user_id', $request->user()->id)
                          ->where('type', $type)
                          ->with(['client:id,name,company'])
                          ->when($search, function ($query, $search) {
                              return $query->where(function ($q) use ($search) {
                                  $q->where('reference', 'like', "%{$search}%")
                                    ->orWhereHas('client', function ($clientQuery) use ($search) {
                                        $clientQuery->where('name', 'like', "%{$search}%")
                                                  ->orWhere('company', 'like', "%{$search}%");
                                    });
                              });
                          })
                          ->when($status, function ($query, $status) {
                              return $query->where('status', $status);
                          })
                          ->orderBy('created_at', 'desc')
                          ->paginate(15)
                          ->withQueryString();
        
        // Statistiques pour les filtres
        $stats = [
            'total' => Invoice::where('user_id', $request->user()->id)->where('type', $type)->count(),
            'draft' => Invoice::where('user_id', $request->user()->id)->where('type', $type)->where('status', 'draft')->count(),
            'sent' => Invoice::where('user_id', $request->user()->id)->where('type', $type)->where('status', 'sent')->count(),
            'paid' => Invoice::where('user_id', $request->user()->id)->where('type', $type)->where('status', 'paid')->count(),
            'overdue' => Invoice::where('user_id', $request->user()->id)->where('type', $type)->where('status', 'overdue')->count(),
        ];
        
        return Inertia::render('Invoices/Index', [
            'invoices' => $invoices,
            'search' => $search,
            'status' => $status,
            'type' => $type,
            'stats' => $stats,
        ]);
    }
    
    public function create(Request $request): Response
    {
        $clients = Client::where('user_id', $request->user()->id)
                        ->orderBy('name')
                        ->get(['id', 'name', 'company']);
        
        $selectedClientId = $request->get('client');
        $type = $request->get('type', 'invoice');
        
        return Inertia::render('Invoices/Create', [
            'clients' => $clients,
            'selectedClientId' => $selectedClientId,
            'type' => $type,
        ]);
    }
    
    public function store(StoreInvoiceRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['user_id'] = $request->user()->id;
        
        // Génération automatique de la référence
        $invoice = new Invoice($validated);
        $invoice->reference = $invoice->generateReference();
        $invoice->save();
        
        // Création des articles de facture
        if (!empty($validated['items'])) {
            foreach ($validated['items'] as $itemData) {
                $item = new InvoiceItem($itemData);
                $item->invoice_id = $invoice->id;
                $item->save();
            }
            
            // Recalcul des totaux de la facture
            $invoice->calculateTotals();
        }
        
        return redirect()->route('invoices.show', $invoice->id)
                        ->with('message', 'Facture créée avec succès.');
    }
    
    public function show(Invoice $invoice): Response
    {
        $this->authorize('view', $invoice);
        
        $invoice->load(['client', 'items']);
        
        return Inertia::render('Invoices/Show', [
            'invoice' => $invoice,
        ]);
    }
    
    public function edit(Invoice $invoice): Response
    {
        $this->authorize('update', $invoice);
        
        // Empêcher la modification des factures payées
        if ($invoice->status === 'paid') {
            return redirect()->route('invoices.show', $invoice->id)
                           ->withErrors(['edit' => 'Impossible de modifier une facture payée.']);
        }
        
        $clients = Client::where('user_id', auth()->id())
                        ->orderBy('name')
                        ->get(['id', 'name', 'company']);
        
        $invoice->load(['client', 'items']);
        
        return Inertia::render('Invoices/Edit', [
            'invoice' => $invoice,
            'clients' => $clients,
        ]);
    }
    
    public function update(UpdateInvoiceRequest $request, Invoice $invoice): RedirectResponse
    {
        $this->authorize('update', $invoice);
        
        // Empêcher la modification des factures payées
        if ($invoice->status === 'paid') {
            return redirect()->route('invoices.show', $invoice->id)
                           ->withErrors(['edit' => 'Impossible de modifier une facture payée.']);
        }
        
        $validated = $request->validated();
        
        // Mise à jour de la facture
        $invoice->update($validated);
        
        // Suppression des anciens articles
        $invoice->items()->delete();
        
        // Création des nouveaux articles
        if (!empty($validated['items'])) {
            foreach ($validated['items'] as $itemData) {
                $item = new InvoiceItem($itemData);
                $item->invoice_id = $invoice->id;
                $item->save();
            }
            
            // Recalcul des totaux
            $invoice->calculateTotals();
        }
        
        return redirect()->route('invoices.show', $invoice->id)
                        ->with('message', 'Facture modifiée avec succès.');
    }
    
    public function destroy(Invoice $invoice): RedirectResponse
    {
        $this->authorize('delete', $invoice);
        
        // Empêcher la suppression des factures payées ou envoyées
        if (in_array($invoice->status, ['paid', 'sent'])) {
            return back()->withErrors([
                'delete' => 'Impossible de supprimer une facture payée ou envoyée.'
            ]);
        }
        
        $invoice->delete();
        
        return redirect()->route('invoices.index')
                        ->with('message', 'Facture supprimée avec succès.');
    }
    
    /**
     * Mettre à jour le statut d'une facture
     */
    public function updateStatus(Request $request, Invoice $invoice): RedirectResponse
    {
        $this->authorize('update', $invoice);
        
        $request->validate([
            'status' => 'required|in:draft,sent,paid,overdue,cancelled'
        ]);
        
        $newStatus = $request->status;
        $updateData = ['status' => $newStatus];
        
        // Actions spécifiques selon le statut
        switch ($newStatus) {
            case 'sent':
                if (!$invoice->sent_at) {
                    $updateData['sent_at'] = now();
                }
                break;
            case 'paid':
                if (!$invoice->paid_at) {
                    $updateData['paid_at'] = now();
                }
                break;
            case 'draft':
                // Réinitialiser les timestamps si on repasse en brouillon
                $updateData['sent_at'] = null;
                $updateData['paid_at'] = null;
                break;
        }
        
        $invoice->update($updateData);
        
        return back()->with('message', 'Statut de la facture mis à jour.');
    }
    
    /**
     * Dupliquer une facture
     */
    public function duplicate(Invoice $invoice): RedirectResponse
    {
        $this->authorize('view', $invoice);
        
        // Créer une nouvelle facture basée sur l'ancienne
        $newInvoice = $invoice->replicate();
        $newInvoice->status = 'draft';
        $newInvoice->sent_at = null;
        $newInvoice->paid_at = null;
        $newInvoice->date = now()->format('Y-m-d');
        $newInvoice->due_date = now()->addDays(30)->format('Y-m-d');
        $newInvoice->reference = $newInvoice->generateReference();
        $newInvoice->save();
        
        // Dupliquer les articles
        foreach ($invoice->items as $item) {
            $newItem = $item->replicate();
            $newItem->invoice_id = $newInvoice->id;
            $newItem->save();
        }
        
        // Recalculer les totaux
        $newInvoice->calculateTotals();
        
        return redirect()->route('invoices.edit', $newInvoice->id)
                        ->with('message', 'Facture dupliquée avec succès.');
    }
    
    /**
     * Convertir un devis en facture
     */
    public function convertToInvoice(Invoice $quote): RedirectResponse
    {
        $this->authorize('update', $quote);
        
        if ($quote->type !== 'quote') {
            return back()->withErrors(['convert' => 'Seuls les devis peuvent être convertis en factures.']);
        }
        
        // Créer une nouvelle facture basée sur le devis
        $invoice = $quote->replicate();
        $invoice->type = 'invoice';
        $invoice->status = 'draft';
        $invoice->sent_at = null;
        $invoice->paid_at = null;
        $invoice->date = now()->format('Y-m-d');
        $invoice->due_date = now()->addDays(30)->format('Y-m-d');
        $invoice->reference = $invoice->generateReference();
        $invoice->save();
        
        // Dupliquer les articles
        foreach ($quote->items as $item) {
            $newItem = $item->replicate();
            $newItem->invoice_id = $invoice->id;
            $newItem->save();
        }
        
        // Recalculer les totaux
        $invoice->calculateTotals();
        
        return redirect()->route('invoices.show', $invoice->id)
                        ->with('message', 'Devis converti en facture avec succès.');
    }
}