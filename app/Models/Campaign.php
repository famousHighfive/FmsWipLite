<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'description', 'start_date', 'end_date', 'status'])]
class Campaign extends Model
{
    use HasFactory;

/**
 * Définit les moulages (casts) des attributs.
 * Cette méthode remplace la propriété protégée $casts.
 */
protected function casts(): array
{
    return [
        'start_date' => 'date:d/m/Y', // Transforme la chaîne SQL en objet Carbon (Date uniquement)
        'end_date' => 'date:d/m/Y',   // Permet de manipuler la date avec ->format() ou ->addDays()
    ];
}

  
    // Récupérer toutes les affectations de cette campagne
    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }


}
