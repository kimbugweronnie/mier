<?php

use App\Enums\LeaveStatus;

class Leave extends Model
{
    protected $casts = [
        'status' => LeaveStatus::class,
    ];

    public function transitionTo(LeaveStatus $status): void
{
    if (! $this->status->canTransitionTo($status)) {

        throw new \DomainException(
            "Invalid status transition from {$this->status->value} to {$status->value}"
        );

    }

    $this->update([
        'status' => $status
    ]);
}
}