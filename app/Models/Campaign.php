<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'description', 'start_date', 'end_date', 'status'])]
class Campaign extends Model
{

    // Relation : Une campagne a plusieurs affectations
    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }


}
