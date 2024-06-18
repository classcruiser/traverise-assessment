<?php

namespace App\View\Components\Shop;

use Illuminate\View\Component;

class Heading extends Component
{
    public array $headings;
    
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        public int $step
    )
    {
        $this->headings = [
            '1' => ['label' => 'Select Class', 'url' => '/book-class'],
            '2' => ['label' => 'Select Add-ons', 'url' => '/book-class/addons'],
            '3' => ['label' => 'Enter your details', 'url' => '/book-class/details'],
            '4' => ['label' => 'Confirm and pay', 'url' => '/book-class/confirm'],
        ];
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.shop.heading');
    }
}
