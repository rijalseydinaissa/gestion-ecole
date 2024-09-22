<?php

namespace App\Repository;

use App\Models\User;
use App\Facades\FirebaseUserFacade;

class FirebaseUserRepository implements UserRepositoryInterface
{
    public function create(array $data): User
    {
        $id = FirebaseUserFacade::create($data);
        $user = new User();
        $user->id = $id;
        $user->fill($data);
        return $user;
    }

    public function update($id, array $data)
    {
        FirebaseUserFacade::update($id, $data);
        return new User($data + ['id' => $id]);
    }

    public function find($id)
    {
        $userData = FirebaseUserFacade::find($id);
        return $userData ? new User($userData + ['id' => $id]) : null;
    }

    public function all()
    {
        return FirebaseUserFacade::all(); // Assurez-vous que cette méthode existe
    }

    public function delete($id)
    {
        FirebaseUserFacade::delete($id);
    }
    public function createWithAuthentication(array $userData) {
        FirebaseUserFacade::createWithAuthentication($userData);
    }
}