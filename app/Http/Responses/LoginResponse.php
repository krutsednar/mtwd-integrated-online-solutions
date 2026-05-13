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

        // if (blank($user->name) OR blank($user->division_id) OR is_null($user->name) OR is_null($user->division_id)) {
        //     return redirect(Profile::getUrl());
        // } elseif( auth()->user()->hasRole('Executive')) {
        //     return Redirect::to('/executive');
        // } else {
        //     return redirect()->intended(Filament::getUrl());
        // }

        // Step 1: Force profile completion
        if (blank($user->name) || blank($user->division_id)) {
            return redirect(Profile::getUrl());
        }

        // Step 2: Role-based redirection
        if ($user->hasRole('Executive')) {
            return redirect('/executive');
        }

        // Step 3: Detect which Filament panel this login belongs to
        $panel = Filament::getCurrentPanel()?->getId();

        return match ($panel) {
            'home' => redirect('/home'),
            'admin' => redirect('/admin'),
            'MCIS' => redirect('/MCIS'),
            'MOJO' => redirect('/MOJO'),
            'MOCA' => redirect('/MOCA'),
            'PFIS' => redirect('/PFIS'),
            'executive' => redirect('/executive'),
            default => redirect()->intended(Filament::getUrl()),
        };
    }
}
