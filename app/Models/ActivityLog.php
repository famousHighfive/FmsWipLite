<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLog extends Model
{
    // Pas besoin de updated_at pour des logs, vous pouvez désactiver si vous voulez
    // public $timestamps = ["created_at"];

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'description',
        'ip_address'
    ];

    /**
     * L'utilisateur qui a réalisé l'action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation polymorphique.
     * Permet de récupérer l'objet lié (ex: l'employé ou la campagne concernée).
     * Usage: $log->model
     */
    public function model(): MorphTo
    {
        return $this->morphTo();
    }
}
