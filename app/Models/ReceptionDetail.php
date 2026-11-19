<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class ReceptionDetail extends Model
{
    use LogsActivity;
    use HasFactory;

    protected $table = 'ReceptionDetail';

    protected $fillable = [
        'reception_id',
        'materiau_id',
        'quantite_declaree',
        'quantite_reelle',
        'prix_unitaire',
        'observation'
    ];

   // 👈 العلاقة التي كانت مفقودة وتسببت بالخطأ
  

   public function reception()
   {
       return $this->belongsTo(Reception::class, 'reception_id', 'id');
   }

   public function materiau()
   {
       return $this->belongsTo(Materiau::class, 'materiau_id', 'id');
   }
}