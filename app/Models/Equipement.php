<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Equipement extends Model
{
    use LogsActivity;
    protected $table = 'Equipement'; // اسم الجدول في قاعدة البيانات

    protected $fillable = [
        'code',
        'nom',
        'type',
        'marque',
        'modele',
        'immatriculation',
        'numero_serie',
        'etat',
        'actif'
    ];
}