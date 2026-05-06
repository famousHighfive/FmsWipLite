<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Timesheet extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id', 
        'period_start', 
        'period_end', 
        'status', 
        'validated_by', 
        'validated_at'
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'validated_at' => 'datetime',
    ];

    /**
     * L'employé à qui appartient la feuille de temps
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Le manager (employé) qui a validé
     */
    public function validator(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'validated_by');
    }

    /**
     * Les entrées quotidiennes liées
     */
    public function entries(): HasMany
    {
        return $this->hasMany(TimesheetEntry::class);
    }
}
