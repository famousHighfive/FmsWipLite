<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Position extends Model
{

      /**
     * @use HasFactory<\Database\Factories\ArticleFactory>
     */
    use HasFactory;
    //
    protected $fillable = ['name', 'code', 'description'];

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }
    public function logs()
{
    return $this->morphMany(ActivityLog::class, 'model');
}
}

