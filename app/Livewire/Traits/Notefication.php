<?php
namespace App\Livewire\Traits;

trait Notefication
{
    public function notify($message = '', $event = 'notify')
    {
        // $this->dispatchBrowserEvent('notify', ['message' => $message]);
        $this->dispatch($event, message: $message);
    }

}