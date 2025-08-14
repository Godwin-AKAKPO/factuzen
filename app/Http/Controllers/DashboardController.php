<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request):Response
    {
        $user=$request->user();

        //Statistiques générales 
        $stats = [
            'total_clients'=> Client::where('user_id', $user->id)->count(),
            'total_invoices'=> Invoice::where('user_id', $user->id)-> count(),
            'pending_invoices'=> Invoice::where('user_id', $user->id)
                                        -> whereIn('status', ['sent', 'overdue'])
                                        -> count(),
            'total_revenue'=> Invoice::where('user_id', $user->id)
                                        -> where('status', 'paid')
                                        -> sum('total_ttc')    
        ];
        //Revenus mensuems (12 derniers mois)
        $monthlyRevenue = [];
        for ($i = 11; $i >= 0; $i--){
            $date = now()-> subMonths($i);
            $revenue = Invoice::where('user_id', $user-> id)
                                -> where('status', 'paid')
                                -> whereMonth('paid_at', $date-> month)
                                -> whereYear('paid_at', $date->year)
                                -> sum('total_ttc');

            $monthlyRevenue[]=[
                'month' => $date-> format('M Y'),
                'revenue'=>  (float) $revenue,
            ];
        }
        // Dernières factures
        $recentInvoices = Invoice::with('client')
                                ->where('user_id', $user->id)
                                ->orderBy('created_at', 'desc')
                                ->limit(5)
                                ->get()
                                ->map(function ($invoice) {
                                    return [
                                        'id' => $invoice->id,
                                        'reference' => $invoice->reference,
                                        'client_name' => $invoice->client->display_name,
                                        'total_ttc' => $invoice->total_ttc,
                                        'status' => $invoice->status,
                                        'date' => $invoice->date->format('d/m/Y'),
                                        'is_overdue' => $invoice->isOverdue(),
                                    ];
                                });
        
        // Factures en retard
        $overdueInvoices = Invoice::with('client')
                                 ->where('user_id', $user->id)
                                 ->where('status', '!=', 'paid')
                                 ->where('due_date', '<', now())
                                 ->orderBy('due_date', 'asc')
                                 ->get()
                                 ->map(function ($invoice) {
                                     return [
                                         'id' => $invoice->id,
                                         'reference' => $invoice->reference,
                                         'client_name' => $invoice->client->display_name,
                                         'total_ttc' => $invoice->total_ttc,
                                         'due_date' => $invoice->due_date->format('d/m/Y'),
                                         'days_overdue' => $invoice->due_date->diffInDays(now()),
                                     ];
                                 });


        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'monthlyRevenue' => $monthlyRevenue,
            'recentInvoices' => $recentInvoices,
            'overdueInvoices' => $overdueInvoices,
        ]);

    }
}
