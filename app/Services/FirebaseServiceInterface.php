<?php

namespace App\Services;

interface FirebaseServiceInterface
{
    public function create(string $model, array $attributes): string;
    public function update(string $model, string $id, array $attributes): void;
    public function find(string $model, string $id): ?array;
    public function all(string $model): array;
    public function delete(string $model, string $id): void;
    public function getActivePromotion(string $collection);
    public function deactivateOtherPromotions(): void;
    
//    public function  createWithAuthentication($userData);
}
