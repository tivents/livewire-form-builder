<?php

namespace Tivents\LivewireFormBuilder\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class Layout extends Component
{
    public function __construct(
        public string $title = 'Form Architect'
    ) {}

    public function render(): View
    {
        return view('livewire-form-builder::components.layout');
    }
}
