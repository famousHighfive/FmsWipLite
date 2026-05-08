<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model
{
    /**
     * @use HasFactory<\Database\Factories\ArticleFactory>
     */
    use HasFactory;
    protected $fillable = [
        'user_id',
        'matricule',
        'first_name',
        'last_name',
        'birth_date',
        'phone',
        'email',
        'address',
        'position_id',
        'salary_base',
        'status'
    ];

    // les modifications sur ces champs
    protected $casts = [
        'birth_date' => 'date',
        'salary_base' => 'decimal:2',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($employee) {
            if (empty($employee->matricule)) {
                $employee->matricule = self::generateMatricule();
            }
        });
    }

    /**
     * Génère un matricule unique (ex: EMP-2026-0001)
     */
    public static function generateMatricule(): string
    {
        $year = date('Y');
        $prefix = 'EMP-' . $year . '-';
        
        $lastEmployee = self::where('matricule', 'like', $prefix . '%')
            ->orderBy('matricule', 'desc')
            ->first();

        if ($lastEmployee) {
            $lastNumber = intval(substr($lastEmployee->matricule, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return $prefix . $newNumber;
    }

    // Statuts disponibles
    const STATUS_ACTIF     = 'actif';
    const STATUS_INACTIF   = 'inactif';
    const STATUS_SUSPENDU  = 'suspendu';

    public static array $statuses = [
        self::STATUS_ACTIF,
        self::STATUS_INACTIF,
        self::STATUS_SUSPENDU,
    ];

    public function timesheets(): HasMany
    {
        return $this->hasMany(Timesheet::class);
    }

    public function timesheetEntries(): HasManyThrough
    {
        return $this->hasManyThrough(TimesheetEntry::class, Timesheet::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    /**
     * L'employé peut être lié à un compte utilisateur
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Historique des modifications de l'employé
     */
    public function histories(): HasMany
    {
        return $this->hasMany(EmployeeHistory::class)->latest();
    }


    // -------------------------------------------------------
    // Filtres réutilisables
    // -------------------------------------------------------

    /** Filtrer par statut */
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /** Seulement les employés actifs */
    public function scopeActif($query)
    {
        return $query->where('status', self::STATUS_ACTIF);
    }

    /** Filtrer par poste */
    public function scopeByPosition($query, int $positionId)
    {
        return $query->where('position_id', $positionId);
    }

    /** Recherche globale (nom, prénom, matricule, email) */
    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('first_name', 'like', "%{$term}%")
              ->orWhere('last_name',  'like', "%{$term}%")
              ->orWhere('matricule',  'like', "%{$term}%")
              ->orWhere('email',      'like', "%{$term}%");
        });
    }


    /** Nom complet de l'employé */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    
    public function planningAssignments(): HasMany
    {
        return $this->hasMany(PlanningAssignment::class);
    }
}
