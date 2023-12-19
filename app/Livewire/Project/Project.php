<?php

namespace App\Livewire\Project;

use App\Livewire\Traits\Notefication;
use App\Livewire\Traits\Slideover;
use App\Livewire\Traits\WidthImageFile;
use App\Models\Project as ModelsProject;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;

class Project extends Component
{
    use Slideover, WidthImageFile, WithFileUploads, Notefication;

    public ModelsProject $currentProject;
    public bool $openModal = false;

    protected $rules = [
        'currentProject.name' => 'required|max:100',
        'currentProject.description' => 'required|max:450',
        'imageFile' => 'nullable|image|max:1024',
        'currentProject.video_link' => ['nullable', 'url', 'regex:/^(https|http):\/\/(www\.)?(youtube\.com\/watch\?v=|youtu\.be\/)[A-z0-9-]+/i'],
        'currentProject.url' => 'nullable|url',
        'currentProject.repo_url' => ['nullable', 'url', 'regex:/^(https|http):\/\/(www\.)?(github|gitlab)\.com\/[A-z0-9-\/?=&]+/i'],
    ];

    public function mount()
    {
        $this->currentProject = new ModelsProject();
    }

    public function loadProject(ModelsProject $project, $modal = true)
    {
        if($this->currentProject->isNot($project)) {
            $this->currentProject = $project;
        }

        $this->openModal = $modal;

        if(!$modal){
            $this->openSlide();
        }
    }

    public function create()
    {
        if($this->currentProject->exists) {
            $this->currentProject = new ModelsProject();
        }

        $this->openSlide();
    }

    public function save()
    {
        $this->validate();

        if($this->imageFile) {
            //Si existe un archivo de imagen, se elimina el anterior
            $this->deleteFile('projects', $this->currentProject->image);

            $this->currentProject->image = $this->imageFile->store('/', 'projects');
        }

        $this->currentProject->save();

        $this->reset(['imageFile', 'openSlideover']);
        $this->notify(__('Project saved successfully'));
    }


    public function render()
    {
        $projects = ModelsProject::get();
        return view('livewire.project.project', compact('projects'));
    }
}
