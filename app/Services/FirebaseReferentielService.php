<?php


namespace App\Services;

use Kreait\Firebase\Database;

class FirebaseReferentielService
{
    protected Database $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function all()
    {
        return $this->database->getReference('referentiels')->getValue();
    }

    public function find($id)
    {
        return $this->database->getReference("referentiels/$id")->getValue();
    }

    public function create(array $data)
    {
        $newReferentielRef = $this->database->getReference('referentiels')->push($data);
        return $newReferentielRef->getKey();
    }

    public function update($id, array $data)
    {
        $this->database->getReference("referentiels/$id")->update($data);
    }

    public function delete($id)
    {
        $this->database->getReference("referentiels/$id")->remove();
    }
}
