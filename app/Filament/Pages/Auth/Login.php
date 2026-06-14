<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Auth\Login as BaseLogin;

class Login extends BaseLogin
{
    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label('Email Address')
            ->placeholder('example@gmail.com')
            ->email()
            ->required()
            ->autocomplete()
            ->autofocus()
            ->prefixIcon('heroicon-o-envelope')
            ->extraInputAttributes([
                'tabindex' => 1,
            ]);
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label('Password')
            ->password()
            ->revealable()
            ->required()
            ->autocomplete('current-password')
            ->prefixIcon('heroicon-o-lock-closed')
            ->extraInputAttributes([
                'tabindex' => 2,
            ]);
    }

    protected function getRememberFormComponent(): Component
    {
        return Checkbox::make('remember')
            ->label('Remember Me');
    }


   
}