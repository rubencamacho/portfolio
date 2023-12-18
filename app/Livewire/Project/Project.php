<?php

namespace App\Livewire\Project;

use App\Models\Project as ModelsProject;
use Livewire\Component;

class Project extends Component
{
    public function render()
    {
        $projects = ModelsProject::get();
        return view('livewire.project.project', compact('projects'));
    }
}
