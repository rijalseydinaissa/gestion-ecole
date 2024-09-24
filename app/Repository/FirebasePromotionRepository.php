<?php

namespace App\Repository;

use App\Models\Promotion;
use App\Facades\FirebasePromotionFacade;

class FirebasePromotionRepository implements PromotionRepositoryInterface
{
    public function create(array $data): Promotion
    {
        // Vérifier si l'état est "Actif"
        if (isset($data['etat']) && $data['etat'] === 'Actif') {
            $this->deactivateOtherPromotions();
        } else {
            // Assigner l'état par défaut "Inactif" si non fourni
            $data['etat'] = 'Inactif';
        }
        $id = FirebasePromotionFacade::create($data);
        $promotion = new Promotion();
        $promotion->id = $id;
        $promotion->fill($data);
        return $promotion;
    }

    public function update($id, array $data): Promotion
    {
        // Si on met à jour l'état en "Actif", désactiver les autres promotions
        if (isset($data['etat']) && $data['etat'] === 'Actif') {
            $this->deactivateOtherPromotions();
        }

        FirebasePromotionFacade::update($id, $data);
        return new Promotion($data + ['id' => $id]);
    }

    public function find($id): ?Promotion
    {
        $promotionData = FirebasePromotionFacade::find($id);
        return $promotionData ? new Promotion($promotionData + ['id' => $id]) : null;
    }

    public function all()
    {
        return FirebasePromotionFacade::all();
    }

    public function delete($id): void
    {
        FirebasePromotionFacade::delete($id);
    }

    protected function deactivateOtherPromotions(): void
    {
        $promotions = FirebasePromotionFacade::all();
        foreach ($promotions as $promotionData) {
            if (isset($promotionData['etat']) && $promotionData['etat'] === 'Actif') {
                FirebasePromotionFacade::update($promotionData['id'], ['etat' => 'Inactif']);
            }
        }
    }
    
}
