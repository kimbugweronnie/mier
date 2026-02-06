<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\Employee;

class EmployeeObserver
{
    public function creating(Employee $employee)
    {
        // runs BEFORE insert — e.g., set defaults
        $employee->name = trim($employee->name);
    }

    public function created(Employee $employee)
    {
        $order = Order::create($data);
        event(new OrderCreated($order->id));
        return $order;

        if (! $employee->relationLoaded('user')) {
            return;
        }
        AuditLog::create([
            'employee_id' => $employee->id,
            'action' => 'created',
        ]);

        //OBSERVER SHOULD NOT DO EMAILS.THEY SHOULD DISPATCH A JOB AND THE JOB SHOULD DO THE EMAIL
        dispatch(new \App\Jobs\SendWelcomeEmail($employee));
    }

    public function updating(Employee $employee)
    {
        // runs BEFORE update
    }

    public function deleted(Employee $employee)
    {
        // runs AFTER delete — e.g., cleanup related resources
    }
}
