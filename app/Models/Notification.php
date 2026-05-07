<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Notification extends Model
{
    // On indique que l'ID est un UUID et non un entier auto-incrémenté
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'type',
        'notifiable_type',
        'notifiable_id',
        'data',
        'read_at'
    ];

    /**
     * Cast des attributs.
     * Le champ 'data' est automatiquement transformé en tableau PHP.
     */
    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    /**
     * L'entité qui reçoit la notification (souvent un User).
     * Usage: $notification->notifiable
     */
    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope pour filtrer les notifications non lues.
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }
    public function logs()
{
    return $this->morphMany(ActivityLog::class, 'model');
}
}
