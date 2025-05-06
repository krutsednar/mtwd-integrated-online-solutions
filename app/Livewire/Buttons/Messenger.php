<?php

namespace App\Livewire\Buttons;

use Livewire\Component;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;

class Messenger extends Component
{
    public $count = 0;

    public function mount()
    {
        $this->updateCount();
    }

    public function updateCount()
    {
        $newCount = auth()->check() ? auth()->user()->getUnreadCount() : 0;

        if ($newCount > $this->count) {
            Notification::make()
                ->title('You have ' . $newCount . ' unread messages')
                ->info()
                ->actions([
                    Action::make('Open Messenger')
                        ->url(url('messenger')) // Replace with your actual route
                        ->button()
                ])
                ->send();

                $this->dispatch('play-notification-sound');
        }

        $this->count = $newCount;

    }

    public function render()
    {
        return view('livewire.buttons.messenger');
    }
}
