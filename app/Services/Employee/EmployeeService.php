<?php

namespace App\Services\Product;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Collection;

class ProductService implements ProductServiceInterface
{
    public function all(): Collection
    {
        // return Employee::all();
        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            foreach (Order::where('tenant_id', $tenant->id)
                ->where('status', 'paid')
                ->cursor() as $order) {

                // Process payment confirmation
                dispatch(new ProcessPayment($order));
            }
        }

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
