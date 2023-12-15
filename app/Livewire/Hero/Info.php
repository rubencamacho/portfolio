<?php

namespace App\Livewire\Hero;

use App\Models\PersonalInformation;
use Livewire\Component;
use App\Livewire\Traits\Slideover;
use App\Livewire\Traits\WidthImageFile;
use App\Livewire\Traits\Notefication;
use Livewire\WithFileUploads;

class Info extends Component
{
    use Slideover, WithFileUploads, WidthImageFile, Notefication;

    public PersonalInformation $info;
    public $cvFile = null;
    public $imageFile = null;
    
    protected $rules = [
        'info.title' => 'required|max:80',
        'info.description' => 'required|max:250',
        'cvFile' => 'nullable|mimes:pdf|max:1024',
        'imageFile' => 'nullable|image|max:1024',
    ];

    public function updatedCvFile()
    {
        $this->validate([
            'cvFile' => 'mimes:pdf|max:1024'
        ]);
    }

    public function mount()
    {
        $this->info = PersonalInformation::first() ?? new PersonalInformation();
    }

    public function edit()
    {
        $this->validate();

        $this->info->save();

        if ($this->cvFile) {
            $this->deleteFile(disk: 'cv', file: $this->info->cv);
            $this->info->update(['cv' => $this->cvFile->store('/', 'cv')]);
        }

        if ($this->imageFile) {
            $this->deleteFile(disk: 'hero', file: $this->info->image);
            $this->info->update(['image' => $this->imageFile->store('/', 'hero')]);
        }

        $this->resetExcept('info');
        $this->notify(__('Information saved successfully'));
    }

    public function render()
    {
        return view('livewire.hero.info');
    }
}
