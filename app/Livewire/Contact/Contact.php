<?php

namespace App\Livewire\Contact;

use App\Livewire\Traits\Notefication;
use Livewire\Component;
use App\Models\PersonalInformation;
use App\Livewire\Traits\Slideover;

class Contact extends Component
{
    use Slideover, Notefication;

    public PersonalInformation $contact;

    protected $rules = [
        'contact.email' => 'required|email:filter',
    ];

    public function mount()
    {
        $this->contact = PersonalInformation::first() ?? new PersonalInformation();
    }

    public function edit()
    {
        $this->validate();

        $this->contact->save();

        $this->reset('openSlideover');

        $this->notify(__('Contact email updated successfully.'));
    }

    public function render()
    {
        return view('livewire.contact.contact');
    }
}
