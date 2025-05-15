<?php

namespace App\Livewire;

use Livewire\Component;

class LoginRedirect extends Component
{
    public function render()
    {
        return $this->redirectRoute('filament.home.auth.login');
    }
}
