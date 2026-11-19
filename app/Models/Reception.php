<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Reception extends Model
{
    use LogsActivity;
    use HasFactory;

    protected $table = 'Reception';

    protected $dateFormat = 'Y-m-d\TH:i:s.v'; 

    protected $casts = [
        'date_validation' => 'datetime',
    ];

    protected $fillable = [
        'numero',
        'mission_collecte_id',
        'fournisseur_id',
        'type_reception',
        'date_reception',
        'poids_fournisseur',
        'poids_entree',
        'poids_sortie',
        'poids_net',
        'controleur_id',
        'decision_controle',
        'observation_controle',
        'statut',
        'observation',
        'zone_stockage_id',
        'date_validation',
    ];

    public function mission()
    {
        return $this->belongsTo(MissionCollecte::class, 'mission_collecte_id');
    }

    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class, 'fournisseur_id');
    }

    public function controleur()
    {
        return $this->belongsTo(Person::class, 'controleur_id');
    }

    public function details()
    {
        return $this->hasMany(ReceptionDetail::class, 'reception_id');
    }
    public function zoneStockage()
{
    return $this->belongsTo(ZoneStockage::class, 'zone_stockage_id');
}

public function receptionDetails()
    {
        return $this->hasMany(ReceptionDetail::class, 'reception_id');
    }
}