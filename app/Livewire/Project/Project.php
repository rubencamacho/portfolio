<?php

namespace App\Livewire\Project;

use App\Models\Project as ModelsProject;
use Livewire\Component;

class Project extends Component
{
    public ModelsProject $currentProject;
    public bool $openModal = false;

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
    }

    public function render()
    {
        $projects = ModelsProject::get();
        return view('livewire.project.project', compact('projects'));
    }
}
