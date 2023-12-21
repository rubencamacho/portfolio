<?php

namespace App\Livewire\Contact;

use Livewire\Component;
use App\Models\SocialLink as SocialLinkModel;

class SocialLink extends Component
{

    public function render()
    {
        $socialLinks = SocialLinkModel::get();
        return view('livewire.contact.social-link', compact('socialLinks'));
    }
}
