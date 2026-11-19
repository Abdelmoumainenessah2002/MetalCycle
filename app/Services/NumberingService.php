<?php

namespace App\Services;

use App\Models\Parametre;
use Illuminate\Support\Facades\DB;

class NumberingService
{
    /**
     * توليد الرقم التلقائي وقراءته من جدول parametres
     * النتيجة: 2026/00001
     */
    public static function generate(string $counterColumn): string
    {
        return DB::transaction(function () use ($counterColumn) {
            $param = Parametre::lockForUpdate()->first();

            if (!$param) {
                throw new \Exception("Le tableau 'parametres' est vide dans la base de données.");
            }

            // 1. زيادة العداد المحدد بمقدار 1
            $param->$counterColumn = $param->$counterColumn + 1;
            $param->save();

            // 2. تنسيق الرقم بالخانات المطلوبة
            $paddedNumber = str_pad($param->$counterColumn, $param->longueur, '0', STR_PAD_LEFT);

            // 3. الدمج والتوليد (مثل: 2026/00001)
            return "{$param->annee}{$param->separateur}{$paddedNumber}";
        });
    }


    public static function preview(string $counterColumn): string
    {
        $param = Parametre::first();

        if (!$param) {
            return '';
        }

        // حساب الرقم القادم (العداد الحالي + 1)
        $nextNumber = $param->$counterColumn + 1;
        $paddedNumber = str_pad($nextNumber, $param->longueur, '0', STR_PAD_LEFT);

        return "{$param->annee}{$param->separateur}{$paddedNumber}";
    }
}