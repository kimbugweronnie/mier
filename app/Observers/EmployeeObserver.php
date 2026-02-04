<?php

namespace App\Observers;

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
        // runs AFTER insert — e.g., fire welcome email job
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