<?php

namespace App\Http\Controllers;

use App\Services\FirebasePromotionService;
use App\Services\FirebaseServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Exports\PromotionsExport; // Pour Excel
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;


class PromotionController extends Controller
{
    protected FirebaseServiceInterface $promotionService;

    public function __construct(FirebaseServiceInterface $promotionService)
    {
        $this->promotionService = $promotionService;
    }

    
    public function store(Request $request)
    {
        // $validatedData = $request->validate([
        //     'libelle' => 'required|unique:promotions,libelle',
        //     'date_debut' => 'required|date',
        //     'date_fin' => 'nullable|date|after_or_equal:date_debut',
        //     'duree' => 'nullable|integer|min:1',
        //     'photo' => 'nullable|url',
        //     'referentiels' => 'nullable|array',
        // ]);
        $validatedData = $request->all();

        // Créer la promotion via le service
        $promotionId = $this->promotionService->create('promotions',$validatedData);

        return response()->json([
            'message' => 'Promotion créée avec succès',
            'data' => $validatedData
        ], 201);
    }

   
     

    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'libelle' => 'required|unique:promotions,libelle,' . $id,
            'date_debut' => 'required|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'duree' => 'nullable|integer|min:1',
            'photo' => 'nullable|url',
            'referentiels' => 'nullable|array',
        ]);

        // Mettre à jour la promotion via le service
        $this->promotionService->update('promotions',$id, $validatedData);

        return response()->json(['message' => 'Promotion mise à jour avec succès'], 200);
    }

  
    public function show(string $id)
    {
        $promotion = $this->promotionService->find('promotions',$id);

        if ($promotion) {
            return response()->json($promotion, 200);
        }

        return response()->json(['message' => 'Promotion non trouvée'], 404);
    }

   
    public function index()
    {

        $promotions = $this->promotionService->all('promotions');

        return response()->json($promotions, 200);
    }

    
    public function delete(string $id)
    {
        $this->promotionService->delete('promotions',$id);

        return response()->json(['message' => 'Promotion supprimée avec succès'], 200);
    }

    //get actif promotion 
    public function getActifPromotion()
    {
        // Récupérer la promotion active via le service
        $promotionActive = $this->promotionService->getActivePromotion('promotions');
    
        if ($promotionActive) {
            return response()->json($promotionActive, 200);
        }
    
        return response()->json(['message' => 'Aucune promotion active trouvée'], 404);
    }
    public function getReferentielsActifs(string $id)
    {
        $promotion = $this->promotionService->find('promotions', $id);
        if (!$promotion) {
            return response()->json(['message' => 'Promotion non trouvée'], 404);
        }
        $referentielsActifs = array_filter($promotion['referentiels'], function($referentiel) {
            return $referentiel['statut'] === 'Actif';
        });
        return response()->json($referentielsActifs, 200);
    }
    public function updateEtat(Request $request, string $id)
{
    $validatedData = $request->validate([
        'etat' => 'required|in:Actif,Cloturer,Inactif',
    ]);
    if ($validatedData['etat'] === 'Actif') {
        // Désactiver toutes les autres promotions actives
        $this->promotionService->deactivateOtherPromotions();
    }
    $this->promotionService->update('promotions', $id, ['etat' => $validatedData['etat']]);

    return response()->json(['message' => 'État de la promotion mis à jour avec succès'], 200);
}

