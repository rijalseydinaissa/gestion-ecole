<?php

namespace App\Services;

use App\Repository\ApprenantRepositoryInterface;
use App\Models\Apprenant;
use Illuminate\Support\Str; 
use Endroid\QrCode\QrCode; 
use App\Mail\AuthenticationEmail;
use Endroid\QrCode\Writer\PngWriter; // Ajoutez cette ligne pour le writer
use App\Jobs\SendMailApprenantJob;


class FirebaseApprenantService implements ApprenantServiceInterface
{
    protected ApprenantRepositoryInterface $apprenantRepository;

    public function __construct(ApprenantRepositoryInterface $apprenantRepository)
    {
        $this->apprenantRepository = $apprenantRepository;
    }

    public function create(array $data): string
    {
        $apprenant = $this->apprenantRepository->create($data);
        $matricule = $this->generateMatricule($apprenant);
        $qrCodeUrl = $this->generateQRCode($apprenant);
        $this->apprenantRepository->update($apprenant->id, [
            'matricule' => $matricule,
            'qr_code' => $qrCodeUrl,
        ]);
        SendMailApprenantJob::dispatch($apprenant->email, $matricule, $apprenant->password);
        return $apprenant->getKey();
    }
    private function generateMatricule(Apprenant $apprenant): string
    {
        return 'APPR' . Str::random(6);  
    }
    private function generateQRCode(Apprenant $apprenant): string
    {
        $qrCodeData = [
            'nom' => $apprenant->nom,
            'prenom' => $apprenant->prenom,
            'email' => $apprenant->email,
            'matricule' => $apprenant->matricule,
        ];
        $qrCodePath = 'qrcodes/' . $apprenant->id . '.png';
        $qrCode = QrCode::create(json_encode($qrCodeData))
            ->setSize(300) 
            ->setMargin(10); 
        $writer = new PngWriter();
        $result = $writer->write($qrCode);
        file_put_contents(storage_path('app/public/' . $qrCodePath), $result->getString());
        return asset('storage/' . $qrCodePath);
    }
    
    
    
    

    public function update(string $id, array $data): void
    {
        $this->apprenantRepository->update($id, $data);
    }

    public function find(string $id): ?array
    {
        $apprenant = $this->apprenantRepository->find($id);
        return $apprenant ? $apprenant->toArray() : null;
    }

    public function all(): array
    {
        return $this->apprenantRepository->all();
    }

    public function delete(string $id): void
    {
        $this->apprenantRepository->delete($id);
    }

    public function getActiveApprenant(string $collection)
    {
        $apprenants = $this->apprenantRepository->all();

        foreach ($apprenants as $apprenant) {
            if ($apprenant['etat'] === 'Actif') {
                return new Apprenant($apprenant);
            }
        }

        return null; // Si aucun apprenant actif n'est trouvé
    }

    public function deactivateOtherApprenants(): void
    {
        // Récupérer tous les apprenants actifs
        $activeApprenants = $this->apprenantRepository->all()->where('etat', 'Actif');

        // Boucler sur ces apprenants et les désactiver
        foreach ($activeApprenants as $apprenant) {
            $this->apprenantRepository->update($apprenant->id, ['etat' => 'Inactif']);
        }
    }
}
