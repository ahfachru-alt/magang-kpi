<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ConfirmPassword extends Component
{
    public $password = '';

    protected $rules = [
        'password' => ['required', 'current_password'],
    ];

    public function confirmPassword()
    {
        $this->validate();

        session()->put('auth.password_confirmed_at', time());

        return redirect()->intended(route('user.dashboard'));
    }

    public function render()
    {
        return view('livewire.auth.confirm-password')->layout('layouts.guest');
    }
}