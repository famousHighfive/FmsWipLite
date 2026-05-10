<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
    //
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // un employee peut avoir plusieurs affectations en cours
     public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }
}