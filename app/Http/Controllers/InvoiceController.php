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
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Services\PdfService;

class InvoiceController extends Controller
{
    use AuthorizesRequests;
    protected $pdfService;

    public function __construct(PdfService $pdfService)
    {
        $this->pdfService = $pdfService;
    }
    public function index(Request $request): Response
    {
        $search = $request->get('search');
        $status = $request->get('status');
        $type = $request->get('type', 'invoice');
        
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
        
        $invoice = new Invoice($validated);
        $invoice->reference = $invoice->generateReference();
        $invoice->save();
        
        if (!empty($validated['items'])) {
            foreach ($validated['items'] as $itemData) {
                $item = new InvoiceItem($itemData);
                $item->invoice_id = $invoice->id;
                $item->save();
            }
            $invoice->calculateTotals();
        }
        
        return redirect()->route('invoices.show', $invoice->id)
                        ->with('message', 'Facture créée avec succès.');
    }
    
    public function show(Invoice $invoice): Response
    {
        $this->authorize('view', $invoice);
        
        $invoice->load(['client', 'items', 'user']);
        
        return Inertia::render('Invoices/Show', [
            'invoice' => $invoice,
        ]);
    }
    
    public function edit(Invoice $invoice): Response
    {
        $this->authorize('update', $invoice);
        
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
        
        if ($invoice->status === 'paid') {
            return redirect()->route('invoices.show', $invoice->id)
                           ->withErrors(['edit' => 'Impossible de modifier une facture payée.']);
        }
        
        $validated = $request->validated();
        
        $invoice->update($validated);
        
        $invoice->items()->delete();
        
        if (!empty($validated['items'])) {
            foreach ($validated['items'] as $itemData) {
                $item = new InvoiceItem($itemData);
                $item->invoice_id = $invoice->id;
                $item->save();
            }
            $invoice->calculateTotals();
        }
        
        return redirect()->route('invoices.show', $invoice->id)
                        ->with('message', 'Facture modifiée avec succès.');
    }
    
    public function destroy(Invoice $invoice): RedirectResponse
    {
        $this->authorize('delete', $invoice);
        
        if (in_array($invoice->status, ['paid', 'sent'])) {
            return back()->withErrors([
                'delete' => 'Impossible de supprimer une facture payée ou envoyée.'
            ]);
        }
        
        $invoice->delete();
        
        return redirect()->route('invoices.index')
                        ->with('message', 'Facture supprimée avec succès.');
    }
    
    public function updateStatus(Request $request, Invoice $invoice): RedirectResponse
    {
        $this->authorize('update', $invoice);
        
        $request->validate([
            'status' => 'required|in:draft,sent,paid,overdue,cancelled'
        ]);
        
        $newStatus = $request->status;
        $updateData = ['status' => $newStatus];
        
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
                $updateData['sent_at'] = null;
                $updateData['paid_at'] = null;
                break;
        }
        
        $invoice->update($updateData);
        
        return back()->with('message', 'Statut de la facture mis à jour.');
    }
    
    public function duplicate(Invoice $invoice): RedirectResponse
    {
        $this->authorize('view', $invoice);
        
        $newInvoice = $invoice->replicate();
        $newInvoice->status = 'draft';
        $newInvoice->sent_at = null;
        $newInvoice->paid_at = null;
        $newInvoice->date = now()->format('Y-m-d');
        $newInvoice->due_date = now()->addDays(30)->format('Y-m-d');
        $newInvoice->reference = $newInvoice->generateReference();
        $newInvoice->save();
        
        foreach ($invoice->items as $item) {
            $newItem = $item->replicate();
            $newItem->invoice_id = $newInvoice->id;
            $newItem->save();
        }
        
        $newInvoice->calculateTotals();
        
        return redirect()->route('invoices.edit', $newInvoice->id)
                        ->with('message', 'Facture dupliquée avec succès.');
    }
    
    public function convertToInvoice(Invoice $quote): RedirectResponse
    {
        $this->authorize('update', $quote);
        
        if ($quote->type !== 'quote') {
            return back()->withErrors(['convert' => 'Seuls les devis peuvent être convertis en factures.']);
        }
        
        $invoice = $quote->replicate();
        $invoice->type = 'invoice';
        $invoice->status = 'draft';
        $invoice->sent_at = null;
        $invoice->paid_at = null;
        $invoice->date = now()->format('Y-m-d');
        $invoice->due_date = now()->addDays(30)->format('Y-m-d');
        $invoice->reference = $invoice->generateReference();
        $invoice->save();
        
        foreach ($quote->items as $item) {
            $newItem = $item->replicate();
            $newItem->invoice_id = $invoice->id;
            $newItem->save();
        }
        
        $invoice->calculateTotals();
        
        return redirect()->route('invoices.show', $invoice->id)
                        ->with('message', 'Devis converti en facture avec succès.');
    }

    public function downloadPdf(Invoice $invoice)
    {
        $this->authorize('view', $invoice);
        
        try {
            return $this->pdfService->downloadPdf($invoice);
        } catch (\Exception $e) {
            return back()->withErrors(['pdf' => 'Erreur lors de la génération du PDF: ' . $e->getMessage()]);
        }
    }
    
    public function previewPdf(Invoice $invoice)
    {
        $this->authorize('view', $invoice);
        
        try {
            return $this->pdfService->streamPdf($invoice);
        } catch (\Exception $e) {
            return back()->withErrors(['pdf' => 'Erreur lors de la génération du PDF: ' . $e->getMessage()]);
        }
    }
    
    public function sendEmail(Invoice $invoice)
    {
        $this->authorize('update', $invoice);
        
        try {
            $pdf = $this->pdfService->generateInvoicePdf($invoice);
            $filename = $this->pdfService->generateFilename($invoice);
            
            Mail::to($invoice->client->email)->send(
                new \App\Mail\InvoiceMail($invoice, $pdf->output(), $filename)
            );
            
            if ($invoice->status === 'draft') {
                $invoice->update([
                    'status' => 'sent',
                    'sent_at' => now()
                ]);
            }
            
            \Log::info("Email envoyé", [
                'invoice_id' => $invoice->id,
                'client_email' => $invoice->client->email,
                'type' => $invoice->type
            ]);
            
            return back()->with('message', 
                $invoice->type === 'quote' 
                    ? 'Devis envoyé avec succès à ' . $invoice->client->email
                    : 'Facture envoyée avec succès à ' . $invoice->client->email
            );
            
        } catch (\Exception $e) {
            \Log::error("Erreur envoi email", [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage()
            ]);
            
            return back()->withErrors([
                'email' => 'Erreur lors de l\'envoi: ' . $e->getMessage()
            ]);
        }
    }
}
