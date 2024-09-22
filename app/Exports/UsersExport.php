<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;

class UsersExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $users = app('App\Services\FirebaseServiceInterface')->all('users');
        
        // Si nécessaire, vous pouvez transformer les données
        return collect($users);
    }
}
