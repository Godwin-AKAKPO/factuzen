<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $invoice->type === 'quote' ? 'Devis' : 'Facture' }} {{ $invoice->reference }}</title>
    <style>
        /* Reset et base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            background: #fff;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        /* Header avec logo et infos entreprise */
        .header {
            display: table;
            width: 100%;
            margin-bottom: 30px;
            border-bottom: 2px solid #16a34a;
            padding-bottom: 20px;
        }
        
        .header-left {
            display: table-cell;
            width: 60%;
            vertical-align: top;
        }
        
        .header-right {
            display: table-cell;
            width: 40%;
            vertical-align: top;
            text-align: right;
        }
        
        .company-logo {
            max-width: 150px;
            max-height: 80px;
            margin-bottom: 10px;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #16a34a;
            margin-bottom: 5px;
        }
        
        .company-info {
            font-size: 11px;
            color: #666;
            line-height: 1.4;
        }
        
        /* Titre du document */
        .document-title {
            font-size: 28px;
            font-weight: bold;
            color: #16a34a;
            text-align: right;
            margin-bottom: 5px;
        }
        
        .document-reference {
            font-size: 14px;
            color: #666;
            text-align: right;
        }
        
        /* Informations facture/devis */
        .invoice-info {
            display: table;
            width: 100%;
            margin: 30px 0;
        }
        
        .billing-info {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 20px;
        }
        
        .invoice-details {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-left: 20px;
        }
        
        .info-section {
            margin-bottom: 20px;
        }
        
        .info-title {
            font-size: 14px;
            font-weight: bold;
            color: #16a34a;
            margin-bottom: 8px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 3px;
        }
        
        .info-content {
            font-size: 11px;
            line-height: 1.5;
        }
        
        .client-name {
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 3px;
        }
        
        /* Tableau des articles */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
            font-size: 11px;
        }
        
        .items-table th {
            background-color: #f0fdf4;
            border: 1px solid #bbf7d0;
            padding: 10px 8px;
            text-align: left;
            font-weight: bold;
            color: #166534;
        }
        
        .items-table td {
            border: 1px solid #e5e7eb;
            padding: 8px;
            vertical-align: top;
        }
        
        .items-table tr:nth-child(even) {
            background-color: #f9fafb;
        }
        
        /* Alignements des colonnes */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        
        /* Totaux */
        .totals {
            margin-top: 20px;
            float: right;
            width: 300px;
        }
        
        .totals table {
            width: 100%;
            font-size: 12px;
        }
        
        .totals table td {
            padding: 5px 10px;
            border: none;
        }
        
        .totals .total-line {
            border-top: 1px solid #e5e7eb;
            font-weight: bold;
        }
        
        .totals .final-total {
            background-color: #16a34a;
            color: white;
            font-weight: bold;
            font-size: 14px;
        }
        
        /* Notes */
        .notes {
            clear: both;
            margin-top: 40px;
            padding: 15px;
            background-color: #f0fdf4;
            border-left: 4px solid #16a34a;
        }
        
        .notes-title {
            font-weight: bold;
            margin-bottom: 8px;
            color: #16a34a;
        }
        
        .notes-content {
            font-size: 11px;
            line-height: 1.5;
            white-space: pre-wrap;
        }
        
        /* Statut */
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .status-draft { background-color: #f3f4f6; color: #374151; }
        .status-sent { background-color: #dbeafe; color: #1d4ed8; }
        .status-paid { background-color: #dcfce7; color: #166534; }
        .status-overdue { background-color: #fee2e2; color: #dc2626; }
        .status-cancelled { background-color: #f3f4f6; color: #6b7280; }
        
        /* Footer */
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 15px;
        }
        
        /* Conditions de paiement */
        .payment-terms {
            margin-top: 30px;
            font-size: 10px;
            color: #6b7280;
            line-height: 1.4;
        }
        
        /* Éléments spéciaux pour devis */
        .quote-validity {
            background-color: #fef3c7;
            border: 1px solid #f59e0b;
            padding: 10px;
            margin: 20px 0;
            border-radius: 4px;
        }
        
        .quote-validity strong {
            color: #92400e;
        }
        
        /* Page break */
        .page-break {
            page-break-after: always;
        }
        
        /* Print optimizations */
        @media print {
            body {
                font-size: 11px;
            }
            .container {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        
        <!-- Header avec logo et infos entreprise -->
        <div class="header">
            <div class="header-left">
                <div class="company-name">
                    FactureZen
                </div>
                
                <div class="company-info">
                    <!-- Informations de l'utilisateur qui a créé la facture -->
                    <strong>{{ $invoice->user->name }}</strong><br>
                    <strong>Email:</strong> {{ $invoice->user->email }}<br>
                    @if($invoice->user->phone ?? false)
                        <strong>Tél:</strong> {{ $invoice->user->phone }}<br>
                    @endif
                    @if($invoice->user->address ?? false)
                        {{ $invoice->user->address }}<br>
                    @endif
                    @if($invoice->user->city ?? false)
                        {{ $invoice->user->city }}<br>
                    @endif
                </div>
            </div>
            
            <div class="header-right">
                <div class="document-title">
                    {{ $invoice->type === 'quote' ? 'DEVIS' : 'FACTURE' }}
                </div>
                <div class="document-reference">
                    N° {{ $invoice->reference }}
                </div>
                <!-- <div style="margin-top: 10px;">
                    <span class="status-badge status-{{ $invoice->status }}">
                        {{ $statusLabels[$invoice->status] ?? $invoice->status }}
                    </span>
                </div> -->
            </div>
        </div>
        
        <!-- Informations facture et client -->
        <div class="invoice-info">
            <div class="billing-info">
                <div class="info-section">
                    <div class="info-title">FACTURER À</div>
                    <div class="info-content">
                        <div class="client-name">{{ $invoice->client->name }}</div>
                        @if($invoice->client->company)
                            <div>{{ $invoice->client->company }}</div>
                        @endif
                        @if($invoice->client->address)
                            <div>{{ $invoice->client->address }}</div>
                        @endif
                        <div>{{ $invoice->client->email }}</div>
                        @if($invoice->client->phone)
                            <div>{{ $invoice->client->phone }}</div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="invoice-details">
                <div class="info-section">
                    <div class="info-title">DÉTAILS</div>
                    <div class="info-content">
                        <table style="width: 100%; font-size: 11px;">
                            <tr>
                                <td><strong>Date d'émission:</strong></td>
                                <td>{{ \Carbon\Carbon::parse($invoice->date)->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <td><strong>{{ $invoice->type === 'quote' ? 'Valable jusqu\'au:' : 'Échéance:' }}</strong></td>
                                <td>{{ \Carbon\Carbon::parse($invoice->due_date)->format('d/m/Y') }}</td>
                            </tr>
                            @if($invoice->sent_at)
                            <tr>
                                <td><strong>Envoyé le:</strong></td>
                                <td>{{ \Carbon\Carbon::parse($invoice->sent_at)->format('d/m/Y') }}</td>
                            </tr>
                            @endif
                            @if($invoice->paid_at)
                            <tr>
                                <td><strong>Payé le:</strong></td>
                                <td>{{ \Carbon\Carbon::parse($invoice->paid_at)->format('d/m/Y') }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Alerte validité devis -->
        @if($invoice->type === 'quote')
        <div class="quote-validity">
            <strong>⏰ Validité du devis:</strong> Ce devis est valable jusqu'au {{ \Carbon\Carbon::parse($invoice->due_date)->format('d/m/Y') }}.
            Au-delà de cette date, les prix et conditions peuvent être révisés.
        </div>
        @endif
        
        <!-- Tableau des articles -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 50%;">Description</th>
                    <th style="width: 8%;" class="text-center">Qté</th>
                    <th style="width: 12%;" class="text-right">Prix unitaire</th>
                    <th style="width: 8%;" class="text-center">TVA</th>
                    <th style="width: 12%;" class="text-right">Total HT</th>
                    <th style="width: 10%;" class="text-right">TVA</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->unit_price, 0, ',', ' ') }} FCFA</td>
                    <td class="text-center">{{ $item->tva_rate }}%</td>
                    <td class="text-right">{{ number_format($item->total_ht, 0, ',', ' ') }} FCFA</td>
                    <td class="text-right">{{ number_format($item->total_tva, 0, ',', ' ') }} FCFA</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <!-- Totaux -->
        <div class="totals">
            <table>
                <tr>
                    <td><strong>Total HT:</strong></td>
                    <td class="text-right">{{ number_format($invoice->total_ht, 0, ',', ' ') }} FCFA</td>
                </tr>
                <tr>
                    <td><strong>Total TVA:</strong></td>
                    <td class="text-right">{{ number_format($invoice->total_tva, 0, ',', ' ') }} FCFA</td>
                </tr>
                <tr class="final-total">
                    <td><strong>TOTAL TTC:</strong></td>
                    <td class="text-right"><strong>{{ number_format($invoice->total_ttc, 0, ',', ' ') }} FCFA</strong></td>
                </tr>
            </table>
        </div>
        
        <!-- Notes -->
        @if($invoice->notes)
        <div class="notes">
            <div class="notes-title">NOTES:</div>
            <div class="notes-content">{{ $invoice->notes }}</div>
        </div>
        @endif
        

        
        <!-- Footer -->
        <div class="footer">
            {{ $invoice->type === 'quote' ? 'Devis' : 'Facture' }} généré{{ $invoice->type === 'quote' ? '' : 'e' }} le {{ \Carbon\Carbon::now()->format('d/m/Y à H:i') }} 
            • FactureZen - Solution de facturation simplifiée
        </div>
        
    </div>
</body>
</html>