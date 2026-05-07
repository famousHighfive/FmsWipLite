<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Employee extends Model
{
    /**
     * @use HasFactory<\Database\Factories\ArticleFactory>
     */
    use HasFactory;
     protected $fillable = [
        'user_id', 'matricule', 'first_name', 'last_name',
        'birth_date', 'phone', 'email', 'address',
        'position_id', 'salary_base', 'status'
    ];
    
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function planningAssignments(): HasMany
    {
        return $this->hasMany(PlanningAssignment::class);
    }
}
