<?php

namespace App\Imports;

use App\Models\User;
use App\Models\UserLocal;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Services\FirebaseServiceInterface;



class UsersImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new User([
            'nom'       => $row['nom'],
            'prenom'    => $row['prenom'],
            'adresse'   => $row['adresse'],
            'password'  => $row['password'],
            'telephone' => $row['telephone'],
            'fonction'  => $row['fonction'],
            'email'     => $row['email'],
            'statut'    => $row['statut'],
            'role'      => $row['role'],
        ]);
    }
}
