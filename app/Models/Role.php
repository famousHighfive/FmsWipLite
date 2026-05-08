<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // <--- Importation importante
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory; // <--- Utilisation du trait indispensable

    protected $fillable = ['name'];

    public function users()
    {
        return $this->hasMany(User::class);
    }
    public function logs()
{
    return $this->morphMany(ActivityLog::class, 'model');
}
}
