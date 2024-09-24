<?php

namespace App\Repository;

use App\Models\Apprenant;
use App\Facades\FirebaseApprenantFacade;

class FirebaseApprenantRepository implements ApprenantRepositoryInterface
{
    public function create(array $data)
    {
        $id = FirebaseApprenantFacade::create($data);
        $apprenant = new Apprenant();
        $apprenant->id = $id;
        $apprenant->fill($data);
        return $apprenant;
    }

    public function update($id, array $data)
    {
        FirebaseApprenantFacade::update($id, $data);
        return new Apprenant($data + ['id' => $id]);
    }

    public function find($id)
    {
        $apprenantData = FirebaseApprenantFacade::find($id);
        return $apprenantData ? new Apprenant($apprenantData + ['id' => $id]) : null;
    }

    public function all()
    {
        return FirebaseApprenantFacade::all();
    }

    public function delete($id): void
    {
        FirebaseApprenantFacade::delete($id);
    }
}
