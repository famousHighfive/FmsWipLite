<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'description', 'start_date', 'end_date', 'status'])]
class Campaign extends Model
{
    use HasFactory;
    // Récupérer toutes les affectations de cette campagne
    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }


}
