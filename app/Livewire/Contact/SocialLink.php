<?php

namespace App\Livewire\Contact;

use App\Livewire\Traits\Notefication;
use App\Livewire\Traits\Notification;
use App\Livewire\Traits\Slideover;
use Livewire\Component;
use App\Models\SocialLink as SocialLinkModel;

class SocialLink extends Component
{
    use Slideover, Notefication;

    public SocialLinkModel $socialLink;
    public $socialLinkSelected = '';

    protected $rules = [
        'socialLink.name' => 'required|max:20',
        'socialLink.url' => 'required|url',
        'socialLink.icon' => ['nullable', 'regex:/^(fa-brands|fa-solid)\sfa-[a-z-]+/i'],
    ];

    public function updatedSocialLinkSelected()
    {
        $data = SocialLinkModel::find($this->socialLinkSelected);
        if ($data) {
            $this->socialLink = $data;
        } else {
            $this->socialLinkSelected = '';
        }
    }

    // Un nuevo método específicamente para manejar el evento change.
    public function handleSocialLinkChange()
    {
        $this->updatedSocialLinkSelected($this->socialLinkSelected);
    }

    public function mount()
    {
        $this->socialLink = new SocialLinkModel();
    }

    public function create()
    {
        if ($this->socialLink->getKey()) {
            $this->socialLink = new SocialLinkModel();
            $this->reset('socialLinkSelected');
        }

        $this->openSlide(true);
    }

    public function save()
    {
        $this->validate();

        $this->socialLink->save();

        $this->reset(['openSlideover', 'socialLinkSelected']);

        $this->notify(__('Social link saved successfully!'));
    }

    public function render()
    {
        $socialLinks = SocialLinkModel::get();
        return view('livewire.contact.social-link', ['socialLinks' => $socialLinks]);
    }
}