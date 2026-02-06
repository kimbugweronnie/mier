<?php

namespace App\Services\Product;

use Illuminate\Database\Eloquent\Collection;

interface EmployeeServiceInterface
{
    public function all(): Collection;

    public function create(array $data): Employee;

    public function find(int $id): Employee;

    public function update(Employee $employee, array $data): Employee;

    public function delete(Employee $employee): void;
}
