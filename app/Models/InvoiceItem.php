<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Modèle représentant une ligne d'article de facture
class InvoiceItem extends Model
{
    // Définition des attributs pouvant être assignés en masse
    protected $fillable = [
        'invoice_id',    // Identifiant de la facture associée
        'description',   // Description de l'article
        'quantity',      // Quantité de l'article
        'unit_price',    // Prix unitaire
        'tva_rate',      // Taux de TVA appliqué
        'total_ht',      // Total hors taxes
        'total_tva',     // Montant de la TVA
        'total_ttc',     // Total toutes taxes comprises
    ];

    // Définition des types de données pour les attributs
    protected $casts = [
        'quantity' => 'integer',         // Quantité en entier
        'unit_price' => 'decimal:2',     // Prix unitaire en décimal (2 chiffres après la virgule)
        'tva_rate' => 'decimal:2',       // Taux de TVA en décimal
        'total_ht' => 'decimal:2',       // Total HT en décimal
        'total_tva' => 'decimal:2',      // Total TVA en décimal
        'total_ttc' => 'decimal:2',      // Total TTC en décimal
    ];

    // Définition de la relation avec la facture (Invoice)
    public function invoice(): BelongsTo
    {
        // Un article appartient à une facture
        return $this->belongsTo(Invoice::class);
    }

  //  Méthode pour calculer les totaux (HT, TVA, TTC)
    public function calculateTotals(): void
    {
        // Calcul du total hors taxes
        $this->total_ht = $this->quantity * $this->unit_price;
        // Calcul du montant de la TVA
        $this->total_tva = $this->total_ht * ($this->tva_rate / 100);
        // Calcul du total TTC
        $this->total_ttc = $this->total_ht + $this->total_tva;
    }

    // Méthode boot pour exécuter automatiquement le calcul avant sauvegarde
    protected static function boot()
    {
        parent::boot();
        
        // Avant chaque sauvegarde, recalculer les totaux
        static::saving(function ($item) {
            $item->calculateTotals();
        });
    }
}
