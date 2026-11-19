<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class MissionCollecte extends Model
{
    use LogsActivity;
    use HasFactory;

    protected $table = 'MissionCollecte';

    protected $fillable = [
        'numero',
        'chauffeur_id',
        'equipement_id',
        'date_depart_prevue',
        'date_retour_prevue',
        'date_depart',
        'date_retour',
        'statut',
        'observation'
    ];

    public function chauffeur()
    {
        return $this->belongsTo(Person::class, 'chauffeur_id');
    }

    public function equipement()
    {
        return $this->belongsTo(Equipement::class, 'equipement_id');
    }

    // علاقة متعدد لمتعدد (تُرجع مجموعة من الطلبات)
    public function demandes()
    {
        return $this->belongsToMany(
            DemandeCollecte::class, 
            'MissionCollecteDemande', 
            'mission_collecte_id', 
            'demande_collecte_id'
        );
    }

    // 🟢 العلاقة المطلوبة للصفحة (تُرجع الطلب المرتبط الرئيسي/الأخير كعنصر مفرد)
 

    public function fournisseur()
    {
        return $this->hasOneThrough(
            Fournisseur::class,
            DemandeCollecte::class,
            'id', 
            'id', 
            'id', 
            'fournisseur_id'
        )->latestOfMany(); 
    }
}