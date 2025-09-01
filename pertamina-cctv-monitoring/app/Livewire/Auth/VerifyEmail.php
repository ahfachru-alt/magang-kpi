<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class VerifyEmail extends Component
{
    public function sendVerificationEmail()
    {
        auth()->user()->sendEmailVerificationNotification();
        
        session()->flash('status', 'Verification link sent!');
    }

    public function render()
    {
        return view('livewire.auth.verify-email')->layout('layouts.guest');
    }
}