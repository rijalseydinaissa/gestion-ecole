<?php

namespace App\Services;

use App\Repository\FirebaseUserRepository;
use Illuminate\Support\Collection;
use Kreait\Firebase\Auth;

class FirebaseUserService implements UserServiceInterface
{
    protected FirebaseUserRepository $userRepository;
    // protected Auth $auth; // Le service Firebase Auth

    public function __construct(FirebaseUserRepository $userRepository, Auth $auth)
    {
        $this->userRepository = $userRepository;
        // $this->auth = Firebase::auth();
    }

    public function create(array $userData): string
    {
        $user = $this->userRepository->create($userData);
        return $user->getKey();  // Assurez-vous que `getKey()` retourne l'identifiant Firebase
    }

    public function update(string $id, array $userData): void
    {
        $this->userRepository->update($id, $userData);
    }

    public function find(string $id): ?array
    {
        $user = $this->userRepository->find($id);
        return $user ? $user->toArray() : null;
    }

    public function all(): array
    {
        $users = $this->userRepository->all();
        // Utiliser la méthode toArray si c'est une collection Laravel
        return $users instanceof Collection ? $users->toArray() : $users;
    }

    public function delete(string $id): void
    {
        $this->userRepository->delete($id);
    }
   
  

   
}
