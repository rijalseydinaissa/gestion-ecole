<?php

namespace App\Services;

use App\Repository\FirebaseRepository;

class FirebaseService implements FirebaseServiceInterface
{
    protected $repository;

    public function __construct(FirebaseRepository $repository)
    {
        $this->repository = $repository;
    }

    public function create(string $model, array $attributes): string
    {
        return $this->repository->create($model, $attributes);
    }

    public function update(string $model, string $id, array $attributes): void
    {
        $this->repository->update($model, $id, $attributes);
    }

    public function find(string $model, string $id): ?array
    {
        return $this->repository->find($model, $id);
    }

    public function all(string $model): array
    {
        return $this->repository->all($model);
    }

    public function delete(string $model, string $id): void
    {
        $this->repository->delete($model, $id);
    }
    public function getActivePromotion(string $collection){
        // Récupérer la promotion active
        $promotions = $this->all($collection);
        $promotionActive = null;

        foreach ($promotions as $promotion) {
            if ($promotion['etat']=='Actif') {
                $promotionActive = $promotion;
                break;
            }
        }

        return $promotionActive;
    }
    public function deactivateOtherPromotions(): void{

        
    }
    // public function createWithAuthentication($userData){
    //     return $this->repository->createWithAuthentication($userData);
    // }
}
