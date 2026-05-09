<?php

declare(strict_types=1);

namespace App\Contracts\Foundation;

use App\Data\Foundation\CreateCompanyData;
use App\Data\Foundation\UpdateCompanyData;
use App\Models\Company;
use Illuminate\Pagination\LengthAwarePaginator;

interface CompanyServiceInterface
{
    public function list(int $perPage = 25): LengthAwarePaginator;

    public function find(string $id): Company;

    public function create(CreateCompanyData $data): Company;

    public function update(string $id, UpdateCompanyData $data): Company;

    public function suspend(string $id): Company;

    public function activate(string $id): Company;

    public function cancel(string $id): Company;
}
