<?php
declare(strict_types=1);

namespace App\Repositories;

interface StockRepository
{
    public function getAll(): array;

    public function buyStock(string $symbol, float $amount, float $price): void;

    public function sellStock(int $id, float $price): void;

    public function deleteStock(int $id): void;

    public function money(): float;
}