<?php

namespace App\Repository;

use App\Models\Referentiel;
use App\Facades\FirebaseReferentielFacade;

class FirebaseReferentielRepository
{
    public function create(array $data): Referentiel
    {
        $id = FirebaseReferentielFacade::create($data);
        $referentiel = new Referentiel();
        $referentiel->id = $id;
        $referentiel->fill($data);
        return $referentiel;
    }

    public function update($id, array $data)
    {
        FirebaseReferentielFacade::update($id, $data);
        return new Referentiel($data + ['id' => $id]);
    }

    public function find($id)
    {
        $referentielData = FirebaseReferentielFacade::find($id);
        return $referentielData ? new Referentiel($referentielData + ['id' => $id]) : null;
    }

    public function all()
    {
        return FirebaseReferentielFacade::all();
    }

    public function delete($id)
    {
        FirebaseReferentielFacade::delete($id);
    }
}
