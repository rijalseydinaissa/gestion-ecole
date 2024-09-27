<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;



class ApprenantsExport implements FromCollection
{
    protected $apprenants;

    public function __construct(Collection $apprenants)
    {
        $this->apprenants = $apprenants;
    }

    public function collection()
    {
        return $this->apprenants;
    }
}


