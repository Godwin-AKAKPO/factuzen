<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $fillable = [
        'user_id',
        'client_id',
        'reference',
        'type',
        'status',
        'date',
        'due_date',
        'total_ht',
        'total_tva',
        'total_ttc',
        'notes',
        'sent_at',
        'paid_at',
    ];

    protected $casts = [
        'date' => 'date',
        'due_date' => 'date',
        'sent_at' => 'datetime',
        'paid_at' => 'datetime',
        'total_ht' => 'decimal:2',
        'total_tva' => 'decimal:2',
        'total_ttc' => 'decimal:2',
    ];
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function generateReference(): string
    {
        $prefix = $this->type === 'quote' ? 'DEV' : 'FACT';
        $year = date('Y');
        $count = static::where('user_id', $this->user_id)
                      ->where('type', $this->type)
                      ->whereYear('created_at', $year)
                      ->count() + 1;
        
        return sprintf('%s-%s-%03d', $prefix, $year, $count);
    }

    public function calculateTotals(): void
    {
        $totalHt = $this->items->sum('total_ht');
        $totalTva = $this->items->sum('total_tva');
        $totalTtc = $this->items->sum('total_ttc');

        $this->update([
            'total_ht' => $totalHt,
            'total_tva' => $totalTva,
            'total_ttc' => $totalTtc,
        ]);
    }

    public function isOverdue(): bool
    {
        return $this->status !== 'paid' && $this->due_date->isPast();
    }
}
