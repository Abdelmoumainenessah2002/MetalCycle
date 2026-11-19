<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class ZoneStockage extends Model
{
    use LogsActivity;
    // اسم الجدول في قاعدة بياناتك
    protected $table = 'ZoneStockage';

    // الحقول المسموح بتعبئتها
    protected $fillable = [
        'code',
        'nom',
        'capacite_max',
        'unite',
        'description',
        'actif'
    ];

    // بما أنك تستخدم SQL Server DEFAULT GETDATE()، سنعطل الـ timestamps التلقائي
    public $timestamps = false;


    public function stocks()
    {
        return $this->hasMany(StockMP::class, 'zone_stockage_id', 'id');
    }
}