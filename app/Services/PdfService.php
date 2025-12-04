<?php
namespace App\Services;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PdfService
{
    /**
     * Générer le PDF d'une facture/devis
     */
    public function generateInvoicePdf(Invoice $invoice): \Barryvdh\DomPDF\PDF
    {
        // Charger les relations nécessaires
        $invoice->load(['client', 'items', 'user']);
        
        // Données pour le template
        $data = [
            'invoice' => $invoice,
            'company' => $this->getCompanyInfo(),
            'statusLabels' => $this->getStatusLabels(),
        ];
        
        // Configuration PDF
        $pdf = Pdf::loadView('pdf.invoice', $data);
        
        // Options spécifiques
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'DejaVu Sans'
        ]);
        
        return $pdf;
    }
    
    /**
     * Télécharger le PDF (retourne le contenu binaire avec headers)
     */
    public function downloadPdf(Invoice $invoice): \Symfony\Component\HttpFoundation\Response
    {
        $pdf = $this->generateInvoicePdf($invoice);
        $filename = $this->generateFilename($invoice);
        
        // Retourner le PDF avec les bons headers pour forcer le téléchargement
        return response($pdf->output())
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }
    
    /**
     * Streamer le PDF (affichage navigateur dans nouvel onglet)
     */
    public function streamPdf(Invoice $invoice): \Symfony\Component\HttpFoundation\Response
    {
        $pdf = $this->generateInvoicePdf($invoice);
        $filename = $this->generateFilename($invoice);
        
        return response($pdf->output())
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="' . $filename . '"')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate');
    }
    
    /**
     * Sauvegarder le PDF sur le disque
     */
    public function savePdf(Invoice $invoice, string $path = null): string
    {
        $pdf = $this->generateInvoicePdf($invoice);
        
        if (!$path) {
            $path = 'invoices/' . $this->generateFilename($invoice);
        }
        
        // Sauvegarder dans storage/app/public
        Storage::disk('public')->put($path, $pdf->output());
        
        return $path;
    }
    
    /**
     * Générer le nom de fichier
     */
    public function generateFilename(Invoice $invoice): string
    {
        $type = $invoice->type === 'quote' ? 'Devis' : 'Facture';
        $reference = str_replace(['/', '\\', ' '], '_', $invoice->reference);
        $date = now()->format('Y-m-d');
        
        return "{$type}_{$reference}_{$date}.pdf";
    }
    
    /**
     * Informations de l'entreprise (à récupérer depuis les paramètres)
     */
    private function getCompanyInfo(): object
    {
        // TODO: Récupérer depuis la table des paramètres entreprise
        return (object) [
            'name' => 'FactuPro',
            'address' => '123 Rue de l\'Innovation',
            'city' => 'Cotonou',
            'country' => 'Bénin',
            'email' => 'contact@factupro.com',
            'phone' => '+229 XX XX XX XX',
            'website' => 'www.factupro.com',
            'logo' => null, // Chemin vers le logo
            'siret' => null,
            'tva_number' => null,
        ];
    }
    
    /**
     * Labels des statuts
     */
    private function getStatusLabels(): array
    {
        return [
            'draft' => 'Brouillon',
            'sent' => 'Envoyé',
            'paid' => 'Payé',
            'overdue' => 'En retard',
            'cancelled' => 'Annulé',
        ];
    }
}