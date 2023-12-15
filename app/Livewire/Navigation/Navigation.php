<?php

namespace App\Livewire\Navigation;

use App\Models\Navitem;
use Livewire\Component;
use App\Livewire\Traits\Slideover;
use App\Livewire\Traits\Notefication;


class Navigation extends Component
{
    use Notefication, Slideover;

    public $items;
    public $openSlideover = false;
    public $addNewItem = false;

    protected $listeners = ['deleteItem', 'itemAdded' => 'updateDataAfterAddItem'];
    
    protected $rules = [
        'items.*.label' => 'required|string|min:3|max:20',
        'items.*.link' => 'required|max:40',
    ];

    public function mount()
    {
        $this->items = Navitem::all();
    }

    public function updateDataAfterAddItem()
    {
        $this->mount();
        $this->reset('openSlideover');
    }

    public function edit()
    {
        $this->validate();

        foreach ($this->items as $item) {
            $item->save();
        }

        $this->reset('openSlideover');
        // notify
        $this->notify(__("Menu items updated successfully"));
    }

    public function deleteItem(Navitem $item)
    {
    
        $item->delete();
        $this->items = Navitem::all();
        $this->notify(__("Menu item has been deleted."), 'deleteMessage');
    }


    public function render()
    {
        return view('livewire.navigation.navigation');
    }
}
