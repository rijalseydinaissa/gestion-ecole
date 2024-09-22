<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

class PromotionsExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $promotions = app('App\Services\FirebaseServiceInterface')->all('promotions');
        
        // Si nécessaire, vous pouvez transformer les données
        return collect($promotions);
    }
}

