<?php

namespace App\Livewire;

use Livewire\Component;

class PanelTitle extends Component
{
    public string $panelId;

    public function render()
    {
        return view('livewire.panel-title');
    }
}
