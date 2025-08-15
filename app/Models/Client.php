<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    // Les attributs pouvant être assignés en masse
    protected $fillable = [
        'user_id',
        'name',
        'company',
        'email',
        'phone',
        'address',
    ]; 

    // Relations

    // Relation : Un client appartient à un utilisateur
    public function user(): BelongsTo 
    {
        return $this->belongsTo(User::class);
    }

    // Relation : Un client peut avoir plusieurs factures
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    // Accesseurs

    // Accesseur pour obtenir un nom d'affichage personnalisé du client
    public function getDisplayNameAttribute(): string
    {
        // Si la société existe, retourne "nom (société)", sinon retourne juste le nom
        return $this->company ?  "{$this->name} ({$this->company})" : $this->name;
    }
}
