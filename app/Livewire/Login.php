<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Masuk Kasir / Admin - Kafe POS')]
class Login extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    protected function rules(): array
    {
        return [
            'email' => 'required|email|max:150',
            'password' => 'required|string|max:100',
        ];
    }

    public function login(): void
    {
        $this->email = trim(strip_tags(strtolower($this->email)));
        $this->validate();

        $throttleKey = Str::transliterate(Str::lower($this->email) . '|' . request()->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('email', 'Terlalu banyak percobaan login gagal. Silakan coba lagi dalam ' . $seconds . ' detik.');
            return;
        }

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::clear($throttleKey);
            session()->regenerate();
            $this->redirectRoute('pos', navigate: true);
        } else {
            RateLimiter::hit($throttleKey, 60);
            $remaining = RateLimiter::remaining($throttleKey, 5);
            $this->addError('email', 'Email atau kata sandi salah. (Sisa percobaan: ' . $remaining . ')');
        }
    }

    public function render()
    {
        return view('livewire.login');
    }
}
