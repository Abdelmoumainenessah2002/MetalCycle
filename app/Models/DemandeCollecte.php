<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;
class DemandeCollecte extends Model
{
    use LogsActivity;
    use HasFactory;

    // اسم الجدول في قاعدة البيانات
    protected $table = 'DemandeCollecte';

    // الحقول المسموح بحفظها وتعديلها (Mass Assignment)
    protected $fillable = [
        'numero',
        'fournisseur_id',
        'materiau_id',
        'quantite_estimee',
        'date_demande',
        'adresse_collecte',
        'contact',
        'telephone',
        'statut',
        'observation',
        'motif_annulation',
    ];

    // علاقة طلب الجمع مع المورد (كل طلب ينتمي إلى مورد واحد)
    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class, 'fournisseur_id');
    }

    // علاقة طلب الجمع مع المادة/المنتج (كل طلب يحتوي على مادة واحدة)
    public function materiau()
    {
        return $this->belongsTo(Materiau::class, 'materiau_id');
    }

    


}
