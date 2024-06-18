<?php

namespace App\View\Components\shop;

use Illuminate\View\Component;

class Button extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct (
        public $href = '',
        public $style = 'primary',
        public $type = 'anchor',
        public $id = '',
        public $btn = [
            'type' => '',
            'name' => ''
        ]
    ) {}

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $style = [
            'primary' => 'inline-block bg-teal-300 text-sky-50 text-xs rounded-sm py-3 px-5 font-bold hover:bg-gray-800 transition-all hover:text-white',
            'secondary' => 'inline-block bg-gray-50 text-gray-500 text-xs rounded py-3 px-5 hover:bg-gray-200 transition-all'
        ];

        return view('components.shop.button', [
            'classes' => $style[$this->style]
        ]);
    }
}
