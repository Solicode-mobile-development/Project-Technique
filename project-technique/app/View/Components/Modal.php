<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Modal extends Component
{
    public string $id;
    public string $title;
    public string $size;

    public function __construct(string $id, string $title, string $size = 'lg')
    {
        $this->id = $id;
        $this->title = $title;
        $this->size = $size;
    }

    public function render(): View|Closure|string
    {
        return view('components.modal');
    }
}
