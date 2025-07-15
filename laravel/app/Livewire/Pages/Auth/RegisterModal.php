<?php

namespace App\Livewire\Pages\Auth;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Component;
use Livewire\Attributes\Validate;

class RegisterModal extends Component
{
    public string $name = '';
    
    #[Validate('required|string|lowercase|email|max:255|unique:users')]
    public string $email = '';
    
    #[Validate('required|string|min:8|confirmed')]
    public string $password = '';
    
    public string $password_confirmation = '';
    
    public bool $showForm = false;

    public function checkEmail()
    {
        $this->reset('showForm');
        if ($this->email && filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $this->showForm = true;
        }
    }

    public function register()
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);
        
        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);
        
        if(config('app.email_verification')){
            $user->email_verified_at = null;
            $user->save();
        }
        
        event(new Registered($user));
        
        Auth::login($user);
        
        if ($plan = Plan::where('is_free', 1)->where('status', 1)->first()) {
            $user->cancelCurrentSubscription();
            $user->upgradeCurrentPlanTo($plan, 2999, false, false);
            
            $plan_history = new \App\Models\PlansHistory;
            $plan_history->plan_id = $plan->id;
            $plan_history->user_id = $user->id;
            $plan_history->save();
        }
        
        return redirect()->intended(route('console-index'));
    }

    public function render()
    {
        return view('livewire.pages.auth.register-modal');
    }
} 