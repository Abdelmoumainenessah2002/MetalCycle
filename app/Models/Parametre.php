<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parametre extends Model
{
    protected $table = 'parametres';

    // 🟢 إيقاف التعامل مع created_at و updated_at
    public $timestamps = false;

    protected $fillable = [
        'annee',
        'separateur',
        'longueur',
        'compteur_demande',
        'compteur_mission',
        'compteur_reception',
    ];
}