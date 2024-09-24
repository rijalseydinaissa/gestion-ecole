<?php
namespace App\Repository;

use Kreait\Firebase\Database;

class FirebaseRepository
{
    protected $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function create(string $model, array $attributes): string
    {
        $newRef = $this->database->getReference($model)->push($attributes);
        return $newRef->getKey(); // Retourne l'ID généré par Firebase
    }

  
    public function update(string $model, string $id, array $attributes): void
    {
        $this->database->getReference("{$model}/{$id}")->update($attributes);
    }

    
    public function find(string $model, string $id): ?array
    {
        $data = $this->database->getReference("{$model}/{$id}")->getValue();
        return $data ? $data : null;
    }

    public function all(string $model): array
    {
        $data = $this->database->getReference($model)->getValue();
        return $data ? $data : [];
    }

    public function delete(string $model, string $id): void
    {
        $this->database->getReference("{$model}/{$id}")->remove();
    }
   
}
