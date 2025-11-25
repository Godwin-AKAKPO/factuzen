<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $isQuote ? 'Devis' : 'Facture' }} {{ $invoice->reference }}</title>
    <style>
        /* Reset CSS */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333333;
            background-color: #f8fafc;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        /* Header */
        .email-header {
            background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
            color: white;
            padding: 30px 40px;
            text-align: center;
        }
        
        .logo {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .header-subtitle {
            font-size: 16px;
            opacity: 0.9;
        }
        
        /* Contenu principal */
        .email-body {
            padding: 40px;
        }
        
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
            color: #1f2937;
        }
        
        .message-content {
            font-size: 16px;
            line-height: 1.8;
            margin-bottom: 30px;
            color: #4b5563;
        }
        
        /* Carte facture */
        .invoice-card {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 8px;
            padding: 25px;
            margin: 25px 0;
        }
        
        .invoice-title {
            font-size: 20px;
            font-weight: bold;
            color: #16a34a;
            margin-bottom: 15px;
            text-align: center;
        }
        
        .invoice-details {
            display: table;
            width: 100%;
        }
        
        .invoice-details-row {
            display: table-row;
        }
        
        .invoice-details-cell {
            display: table-cell;
            padding: 8px 12px;
            border-bottom: 1px solid #dcfce7;
        }
        
        .invoice-details-label {
            font-weight: 600;
            color: #166534;
            width: 40%;
        }
        
        .invoice-details-value {
            color: #15803d;
        }
        
        .total-amount {
            background: #16a34a;
            color: white;
            text-align: center;
            padding: 15px;
            border-radius: 6px;
            margin-top: 15px;
        }
        
        .total-amount .label {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .total-amount .amount {
            font-size: 24px;
            font-weight: bold;
            margin-top: 5px;
        }
        
        /* Informations expéditeur */
        .sender-info {
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        
        .sender-info h3 {
            color: #16a34a;
            font-size: 16px;
            margin-bottom: 10px;
        }
        
        .sender-info p {
            color: #6b7280;
            font-size: 14px;
            margin: 3px 0;
        }
        
        /* Boutons d'action */
        .action-buttons {
            text-align: center;
            margin: 30px 0;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 24px;
            margin: 0 10px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background: #16a34a;
            color: white;
        }
        
        .btn-secondary {
            background: #f3f4f6;
            color: #374151;
            border: 1px solid #d1d5db;
        }
        
        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        /* Informations supplémentaires */
        .additional-info {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px 20px;
            margin: 25px 0;
        }
        
        .additional-info h3 {
            color: #92400e;
            font-size: 16px;
            margin-bottom: 8px;
        }
        
        .additional-info p {
            color: #78350f;
            font-size: 14px;
            margin: 5px 0;
        }
        
        /* Footer */
        .email-footer {
            background: #f9fafb;
            padding: 30px 40px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        
        .contact-info {
            margin-bottom: 20px;
        }
        
        .contact-info h3 {
            color: #374151;
            font-size: 16px;
            margin-bottom: 10px;
        }
        
        .contact-info p {
            color: #6b7280;
            font-size: 14px;
            margin: 3px 0;
        }
        
        .footer-links {
            margin-top: 20px;
        }
        
        .footer-links a {
            color: #16a34a;
            text-decoration: none;
            margin: 0 15px;
            font-size: 14px;
        }
        
        .footer-note {
            margin-top: 20px;
            font-size: 12px;
            color: #9ca3af;
            line-height: 1.5;
        }
        
        /* Responsive */
        @media (max-width: 600px) {
            .email-body {
                padding: 20px;
            }
            
            .email-header {
                padding: 20px;
            }
            
            .invoice-card {
                padding: 15px;
            }
            
            .btn {
                display: block;
                margin: 10px 0;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">   
        <div class="email-header">
            <div class="logo">FactureZen</div>
            <div class="header-subtitle">Solution de facturation simplifiée</div>
        </div>
        <div class="email-body">
            <!-- Salutation -->
            <div class="greeting">
                Bonjour {{ $client->name }},
            </div>
            <div class="message-content">
                @if($isQuote)
                    <p>Nous avons le plaisir de vous transmettre notre devis pour les prestations demandées.</p>
                    <p>Vous trouverez ci-joint le devis détaillé en format PDF. N'hésitez pas à nous contacter si vous avez des questions ou des modifications à apporter.</p>
                @else
                    <p>Veuillez trouver ci-joint votre facture en format PDF.</p>
                    <p>Le paiement est attendu avant le <strong>{{ \Carbon\Carbon::parse($invoice->due_date)->format('d/m/Y') }}</strong>. 
                    Merci de nous faire parvenir votre règlement dans les délais impartis.</p>
                @endif
            </div>

            <div class="sender-info">
                <h3>📧 Envoyé par :</h3>
                <p><strong>{{ $invoice->user->name }}</strong></p>
                <p>{{ $invoice->user->email }}</p>
                @if($invoice->user->phone ?? false)
                    <p>📞 {{ $invoice->user->phone }}</p>
                @endif
            </div>
            
            
            <div class="invoice-card">
                <div class="invoice-title">
                    {{ $isQuote ? 'DEVIS' : 'FACTURE' }} #{{ $invoice->reference }}
                </div>
                
                <div class="invoice-details">
                    <div class="invoice-details-row">
                        <div class="invoice-details-cell invoice-details-label">Date d'émission:</div>
                        <div class="invoice-details-cell invoice-details-value">
                            {{ \Carbon\Carbon::parse($invoice->date)->format('d/m/Y') }}
                        </div>
                    </div>
                    
                    <div class="invoice-details-row">
                        <div class="invoice-details-cell invoice-details-label">
                            {{ $isQuote ? 'Valable jusqu\'au:' : 'Date d\'échéance:' }}
                        </div>
                        <div class="invoice-details-cell invoice-details-value">
                            {{ \Carbon\Carbon::parse($invoice->due_date)->format('d/m/Y') }}
                        </div>
                    </div>
                    
                    <div class="invoice-details-row">
                        <div class="invoice-details-cell invoice-details-label">Client:</div>
                        <div class="invoice-details-cell invoice-details-value">
                            {{ $client->name }}
                            @if($client->company)
                                <br><small>{{ $client->company }}</small>
                            @endif
                        </div>
                    </div>
                    
                    <div class="invoice-details-row">
                        <div class="invoice-details-cell invoice-details-label">Nombre d'articles:</div>
                        <div class="invoice-details-cell invoice-details-value">{{ $invoice->items->count() }}</div>
                    </div>
                </div>
                
                <div class="total-amount">
                    <div class="label">{{ $isQuote ? 'Montant du devis' : 'Montant total TTC' }}</div>
                    <div class="amount">{{ number_format($invoice->total_ttc, 0, ',', ' ') }} FCFA</div>
                </div>
            </div>
            
            <!-- Informations spécifiques -->
            <!-- @if($isQuote)
                <div class="additional-info">
                    <h3>📋 Informations importantes</h3>
                    <p>• Ce devis est valable jusqu'au {{ \Carbon\Carbon::parse($invoice->due_date)->format('d/m/Y') }}</p>
                    <p>• Les prix indiqués sont nets et peuvent être révisés en cas de modification</p>
                    <p>• Pour accepter ce devis, merci de nous renvoyer un exemplaire signé avec la mention "Bon pour accord"</p>
                </div>
            @else
                <div class="additional-info">
                    <h3>💳 Modalités de paiement</h3>
                    <p>• Paiement par virement bancaire, chèque ou espèces</p>
                    <p>• En cas de retard, des pénalités de 3 fois le taux légal s'appliqueront</p>
                    <p>• Merci de mentionner la référence {{ $invoice->reference }} lors du paiement</p>
                </div>
            @endif -->
            
            <!-- Boutons d'action -->
            <div class="action-buttons">
                @if($isQuote)
                    <a href="mailto:{{ $invoice->user->email }}?subject=Acceptation devis {{ $invoice->reference }}" class="btn btn-primary">
                        ✅ Accepter le devis
                    </a>
                    <a href="mailto:{{ $invoice->user->email }}?subject=Question devis {{ $invoice->reference }}" class="btn btn-secondary">
                        ❓ Poser une question
                    </a>
                @else
                    <a href="mailto:{{ $invoice->user->email }}?subject=Confirmation paiement {{ $invoice->reference }}" class="btn btn-primary">
                        ✅ Confirmer le paiement
                    </a>
                    <a href="mailto:{{ $invoice->user->email }}?subject=Question facture {{ $invoice->reference }}" class="btn btn-secondary">
                        ❓ Une question ?
                    </a>
                @endif
            </div>
            
            <!-- Message de clôture -->
            <div class="message-content">
                <p>Nous restons à votre disposition pour tout complément d'information.</p>
                <p>Cordialement,<br><strong>{{ $invoice->user->name }}</strong></p>
            </div>
            
        </div>
        
        <!-- Footer -->
        <div class="email-footer">
            <div class="contact-info">
                <h3>Informations de contact</h3>
                <p><strong>{{ $invoice->user->name }}</strong></p>
                <p><strong>Email:</strong> {{ $invoice->user->email }}</p>
                @if($invoice->user->phone ?? false)
                    <p><strong>Téléphone:</strong> {{ $invoice->user->phone }}</p>
                @endif
            </div>
            
            <div class="footer-links">
                <a href="#" style="color: #16a34a;">Conditions générales</a>
                <a href="#" style="color: #16a34a;">Politique de confidentialité</a>
                <a href="#" style="color: #16a34a;">Support</a>
            </div>
            
            <div class="footer-note">
                <p>Cet email a été envoyé automatiquement depuis FactureZen.</p>
                <p>Pour toute question, répondez directement à cet email ou contactez {{ $invoice->user->email }}</p>
            </div>
        </div>
        
    </div>
</body>
</html>