<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FirebaseServiceInterface;
use App\Models\Apprenant;
use App\Models\Referentiel;
use App\Models\User;

class ApprenantController extends Controller
{
    protected FirebaseServiceInterface $apprenantService;

    public function __construct(FirebaseServiceInterface $apprenantService)
    {
        $this->apprenantService = $apprenantService;
    }

    public function store(Request $request)
    {
        // Valider les données d'entrée
        $validatedData = $request->all();

        $user = User::find($validatedData['user_id']);
        $referentiel = Referentiel::find($validatedData['referentiel_id']);
        $apprenantData = [
            'user_id' => $validatedData['user_id'],
            'referentiel_id' => $validatedData['referentiel_id'],
            'photo' => $validatedData['photo'],
            // 'user_name' => $user->name, // Exemple d'ajout d'information
            // 'referentiel_title' => $referentiel->title, // Exemple d'ajout d'information
        ];

        // Créer l'apprenant
        $apprenantId = $this->apprenantService->create('apprenants', $apprenantData);

        return response()->json([
            'message' => 'Apprenant créé avec succès',
            'id' => $apprenantId,
        ], 201);
    }

    public function show($id)
    {
        $apprenant = $this->apprenantService->find('apprenants',$id);

        if (!$apprenant) {
            return response()->json(['message' => 'Apprenant non trouvé'], 404);
        }

        return response()->json($apprenant, 200);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'nom' => 'string|max:255',
            'prenom' => 'string|max:255',
            'email' => 'email|unique:apprenants,email,' . $id,
            'etat' => 'string',
        ]);
        $this->apprenantService->update('apprenants',$id, $validatedData);
        return response()->json(['message' => 'Apprenant mis à jour avec succès'], 200);
    }

    public function delete($id)
    {
        $this->apprenantService->delete('apprenants',$id);
        return response()->json(['message' => 'Apprenant supprimé avec succès'], 200);
    }

    // Récupérer tous les apprenants
    public function index()
    {
        $apprenants = $this->apprenantService->all('apprenants',);
        return response()->json($apprenants, 200);
    }

    
}
