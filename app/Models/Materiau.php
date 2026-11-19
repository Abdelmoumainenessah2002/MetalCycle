<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Materiau extends Model
{ 
    use LogsActivity;
    protected $table = 'Materiau';

    protected $fillable = [
        'code',
        'nom',
        'categorie',
        'unite',
        'description',
        'actif'
    ];


    // علاقة المادة بتفاصيل الاستلام (لمعرفة كل السندات التي استلمنا فيها هذه المادة)
    public function receptionDetails()
    {
        return $this->hasMany(ReceptionDetail::class, 'materiau_id');
    }

    // علاقة المادة بطلبات التجميع (DemandeCollecte) - بناءً على قاعدة بياناتك
    public function demandesCollecte()
    {
        return $this->hasMany(DemandeCollecte::class, 'materiau_id');
    }
}