<?php

namespace App\Http\Controllers;

use App\Services\FirebaseReferentielService;
use Illuminate\Http\Request;
use App\Services\FirebaseServiceInterface;

class ReferentielController extends Controller
{
    protected FirebaseServiceInterface $firebaseService;

    public function __construct(FirebaseServiceInterface $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function index()
    {
        $referentiels = $this->firebaseService->all('referentiels');
        return response()->json($referentiels);
    }

    public function show($id, Request $request)
    {
        $referentiel = $this->firebaseService->find('referentiels', $id);
        if (!$referentiel) {
            return response()->json(['message' => 'Not Found'], 404);
        }
        if ($request->has('filtre')) {
            $filtre = $request->input('filtre');
            if ($filtre === 'competences') {
                return response()->json($referentiel['competences']);
            }
            if ($filtre === 'modules') {
                $modules = [];
                foreach ($referentiel['competences'] as $competence) {
                    // Vérifier si la compétence a une clé 'modules'
                    if (isset($competence['modules'])) {
                        foreach ($competence['modules'] as $module) {
                            $modules[] = $module;
                        }
                    }
                }
                return response()->json($modules);
            }
        }
        return response()->json($referentiel);
    }
    
    
    

    public function store(Request $request)
    {
        $data = $request->only( 'code', 'libelle', 'description', 'photo_couverture', 'statut', 'competences','modules');
        // $data = $request->validate([
        //     'code' => 'required|string|unique:referentiels',
        //     'libelle' => 'required|string|unique:referentiels',
        //     'description' => 'nullable|string',
        //     'photo_couverture' => 'nullable|string',
        //     'statut' => 'required|string',
        //     'competences' => 'array|nullable',
        //     'modules' => 'array|nullable',
        // ]);

        // Ajout d'un nouvel ID généré par Firebase
        $id = $this->firebaseService->create('referentiels',$data);

        return response()->json($data, 201);
    }

    public function update(Request $request, $id)
    {
        // Validation des données d'entrée
        $data = $request->validate([
            'code' => 'sometimes|required|string',
            'libelle' => 'sometimes|required|string',
            'description' => 'nullable|string',
            'photo_couverture' => 'nullable|string',
            'statut' => 'sometimes|required|string',
            'competences' => 'array|nullable',
            'delete_competence' => 'nullable|string', 
            'delete_module' => 'nullable|array', 
        ]);
    
        // Récupérer le référentiel existant
        $referentiel = $this->firebaseService->find('referentiels', $id);
        if (!$referentiel) {
            return response()->json(['message' => 'Not Found'], 404);
        }
        // Mettre à jour les champs simples du référentiel
        $updatedReferentiel = array_merge($referentiel, $data);
        // 1. Gestion des compétences (ajout ou modification)
        if (isset($data['competences'])) {
            foreach ($data['competences'] as $compKey => $competence) {
                // Ajouter ou mettre à jour la compétence
                if (isset($competence['libelle'])) {
                    $updatedReferentiel['competences'][$compKey] = $competence;
                }
                // Mettre à jour ou ajouter des modules à cette compétence
                if (isset($competence['modules'])) {
                    $updatedReferentiel['competences'][$compKey]['modules'] = $competence['modules'];
                }
            }
        }
        // 2. Gestion de la suppression d'une compétence (soft delete)
        if (isset($data['delete_competence'])) {
            $competenceToDelete = $data['delete_competence'];
            if (isset($referentiel['competences'][$competenceToDelete])) {
                // Marquer la compétence comme supprimée
                $updatedReferentiel['competences'][$competenceToDelete]['deleted_at'] = now();
                // Soft delete des modules liés à la compétence
                foreach ($referentiel['competences'][$competenceToDelete]['modules'] as &$module) {
                    $module['deleted_at'] = now();
                }
            }
        }
        // 3. Gestion de la suppression d'un module dans une compétence (soft delete)
        if (isset($data['delete_module'])) {
            $competenceKey = $data['delete_module']['competence']; // La compétence ciblée
            $moduleKey = $data['delete_module']['module']; // Le module à supprimer
            if (isset($referentiel['competences'][$competenceKey]['modules'][$moduleKey])) {
                // Marquer le module comme supprimé
                $updatedReferentiel['competences'][$competenceKey]['modules'][$moduleKey]['deleted_at'] = now();
            }
        }
        // Mise à jour dans Firebase
        $this->firebaseService->update('referentiels', $id, $updatedReferentiel);
    
        return response()->json(['message' => 'Updated Successfully']);
    }
    

    public function destroy($id)
    {
        $referentiel = $this->firebaseService->find('referentiels', $id);
        if (!$referentiel) {
            return response()->json(['message' => 'Not Found'], 404);
        }
        $referentiel['deleted_at'] = now(); // Ajouter le champ deleted_at avec la date actuelle
        $this->firebaseService->update('referentiels', $id, $referentiel);
        return response()->json(['message' => 'Referentiel soft deleted successfully']);
    }
    public function getAll()
    {
        $referentiels = $this->firebaseService->all('referentiels');
        $deletedReferentiels = array_filter($referentiels, function ($referentiel) {
            return isset($referentiel['deleted_at']);
        });
        return response()->json($deletedReferentiels);
    }

}
