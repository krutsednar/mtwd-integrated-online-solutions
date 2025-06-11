<?php

namespace App\Http\Responses;

// use App\Filament\Resources\OrderResource;
use Auth;
use Filament\Facades\Filament;
use App\Filament\Pages\Profile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Livewire\Features\SupportRedirects\Redirector;
use Filament\Http\Responses\Auth\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): RedirectResponse|Redirector
    {

         $user = auth()->user();

        if (blank($user->name) OR blank($user->division_id) OR is_null($user->name) OR is_null($user->division_id)) {
            return redirect(Profile::getUrl());
        } elseif( auth()->user()->hasRole('Executive')) {
            return Redirect::to('/executive');
        } else {
            return redirect()->intended(Filament::getUrl());
        }
    }
}
