<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class StockMP extends Model
{
    use LogsActivity;
    protected $table = 'StockMP';

    protected $fillable = [
        'materiau_id',      // 👈 العمود الجديد
        'zone_stockage_id',
        'quantite',
        'prix_moyen',        // 👈 العمود الجديد
        'date_entree',
        'statut'
    ];

    // 👈 العلاقة الجديدة مع المادة
    public function materiau()
    {
        return $this->belongsTo(Materiau::class, 'materiau_id', 'id');
    }

    public function zoneStockage()
    {
        return $this->belongsTo(ZoneStockage::class, 'zone_stockage_id', 'id');
    }
}