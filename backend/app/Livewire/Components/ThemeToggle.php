<?php

namespace App\Livewire\Components;

use Livewire\Component;

class ThemeToggle extends Component
{
    public $theme;

    public function mount()
    {
        $this->theme = session('theme', 'light');
    }

    public function toggleTheme()
    {
        $this->theme = $this->theme === 'light' ? 'dark' : 'light';
        session(['theme' => $this->theme]);
        $this->dispatch('theme-changed', theme: $this->theme);
    }

    public function render()
    {
        return view('livewire.components.theme-toggle');
    }
}