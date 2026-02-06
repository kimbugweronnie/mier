<?php
namespace App\Services\Product;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ProductService implements ProductServiceInterface
{
    public function all(): Collection
    {
        return Employee::all();
    }

    public function create(array $data): Employee
    {
        return Employee::create($data);
    }

    public function find(int $id): Employee
    {
        return Employee::findOrFail($id);
    }

    public function update(Employee $employee, array $data): Employee
    {
        $employee->update($data);
        return $employee;
    }

    public function delete(Employee $employee): void
    {
        $employee->delete();
    }
}