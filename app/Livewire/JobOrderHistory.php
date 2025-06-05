<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use App\Models\OnlineJobOrder;


class JobOrderHistory extends Component
{
    public OnlineJobOrder $record;

    public User $user;

    // public $jobOrder;
    // public string $jo_number;

    public function mount(User $user): void
    {
        $this->user = $user;
    }

    public function render()
    {
        return view('livewire.job-order-history');
    }
}
