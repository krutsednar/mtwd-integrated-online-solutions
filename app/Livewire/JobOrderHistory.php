<?php

namespace App\Livewire;

use App\Models\JoAccomplishment;
use App\Models\JoDispatch;
use App\Models\User;
use Livewire\Component;
use App\Models\OnlineJobOrder;

class JobOrderHistory extends Component
{
    public OnlineJobOrder $record;

    public ?string $verifiedByName      = null;
    public ?string $dispatchedByName    = null;
    public ?string $receivedByName      = null;
    public ?string $forwardedByName     = null;
    public ?string $processedByName     = null;
    public string  $accomplishedByNames = '';
    public string  $dispatchedByNames   = '';

    public function mount(): void
    {
        abort_unless(auth()->check() && auth()->user()->can('view', $this->record), 403);

        $joId      = fn (?string $field) => $field
            ? User::where('jo_id', $field)->first()
            : null;

        $this->verifiedByName   = $this->resolveUserName($this->record->verified_by);
        $this->dispatchedByName = $this->resolveUserName($this->record->dispatched_by);
        $this->receivedByName   = $this->resolveUserName($this->record->received_by);
        $this->forwardedByName  = $this->resolveUserName($this->record->forwarded_by);
        $this->processedByName  = $this->resolveUserName($this->record->processed_by);

        $this->accomplishedByNames = User::whereIn(
            'employee_number',
            JoAccomplishment::where('jo_number', $this->record->jo_number)->pluck('jo_user')
        )->get()->pluck('full_name')->implode(', ');

        $this->dispatchedByNames = User::whereIn(
            'employee_number',
            JoDispatch::where('jo_number', $this->record->jo_number)->pluck('jo_user')
        )->get()->pluck('full_name')->implode(', ');
    }

    private function resolveUserName(?string $field): ?string
    {
        if (! $field) return null;

        $user = User::where('jo_id', $field)->first()
            ?? User::where('employee_number', substr_replace($field, '-', 2, 0))->first()
            ?? User::where('employee_number', $field)->first();

        return $user ? "{$user->first_name} {$user->last_name}" : null;
    }

    public function render()
    {
        return view('livewire.job-order-history');
    }
}
