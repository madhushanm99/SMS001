<?php

namespace App\View\Components\Customer\Layouts;

use Illuminate\View\Component;

class App extends Component
{
    /**
     * The page title.
     *
     * @var string
     */
    public $title;

    /**
     * Create a new component instance.
     *
     * @param  string|null  $title
     * @return void
     */
    public function __construct($title = null)
    {
        $this->title = $title ?? 'Customer Portal';
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.customer.layouts.app');
    }
} 