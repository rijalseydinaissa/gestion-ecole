<?php

namespace App\Services;

use App\Repository\PromotionRepositoryInterface;
use App\Models\Promotion;


class FirebasePromotionService implements PromotionServiceInterface
{
    protected PromotionRepositoryInterface $promotionRepository;

    public function __construct(PromotionRepositoryInterface $promotionRepository)
    {
        $this->promotionRepository = $promotionRepository;
    }

    public function create(array $data): string
    {
        $promotion = $this->promotionRepository->create($data);
        return $promotion->getKey();
    }

    public function update(string $id, array $data): void
    {
        $this->promotionRepository->update($id, $data);
    }

    public function find(string $id): ?array
    {
        $promotion = $this->promotionRepository->find($id);
        return $promotion ? $promotion->toArray() : null;
    }

    public function all(): array
    {
        return $this->promotionRepository->all();
    }

    public function delete(string $id): void
    {
        $this->promotionRepository->delete($id);
    }
    public function getActivePromotion(string $collection)
{
    $promotions = $this->promotionRepository->all();

    foreach ($promotions as $promotion) {
        if ($promotion['etat'] === 'Actif') {
            return new Promotion($promotion);
        }
    }

    return null; // Si aucune promotion active n'est trouvée
}

}
