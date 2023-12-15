<?php

namespace App\Livewire\Traits;

trait Slideover
{
    public $openSlideover = false;
    public $addNewItem = false;

    public function openSlide($addNewItem = false)
    {
        $this->addNewItem = $addNewItem;
        $this->openSlideover = true;
    }
}