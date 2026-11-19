<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Filiale extends Model
{
    use LogsActivity;
    protected $table = 'Filiale'; 

    protected $fillable = [
        'code_filiale',
        'nom_filiale',
        'type_filiale',
        'filiale_bloque'
    ];

    // حماية التواريخ لـ SQL Server
    protected $dateFormat = 'Y-m-d H:i:s';

    public function fournisseurs()
    {
        return $this->hasMany(Fournisseur::class, 'filiale_id');
    }
}