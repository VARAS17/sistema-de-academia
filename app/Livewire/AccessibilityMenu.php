<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Cookie;

class AccessibilityMenu extends Component
{
    public string $colorMode = 'normal'; // normal | protanopia | deuteranopia | tritanopia | high-contrast
    public string $fontSize = 'base';    // sm | base | lg | xl | 2xl

    public function mount()
    {
        $this->colorMode = Cookie::get('color_mode', 'normal');
        $this->fontSize = Cookie::get('font_size', 'base');
    }

    public function setColorMode(string $mode)
    {
        $this->colorMode = $mode;
        Cookie::queue('color_mode', $mode, 60 * 24 * 365);
        $this->dispatch('color-mode-changed', mode: $mode);
    }

    public function increaseFont()
    {
        $sizes = ['sm', 'base', 'lg', 'xl', '2xl'];
        $i = array_search($this->fontSize, $sizes);
        if ($i !== false && $i < count($sizes) - 1) {
            $this->fontSize = $sizes[$i + 1];
            Cookie::queue('font_size', $this->fontSize, 60 * 24 * 365);
            $this->dispatch('font-size-changed', size: $this->fontSize);
        }
    }

    public function decreaseFont()
    {
        $sizes = ['sm', 'base', 'lg', 'xl', '2xl'];
        $i = array_search($this->fontSize, $sizes);
        if ($i !== false && $i > 0) {
            $this->fontSize = $sizes[$i - 1];
            Cookie::queue('font_size', $this->fontSize, 60 * 24 * 365);
            $this->dispatch('font-size-changed', size: $this->fontSize);
        }
    }

    public function resetFont()
    {
        $this->fontSize = 'base';
        Cookie::queue('font_size', 'base', 60 * 24 * 365);
        $this->dispatch('font-size-changed', size: 'base');
    }

    public function render()
    {
        return view('livewire.accessibility-menu');
    }
}