<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Models\UserLocal;
use App\Services\FirebaseServiceInterface;
use Illuminate\Http\Request;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Imports\UsersImport;
use Illuminate\Support\Facades\Validator;
use Smalot\PdfParser\Parser; 
use Illuminate\Support\Facades\Storage;


class UserController extends Controller
{
    protected $firebaseService;
    public function __construct(FirebaseServiceInterface $firebaseService)
        {
            $this->firebaseService = $firebaseService;
        }
    public function index(Request $request)
        {
            $users = $this->firebaseService->all('users');
            if ($request->has('role')) {
                $users = array_filter($users, function ($user) use ($request) {
                    return $user['role'] === $request->role;
                });
            }
            return response()->json(array_values($users));
        }

    public function show($id)
        {
            $user = $this->firebaseService->find('users', $id);
            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }
            return response()->json($user);
        }
    public function store(Request $request)
    {
        $userData = $request->only(['nom', 'prenom', 'adresse', 'password', 'telephone', 'fonction', 'email', 'statut', 'role']);
        if ($request->hasFile('photo')) {
            $userData['photo'] = $request->file('photo');
        }
        try {
            DB::beginTransaction();
            $user = new User($userData);
            $user->save();
            $authUid = $user->createWithAuthentication($userData);
            $user->auth_uid = $authUid;
            $user->save();
            $localUser = new UserLocal($user->toArray());
            $localUser->id = $user->id;
            if (!$localUser->save()) {
                throw new Exception('Failed to create user in local database');
            }
            DB::commit();
            return response()->json($user->toArray(), 201);
        } catch (Exception $e) {
            DB::rollBack();
            if (isset($user->id)) {
                $this->firebaseService->delete('users', $user->id);
            }
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function update(UpdateUserRequest $request, $id)
        {
            $userData = $request->validated();
            $this->firebaseService->update('users', $id, $userData);
            $localUser = UserLocal::find($id);
            if ($localUser) {
                $localUser->update($userData);
            }
            $updatedUser = $this->firebaseService->find('users', $id);
            if (!$updatedUser) {
                return response()->json(['message' => 'User not found'], 404);
            }
            return response()->json($updatedUser);
        }
    public function destroy($id)
    {
        $user = $this->firebaseService->find('users', $id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        $this->firebaseService->delete('users', $id);
        UserLocal::destroy($id);
        return response()->json(null, 204);
    }
    public function export(Request $request)
    {
        $format = $request->query('format', 'excel');
        if ($format === 'pdf') {
            return $this->exportToPdf();
        }
        return $this->exportToExcel();
    }
    private function exportToExcel()
    {
        Excel::store(new UsersExport, 'users.xlsx','public');
        return  Excel::download(new UsersExport, 'users.xlsx');
    }

    public function exportToPdf()
    {
        $users = $this->firebaseService->all('users');
        $users = array_map(function ($user) {
            return [
                'nom' => $user['nom'] ?? 'N/A',
                'prenom' => $user['prenom'] ?? 'N/A',
                'email' => $user['email'] ?? 'N/A',
                'role' => $user['role'] ?? 'N/A',
                'telephone' => $user['telephone'] ?? 'N/A',
                'statut' => $user['statut'] ?? 'N/A',
                'adresse' => $user['adresse'] ?? 'N/A',
                'photo' => $user['photo'] ?? 'N/A',
            ];
        }, $users);
        $pdf = PDF::loadView('users.pdf', ['users' => $users]);
        return $pdf->download('users.pdf');
    }
    

    public function importUsers(Request $request)
{
    try {
        // Valider si le fichier est présent
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,pdf|max:2048',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        // Importer depuis un fichier Excel
        if ($request->file('file')->getClientOriginalExtension() == 'xlsx') {
            DB::beginTransaction();
            Excel::import(new UsersImport, $request->file('file'));
            DB::commit();
            return response()->json(['message' => 'Users imported successfully from Excel.'], 200);
        }
        // Importer depuis un fichier PDF
        if ($request->file('file')->getClientOriginalExtension() == 'pdf') {
            DB::beginTransaction();
            $this->importFromPdf($request->file('file'));
            DB::commit();
            return response()->json(['message' => 'Users imported successfully from PDF.'], 200);
        }

    } catch (Exception $e) {
        DB::rollBack();
        return response()->json(['error' => $e->getMessage()], 500);
    }
}


    public function importFromPdf($file)
    {
        // Initialiser le parser de PDF
        $parser = new Parser();
        $pdf = $parser->parseFile($file);
        $text = $pdf->getText();
        // Analysez le texte pour extraire les utilisateurs.
        $usersData = explode("\n\n", $text); // Séparer chaque utilisateur par des lignes vides
        foreach ($usersData as $userData) {
            $fields = explode(';', $userData); // Séparer les champs par des points-virgules (adaptez cela à votre PDF)
            if (count($fields) >= 7) {
                $user = new User([
                    'nom'       => trim($fields[0]),
                    'prenom'    => trim($fields[1]),
                    'adresse'   => trim($fields[2]),
                    'password'  => (trim($fields[3])), 
                    'telephone' => trim($fields[4]),
                    'fonction'  => trim($fields[5]),
                    'email'     => trim($fields[6]),
                    'statut'    => isset($fields[7]) ? trim($fields[7]) : 'Actif', 
                    'role'      => isset($fields[8]) ? trim($fields[8]) : 'Manager',
                ]);
                $user->save();
            }
        }
    }

      
}