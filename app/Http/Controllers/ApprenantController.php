<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FirebaseApprenantService;
use App\Services\FirebaseServiceInterface;

class ApprenantController extends Controller
{
    protected FirebaseServiceInterface $apprenantService;

    public function __construct(FirebaseServiceInterface $apprenantService)
    {
        $this->apprenantService = $apprenantService;
    }

    // Créer un apprenant
    public function store(Request $request)
    {
        // $ = $request->validate([
        //     'user_id' => 'required|exists:users,id', // Lier à un utilisateur existant
        //     'referentiel_id' => 'required|exists:referentiels,id', // Lier à un référentiel existant
        //     'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048', // Photo de profil
        // ]);
        $validatedData= $request->only('referentiel_id', 'user_id', 'photo');
        // $photoPath = $request->file('photo')->create('photos_apprenants');
        $apprenantData = [
            'user_id' => $validatedData['user_id'],
            'referentiel_id' => $validatedData['referentiel_id'],
            // 'photo' => $photoPath,
        ];
        $apprenantId = $this->apprenantService->create('apprenants', $apprenantData);
        return response()->json([
            'message' => 'Apprenant créé avec succès',
            'id' => $apprenantId
        ], 201);
    }

    // Récupérer les informations d'un apprenant
    public function show($id)
    {
        $apprenant = $this->apprenantService->find('apprenants',$id);

        if (!$apprenant) {
            return response()->json(['message' => 'Apprenant non trouvé'], 404);
        }

        return response()->json($apprenant, 200);
    }

    // Mettre à jour un apprenant
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

    // Supprimer un apprenant
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

    // Récupérer l'apprenant actif
    // public function getActiveApprenant()
    // {
    //     $apprenant = $this->apprenantService->getActiveApprenant('apprenants');

    //     if (!$apprenant) {
    //         return response()->json(['message' => 'Aucun apprenant actif trouvé'], 404);
    //     }

    //     return response()->json($apprenant, 200);
    // }

    // // Désactiver tous les autres apprenants
    // public function deactivateOtherApprenants()
    // {
    //     $this->apprenantService->deactivateOtherApprenants();

    //     return response()->json(['message' => 'Tous les autres apprenants ont été désactivés'], 200);
    // }
}