public function getStats(string $id)
{
    // Récupérer les détails de la promotion
    $promotion = $this->promotionService->find('promotions', $id);

    if (!$promotion) {
        return response()->json(['message' => 'Promotion non trouvée'], 404);
    }
    // Récupérer les apprenants liés à la promotion
    $apprenants = $this->getApprenantsForPromotion($id);
    // Calculer les statistiques
    $totalApprenants = count($apprenants);
    $apprenantsActifs = count(array_filter($apprenants, fn($apprenant) => $apprenant['statut'] === 'Actif'));
    $apprenantsInactifs = $totalApprenants - $apprenantsActifs;
    // Récupérer les référentiels actifs de la promotion
    $referentielsActifs = $this->getReferentielsActifs($id);
    // Construire la réponse
    $stats = [
        'promotion' => $promotion,
        'nombre_apprenants' => $totalApprenants,
        'nombre_apprenants_actifs' => $apprenantsActifs,
        'nombre_apprenants_inactifs' => $apprenantsInactifs,
        'referentiels' => $referentielsActifs,
    ];

    return response()->json($stats, 200);
}
private function getApprenantsForPromotion(string $promotionId)
    {
        // Récupérer les détails de la promotion
        $promotion = $this->promotionService->find('promotions', $promotionId);
        if (!$promotion || !isset($promotion['referentiels'])) {
            return [];
        }
        $apprenants = [];
        foreach ($promotion['referentiels'] as $referentiel) {
            if (isset($referentiel['apprenants'])) {
                $apprenants = array_merge($apprenants, $referentiel['apprenants']);
            }
        }
        return $apprenants;
    }
    
    public function updateReferentiel(Request $request, string $id)
    {
        // $userRole = auth()->user()->role;
        $userRole = 'Manager';
        $promotion = $this->promotionService->find('promotions', $id);
        if (!$promotion) {
            return response()->json(['message' => 'Promotion non trouvée'], 404);
        }
        $referentielData = $request->input('referentiel');
        if (isset($referentielData['action']) && $referentielData['action'] === 'retirer') {
            return $this->retirerReferentiel($promotion, $referentielData, $userRole);
        } elseif (isset($referentielData['action']) && $referentielData['action'] === 'ajouter') {
            return $this->ajouterReferentiel($promotion, $referentielData);
        }
        return response()->json(['message' => 'Action invalide'], 400);
    }

    private function retirerReferentiel($promotion, $referentielData, $userRole)
    {
        $referentielId = $referentielData['id'];
        $referentiel = $this->findReferentielById($promotion['referentiels'], $referentielId);
        if (!$referentiel) {
            return response()->json(['message' => 'Référentiel non trouvé'], 404);
        }
        $nombreApprenants = count($referentiel['apprenants']);
        if ($userRole === 'CM' && $nombreApprenants > 0) {
            return response()->json(['message' => 'Le rôle CM ne peut pas retirer un référentiel avec des apprenants'], 403);
        }
        $referentiel['statut'] = 'Inactif';
        $this->promotionService->update('promotions', $promotion['id'], ['referentiels' => $promotion['referentiels']]);
        return response()->json(['message' => 'Référentiel retiré avec succès'], 200);
    }

    private function ajouterReferentiel($promotion, $referentielData)
        {
                $nouveauReferentiel = [
                    'id' => $referentielData['id'],
                    'libelle' => $referentielData['libelle'],
                    'statut' => 'Actif',
                    'apprenants' => [] 
                ];
                $promotion['referentiels'][] = $nouveauReferentiel;
                $this->promotionService->update('promotions', $promotion['id'], ['referentiels' => $promotion['referentiels']]);
                return response()->json(['message' => 'Référentiel ajouté avec succès'], 200);
            }

            private function findReferentielById(array $referentiels, string $id)
            {
                foreach ($referentiels as $referentiel) {
                    if ($referentiel['id'] === $id) {
                        return $referentiel;
                    }
                }
                return null;
            }

   
    public function export(Request $request)
        {
                // dd("&&&");
            $format = $request->query('format', 'excel');
            if ($format === 'pdf') {
                return $this->exportToPdf();
            }
            return $this->exportToExcel();
        }     
    private function exportToExcel()
    {
       
        Excel::store(new PromotionsExport, 'promotions.xlsx','public');
        return  Excel::download(new PromotionsExport, 'promotions.xlsx');
    }
    public function exportToPdf()
    {
        $promotions = $this->promotionService->all('promotions');
        $promotions = array_map(function ($promotion) {
            return [
                'libelle' => $promotion['libelle'] ?? 'N/A',
                'date_debut' => $promotion['date_debut'] ?? 'N/A',
                'date_fin' => $promotion['date_fin'] ?? 'N/A',
                'duree' => $promotion['duree'] ?? 'N/A',
                'photo' => $promotion['photo'] ?? 'N/A',
                'etat' => $promotion['etat'] ?? 'N/A',
            ];
        }, $promotions);
        $pdf = PDF::loadView('promotions.pdf', ['promotions' => $promotions]);
        return $pdf->download('promotions.pdf');
    }

}
