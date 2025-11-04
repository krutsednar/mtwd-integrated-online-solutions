<?php

namespace App\Http\Responses;

use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse as Responsable;

class LogoutResponse implements Responsable
{
    public function toResponse($request): RedirectResponse
    {
        // return Redirect::to('/home/login');
        $panel = Filament::getCurrentPanel()?->getId();

        return match ($panel) {
            'home' => redirect('/home/login'),
            'admin' => redirect('/admin/login'),
            'MCIS' => redirect('/MCIS/login'),
            'MOJO' => redirect('/MOJO/login'),
            'MOCA' => redirect('/MOCA/login'),
            'PFIS' => redirect('/PFIS/login'),
            'executive' => redirect('/executive/login'),
            default => redirect('/login'),
        };
    }
}
