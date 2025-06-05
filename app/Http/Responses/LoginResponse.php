<?php

namespace App\Http\Responses;

// use App\Filament\Resources\OrderResource;
use Auth;
use Filament\Facades\Filament;
use App\Filament\Pages\Profile;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;
use Filament\Http\Responses\Auth\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): RedirectResponse|Redirector
    {

         $user = auth()->user();

        if (blank($user->name)) {
            return redirect(Profile::getUrl());
        } else {
            return redirect()->intended(Filament::getUrl());
        }


        // Here, you can define which resource and which page you want to redirect to
        // return redirect()->to(OrderResource::getUrl('index'));
    }
}
