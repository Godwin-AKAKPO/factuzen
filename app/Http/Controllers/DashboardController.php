<?php

namespace App\Http\Controllers;

// Importation du modèle Client
use App\Models\Client;
// Importation du modèle Invoice (Facture)
use App\Models\Invoice;
// Importation de la classe Request pour gérer les requêtes HTTP
use Illuminate\Http\Request;
// Importation de la façade Inertia pour le rendu côté front-end
use Inertia\Inertia;
// Importation du type de retour Response d'Inertia
use Inertia\Response;

// Déclaration du contrôleur DashboardController qui hérite du contrôleur de base
class DashboardController extends Controller
{
    // Méthode principale pour afficher le tableau de bord
    public function index(Request $request): Response
    {
        // Récupération de l'utilisateur actuellement authentifié à partir de la requête
        $user = $request->user();

        // Calcul des statistiques générales pour l'utilisateur connecté
        $stats = [
            // Nombre total de clients associés à l'utilisateur
            'total_clients' => Client::where('user_id', $user->id)->count(),
            // Nombre total de factures associées à l'utilisateur
            'total_invoices' => Invoice::where('user_id', $user->id)->count(),
            // Nombre de factures en attente (envoyées ou en retard) pour l'utilisateur
            'pending_invoices' => Invoice::where('user_id', $user->id)
                                        ->whereIn('status', ['sent', 'overdue'])
                                        ->count(),
            // Somme totale des montants TTC des factures payées pour l'utilisateur
            'total_revenue' => Invoice::where('user_id', $user->id)
                                        ->where('status', 'paid')
                                        ->sum('total_ttc')
        ];

        // Initialisation d'un tableau pour stocker les revenus mensuels des 12 derniers mois
        $monthlyRevenue = [];
        // Boucle sur les 12 derniers mois (de 11 à 0)
        for ($i = 11; $i >= 0; $i--) {
            // Calcul de la date correspondant au mois courant de la boucle
            $date = now()->subMonths($i);
            // Calcul du revenu du mois courant (somme des factures payées sur ce mois)
            $revenue = Invoice::where('user_id', $user->id)
                                ->where('status', 'paid')
                                ->whereMonth('paid_at', $date->month)
                                ->whereYear('paid_at', $date->year)
                                ->sum('total_ttc');

            // Ajout des données du mois courant dans le tableau des revenus mensuels
            $monthlyRevenue[] = [
                // Formatage du mois (ex: Jan 2024)
                'month' => $date->format('M Y'),
                // Conversion du revenu en float pour éviter les problèmes de type
                'revenue' => (float) $revenue,
            ];
        }

        // Récupération des 5 dernières factures créées par l'utilisateur
        $recentInvoices = Invoice::with('client') // Chargement de la relation client
                                ->where('user_id', $user->id) // Filtre sur l'utilisateur
                                ->orderBy('created_at', 'desc') // Tri par date de création décroissante
                                ->limit(5) // Limite à 5 résultats
                                ->get() // Exécution de la requête et récupération des résultats
                                ->map(function ($invoice) { // Transformation de chaque facture
                                    return [
                                        // Identifiant de la facture
                                        'id' => $invoice->id,
                                        // Référence de la facture
                                        'reference' => $invoice->reference,
                                        // Nom du client associé à la facture
                                        'client_name' => $invoice->client->display_name,
                                        // Montant total TTC de la facture
                                        'total_ttc' => $invoice->total_ttc,
                                        // Statut de la facture (payée, envoyée, etc.)
                                        'status' => $invoice->status,
                                        // Date de la facture formatée (jour/mois/année)
                                        'date' => $invoice->date->format('d/m/Y'),
                                        // Indique si la facture est en retard
                                        'is_overdue' => $invoice->isOverdue(),
                                    ];
                                });

        // Récupération des factures en retard (non payées et dont la date d'échéance est dépassée)
        $overdueInvoices = Invoice::with('client') // Chargement de la relation client
                                 ->where('user_id', $user->id) // Filtre sur l'utilisateur
                                 ->where('status', '!=', 'paid') // Exclure les factures déjà payées
                                 ->where('due_date', '<', now()) // Factures dont la date d'échéance est passée
                                 ->orderBy('due_date', 'asc') // Tri par date d'échéance croissante
                                 ->get() // Exécution de la requête et récupération des résultats
                                 ->map(function ($invoice) { // Transformation de chaque facture
                                     return [
                                         // Identifiant de la facture
                                         'id' => $invoice->id,
                                         // Référence de la facture
                                         'reference' => $invoice->reference,
                                         // Nom du client associé à la facture
                                         'client_name' => $invoice->client->display_name,
                                         // Montant total TTC de la facture
                                         'total_ttc' => $invoice->total_ttc,
                                         // Date d'échéance formatée (jour/mois/année)
                                         'due_date' => $invoice->due_date->format('d/m/Y'),
                                         // Nombre de jours de retard (différence entre aujourd'hui et la date d'échéance)
                                         'days_overdue' => $invoice->due_date->diffInDays(now()),
                                     ];
                                 });

        // Retourne la vue Inertia 'Dashboard' avec toutes les données calculées
        return Inertia::render('Dashboard', [
            // Statistiques générales
            'stats' => $stats,
            // Revenus mensuels sur 12 mois
            'monthlyRevenue' => $monthlyRevenue,
            // 5 dernières factures
            'recentInvoices' => $recentInvoices,
            // Factures en retard
            'overdueInvoices' => $overdueInvoices,
        ]);
    }
}
