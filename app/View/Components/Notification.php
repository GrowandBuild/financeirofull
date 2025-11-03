<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Notification extends Component
{
    public $type;
    public $message;
    public $autoClose;
    
    public function __construct($type = 'info', $message = '', $autoClose = true)
    {
        $this->type = $type;
        $this->message = $message;
        $this->autoClose = $autoClose;
    }
    
    public function render()
    {
        return view('components.notification');
    }
}

