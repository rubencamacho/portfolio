<?php

namespace Tests\Feature\Project;

use App\Livewire\Project\Project;
use App\Models\Project as ProjectModel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Termwind\Components\Li;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    /** @test **/
    public function project_component_can_be_rendered()
    {
        $this->get('/')
            ->assertStatus(200)
            ->assertSeeLivewire('project.project');
    }

    /** @test **/
    public function component_can_load_projects()
    {
        $projects = ProjectModel::factory(2)->create();

        Livewire::test(Project::class)
            ->assertSee($projects->first()->name)
            ->assertSee($projects->first()->image)
            ->assertSee($projects->last()->name)
            ->assertSee($projects->last()->image);
    }

    /** @test */
    public function user_can_see_all_project_info()
    {
        $project = ProjectModel::factory()->create([
            'image' => 'myproject.jpg',
            'video_link' => 'https://www.youtube.com/watch?v=vmuwGgdK4IU',
            'url' => 'https://www.cafedelprogramador.com/',
            'repo_url' => 'https://github.com/gamg/workshop-portfolio',
        ]);
        // dd($project->video_code);
        Livewire::test(Project::class)
            ->call('loadProject', $project->id)
            ->assertSee($project->name)
            ->assertSee($project->description)
            ->assertSee($project->image)
            ->assertSee($project->video_code)
            ->assertSee($project->url)
            ->assertSee($project->repo_url);
    }

    /** @test */
    public function only_admin_can_see_projects_actions()
    {
        $user = User::factory()->create();
        ProjectModel::factory(3)->create();


        Livewire::actingAs($user)
            ->test(Project::class)
            ->assertStatus(200)
            ->assertSee(__('New Project'))
            ->assertSee(__('Edit'))
            ->assertSee(__('Delete'));
    }

    /** @test */
    public function guests_cannot_see_projects_actions()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');

        // Livewire::test(Project::class)
        //     ->assertStatus(200)
        //     ->assertDontSee(__('New Project'))
        //     ->assertDontSee(__('Edit'))
        //     ->assertDontSee(__('Delete'));
        
        // $this->assertGuest();
    }

    /** @test */
    public function admin_can_add_a_project()
    {
        $user = User::factory()->create();
        $image = UploadedFile::fake()->image('myproject.jpg');
        Storage::fake('projects');

        Livewire::actingAs($user)
            ->test(Project::class)
            ->set('currentProject.name', 'My Project')
            ->set('currentProject.description', 'My Project Description')
            ->set('imageFile', $image)
            ->set('currentProject.video_link', 'https://www.youtube.com/watch?v=vmuwGgdK4IU')
            ->set('currentProject.url', 'https://www.cafedelprogramador.com/')
            ->set('currentProject.repo_url', 'https://github.com/rubencamacho/portfolio')
            ->call('save');

        $newProject = ProjectModel::first();

        $this->assertDatabaseHas('projects', [
            'id' => $newProject->id,
            'name' => 'My Project',
            'description' => 'My Project Description',
            'image' => $newProject->image,
            'video_link' => $newProject->video_link,
            'url' =>  $newProject->url,
            'repo_url' => $newProject->repo_url
        ]);

        Storage::disk('projects')->assertExists($newProject->image);
    }

    /** @test */
    public function admin_can_edit_a_project()
    {
        $user = User::factory()->create();
        $project = ProjectModel::factory()->create();
        $image = UploadedFile::fake()->image('myproject.jpg');
        Storage::fake('projects');

        Livewire::actingAs($user)
            ->test(Project::class)
            ->call('loadProject', $project->id)
            ->set('currentProject.name', 'My Project')
            ->set('currentProject.description', 'My Project Description')
            ->set('imageFile', $image)
            ->set('currentProject.video_link', 'https://www.youtube.com/watch?v=vmuwGgdK4IU')
            ->set('currentProject.url', 'https://www.cafedelprogramador.com/')
            ->set('currentProject.repo_url', 'https://github.com/rubencamacho/portfolio')
            ->call('save');

        $project->refresh();
        
        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'name' => 'My Project',
            'description' => 'My Project Description',
            'image' => $project->image,
            'video_link' => $project->video_link,
            'url' =>  $project->url,
            'repo_url' => $project->repo_url
        ]);

        Storage::disk('projects')->assertExists($project->image);
    }

    /** @test */
    public function admin_can_delete_a_project()
    {
        $user = User::factory()->create();
        $project = ProjectModel::factory()->create();
        $image = UploadedFile::fake()->image('myproject.jpg');
        Storage::fake('projects');

        Livewire::actingAs($user)
            ->test(Project::class)
            ->call('loadProject', $project->id)
            ->set('imageFile', $image)
            ->call('save');
        
        $project->refresh();

        Livewire::actingAs($user)
            ->test(Project::class)
            ->call('deleteProject', $project->id);

        $this->assertDatabaseMissing('projects', [
            'id' => $project->id
        ]);
        
        Storage::disk('projects')->assertMissing($project->image);
    }

    /** @test */
    public function name_is_required()
    {
        $user = User::factory()->create();
        
        Livewire::actingAs($user)
            ->test(Project::class)
            ->set('currentProject.name', '')
            ->call('save')
            ->assertHasErrors(['currentProject.name' => 'required']);
    }

    /** @test */
    public function name_must_be_max_100_characters()
    {
        $user = User::factory()->create();
        
        Livewire::actingAs($user)
            ->test(Project::class)
            ->set('currentProject.name', str_repeat('a', 101))
            ->call('save')
            ->assertHasErrors(['currentProject.name' => 'max']);
    }

    /** @test */
    public function description_is_required()
    {
        $user = User::factory()->create();
        
        Livewire::actingAs($user)
            ->test(Project::class)
            ->set('currentProject.description', '')
            ->call('save')
            ->assertHasErrors(['currentProject.description' => 'required']);
    }

    /** @test */
    public function description_must_be_max_450_characters()
    {
        $user = User::factory()->create();
        
        Livewire::actingAs($user)
            ->test(Project::class)
            ->set('currentProject.description', str_repeat('a', 451))
            ->call('save')
            ->assertHasErrors(['currentProject.description' => 'max']);
    }

    /** @test */
    public function image_must_be_an_image()
    {
        $user = User::factory()->create();
        $image = UploadedFile::fake()->create('myproject.pdf', 1024, 'application/pdf');

        Livewire::actingAs($user)
            ->test(Project::class)
            ->set('imageFile', $image)
            ->call('save')
            ->assertHasErrors(['imageFile' => 'image']);
    }

    /** @test */
    public function image_must_be_max_1024_kb()
    {
        $user = User::factory()->create();
        $image = UploadedFile::fake()->image('myproject.jpg')->size(1025);

        Livewire::actingAs($user)
            ->test(Project::class)
            ->set('imageFile', $image)
            ->call('save')
            ->assertHasErrors(['imageFile' => 'max']);
    }

    /** @test */
    public function video_link_must_be_a_valid_url()
    {
        $user = User::factory()->create();
        
        Livewire::actingAs($user)
            ->test(Project::class)
            ->set('currentProject.video_link', 'invalid_url')
            ->call('save')
            ->assertHasErrors(['currentProject.video_link' => 'url']);
    }

    /** @test */
    public function video_link_must_match_with_regex()
    {
        $user = User::factory()->create();
        
        Livewire::actingAs($user)
            ->test(Project::class)
            ->set('currentProject.video_link', 'https://www.youtube.com/watch?v=vmuwGgdK4IU')
            ->call('save')
            ->assertHasNoErrors(['currentProject.video_link' => 'regex']);
    }

    /** @test */
    public function url_must_be_a_valid_url()
    {
        $user = User::factory()->create();
        
        Livewire::actingAs($user)
            ->test(Project::class)
            ->set('currentProject.url', 'invalid_url')
            ->call('save')
            ->assertHasErrors(['currentProject.url' => 'url']);
    }

    /** @test */
    public function repo_url_must_be_a_valid_url()
    {
        $user = User::factory()->create();
        
        Livewire::actingAs($user)
            ->test(Project::class)
            ->set('currentProject.repo_url', 'invalid_url')
            ->call('save')
            ->assertHasErrors(['currentProject.repo_url' => 'url']);
    }

    /** @test */
    public function repo_url_must_match_with_regex()
    {
        $user = User::factory()->create();
        
        Livewire::actingAs($user)
            ->test(Project::class)
            ->set('currentProject.repo_url', 'https://nogithub.com/rubencamacho/portfolio')
            ->call('save')
            ->assertHasErrors(['currentProject.repo_url' => 'regex']);
    }
    
}
