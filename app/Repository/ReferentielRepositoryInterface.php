<?php

namespace App\Repository;

interface ReferentielRepositoryInterface
{
    public function create(array $data);
    public function update($id, array $data);
    public function find($id);
    public function all();
    public function delete($id);
}