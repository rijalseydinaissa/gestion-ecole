<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FirebaseServiceInterface;
use Maatwebsite\Excel\Facades\Excel;
use Kreait\Firebase\Database;
use App\Models\UserLocal;
use App\Models\User;
use Endroid\QrCode\Writer\PngWriter;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Jobs\SendRegistrationEmail;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\ApprenantsExport;


use Endroid\QrCode\QrCode;

class ApprenantController extends Controller
{
    protected FirebaseServiceInterface $apprenantService;
    protected $database;
    public function __construct(FirebaseServiceInterface $apprenantService,Database $database)
    {
        $this->apprenantService = $apprenantService;
    }
    public function store(Request $request)
{
    $validatedData = $request->all();
    
    try {
        DB::beginTransaction();
        
        // 1. Création de l'utilisateur
        $userData = [
            'nom' => $validatedData['nom'],
            'prenom' => $validatedData['prenom'],
            'adresse' => $validatedData['adresse'],
            'email' => $validatedData['email'],
            'password' => bcrypt($validatedData['password']),
            'telephone' => $validatedData['telephone'],
            'role' => $validatedData['role'],
            'statut' => $validatedData['statut'],
            'photo' => $request->hasFile('photo') ? $request->file('photo')->store('photos', 'public') : null,
        ];
        $user = new User($userData);
        $user->save();
        
        // Création dans Firebase et gestion de l'auth_uid
        $authUid = $user->createWithAuthentication($userData);
        $user->auth_uid = $authUid;
        $user->save();

        // Création de l'utilisateur local
        $localUser = new UserLocal($user->toArray());
        $localUser->id = $user->id;
        if (!$localUser->save()) {
            throw new Exception('Failed to create user in local database');
        }
        // Génération du matricule
        $matricule = 'APP-' . strtoupper(Str::random(6));
        $qrCodeData = json_encode([
            'matricule' => $matricule,
            'nom' => $user->nom,
            'prenom' => $user->prenom,
            'email' => $user->email,
        ]);
        $writer = new PngWriter();
        $qrCode = QrCode::create($qrCodeData)
            ->setSize(400)
            ->setMargin(10);
        $Qr = base64_encode($writer->write($qrCode)->getString());
        // 2. Création de l'apprenant associé à l'utilisateur
        $apprenantData = [
            'user_id' => $userData, // Utiliser l'ID de l'utilisateur
            'referentiel_id' => $validatedData['referentiel_id'],
            'photo' => $userData['photo'], // Utilisation de la même photo que l'utilisateur
            'matricule' => $matricule,
            'qr_code' => $Qr, // Utilisation du QR Code généré précédemment
        ];
        $apprenantId = $this->apprenantService->create('apprenants', $apprenantData);
        
        DB::commit();
        // Envoi d'un email d'inscription via un Job asynchrone
        $emailData = [
            'login' => $user->email,
            'password' => $validatedData['password'], // mot de passe en clair pour l'email
            // 'auth_link' => route('email'), // lien vers la page d'authentification
        ];
        SendRegistrationEmail::dispatch($user->id, $emailData);
        return response()->json([
            'message' => 'Utilisateur et apprenant créés avec succès',
            'user' => $user->toArray(),
            'apprenant_id' => $apprenantId,
            'qr_code' => 'data:image/png;base64,' . $Qr,
        ], 201);
    } catch (Exception $e) {
        DB::rollBack();
        // Si un utilisateur est créé mais que l'apprenant échoue, on supprime l'utilisateur dans Firebase
        if (isset($user->id)) {
            $this->apprenantService->delete('users', $user->id);
        }
        return response()->json(['error' => $e->getMessage()], 500);
    }
}
    
    
    
    protected function checkReferentielInFirebase(string $referentielId): bool
{
    $reference = $this->database->getReference('referentiels/' . $referentielId);
    $snapshot = $reference->getSnapshot();

    return $snapshot->exists();
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
        $validatedData = $request->all();
        $this->apprenantService->update('apprenants',$id, $validatedData);
        return response()->json(['message' => 'Apprenant mis à jour avec succès'], 200);
    }
    public function delete($id)
    {
        $this->apprenantService->delete('apprenants',$id);
        return response()->json(['message' => 'Apprenant supprimé avec succès'], 200);
    }
    // Récupérer tous les apprenants

    public function index(Request $request)
    {
        $apprenantsArray = $this->apprenantService->all('apprenants');
        $apprenants = collect($apprenantsArray);
        if ($request->has('referentiel')) {
            $apprenants = $apprenants->where('referentiel_id', $request->input('referentiel'));
        }
        if ($request->has('statut')) {
            $apprenants = $apprenants->where('statut', $request->input('statut'));
        }
        return response()->json($apprenants->values(), 200);
    }
    
    public function exportPdf()
{
    // Récupérer la liste des apprenants
    $apprenants = $this->apprenantService->all('apprenants');

    // Charger la vue apprenants_pdf.blade.php
    $pdf = PDF::loadView('apprenants_pdf', compact('apprenants'));

    // Retourner le PDF téléchargé
    return $pdf->download('apprenants.pdf');
}

public function exportExcel()
{
    // Récupérer les apprenants et convertir en collection
    $apprenants = collect($this->apprenantService->all('apprenants'));

    return \Excel::download(new ApprenantsExport($apprenants), 'apprenants.xlsx');
}



    
}
