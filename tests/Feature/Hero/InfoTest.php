<?php

namespace Tests\Feature\Hero;

use App\Models\PersonalInformation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Livewire\Hero\Info;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use Livewire\Livewire; 
class InfoTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function hero_info_component_can_be_rendered()
    {
        $this->get('/')->assertStatus(200)->assertSeeLivewire('hero.info');
    }

    /** @test */
    public function component_cant_load_hero_information()
    {
        $info = PersonalInformation::factory()->create();

        Livewire::test(Info::class)
                ->assertSee($info->title)
                ->assertSee($info->description);
    }

    /** @test */ 
    public function only_admin_can_see_hero_action()
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Info::class)
            ->assertStatus(200)
            ->assertSee(__('Edit'));
    }

    /** @test */
    public function guests_cannot_see_hero_action()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
        // $this->markTestSkipped('This test has not been implemented yet.');
        
        // Livewire::test(Info::class)
        //     ->assertStatus(200)
        //     ->assertDontSee(__('Edit'));
        
        // $this->assertGuest();
    }

    /** @test */
    public function admin_can_edit_hero_info()
    {
        $user = User::factory()->create();
        $info = PersonalInformation::factory()->create();
        $image = UploadedFile::fake()->image('heroimage.jpg');
        $cv = UploadedFile::fake()->create('curriculum.pdf');
        Storage::fake('hero');
        Storage::fake('cv');

        Livewire::actingAs($user)
            ->test(Info::class)
            ->set('info.title', 'Rubén Camacho')
            ->set('info.description', 'Software Developer in Laravel PHP')
            ->set('imageFile', $image)
            ->set('cvFile', $cv)
            ->call('edit');
        
        $info->refresh();
    
        $this->assertDatabaseHas('personal_information', [
            'ID' => $info->ID,
            'title' => 'Rubén Camacho',
            'description' => 'Software Developer in Laravel PHP',
            'image' => $info->image,
            'cv' => $info->cv,
        ]);

        Storage::disk('hero')->assertExists($info->image);
        Storage::disk('cv')->assertExists($info->cv);

    }
}