<?php

namespace App\Livewire\Navigation;

use App\Models\Navitem;
use Livewire\Component;

class Navigation extends Component
{
    public $items;
    public $openSlideover = false;
    public $addNewItem = false;

    public function mount()
    {
        $this->items = Navitem::all();
    }

    public function openSlide($addNewItem = false)
    {
        $this->addNewItem = $addNewItem;
        $this->openSlideover = true;

        // Imprimir mensaje en la consola para depuración
        // dd($this->addNewItem);
    }

    public function render()
    {
        return view('livewire.navigation.navigation');

        // return view('livewire.navigation.navigation', [
        //     'openSlideOver' => $this->openSlideOver,
        // ]);
    }
}
