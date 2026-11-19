<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Fournisseur extends Model
{
    use LogsActivity;
    protected $table = 'Fournisseur';

    protected $fillable = [
        'code_fournisseur',
        'nom_fournisseur',
        'filiale_id',
        'adresse',
        'telephone',
        'fax',
        'email',
        'contact',
        'remarque',
        'fournisseur_bloque'
    ];

    // المورد ينتمي إلى فرع معين
    public function filiale()
    {
        return $this->belongsTo(Filiale::class, 'filiale_id');
    }
}
