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
        // $this->markTestSkipped('This test has not been implemented yet.');
        
        Livewire::test(Info::class)
            ->assertStatus(200)
            ->assertDontSee(__('Edit'));
        
        $this->assertGuest();
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

        Livewire::actingAs($user)->test(Info::class)
            ->set('info.title', 'Adolfo Gutierrez')
            ->set('info.description', 'Software Developer in Laravel PHP')
            ->set('cvFile', $cv)
            ->set('imageFile', $image)
            ->call('edit');

        $info->refresh();

        $this->assertDatabaseHas('personal_information', [
            'id' => $info->id,
            'title' => 'Adolfo Gutierrez',
            'description' => 'Software Developer in Laravel PHP',
            'cv' => $info->cv,
            'image' => $info->image,
        ]);

        Storage::disk('hero')->assertExists($info->image);
        Storage::disk('cv')->assertExists($info->cv);
    }

    /** @test */
    public function can_download_cv()
    {
        Livewire::test(Info::class)
            ->call('download')
            ->assertFileDownloaded('my-cv.pdf');
    }

    /** @test */
    public function title_is_required()
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)->test(Info::class)
            ->set('info.title', '')
            ->set('info.description', 'This is a description')
            ->call('edit')
            ->assertHasErrors(['info.title' => 'required'])
            ->assertHasNoErrors(['info.description' => 'required']);
    }

    /** @test */
    public function title_must_have_a_maximum_of_eighty_characters()
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)->test(Info::class)
            ->set('info.title', str_repeat('a', 81))
            ->set('info.description', 'This is a description')
            ->call('edit')
            ->assertHasErrors(['info.title' => 'max'])
            ->assertHasNoErrors(['info.description' => 'max']);
    }

    /** @test */
    public function description_is_required()
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)->test(Info::class)
            ->set('info.title', 'This is a title')
            ->set('info.description', '')
            ->call('edit')
            ->assertHasErrors(['info.description' => 'required'])
            ->assertHasNoErrors(['info.title' => 'required']);  
    }

    /** @test */
    public function description_must_have_a_maximum_of_two_hundred_fifty_characters()
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)->test(Info::class)
            ->set('info.title', 'This is a title')
            ->set('info.description', str_repeat('a', 251))
            ->call('edit')
            ->assertHasErrors(['info.description' => 'max'])
            ->assertHasNoErrors(['info.title' => 'max']);
    }

    /** @test */
    public function cv_file_must_be_a_pdf()
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)->test(Info::class)
            ->set('info.title', 'This is a title')
            ->set('info.description', 'This is a description')
            ->set('cvFile', UploadedFile::fake()->image('cv.jpg'))
            ->call('edit')
            ->assertHasErrors(['cvFile' => 'mimes']);
    }

    /** @test */
    public function cv_file_must_be_max_one_megabyte()
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)->test(Info::class)
            ->set('info.title', 'This is a title')
            ->set('info.description', 'This is a description')
            ->set('cvFile', UploadedFile::fake()->create('cv.pdf', 1025))
            ->call('edit')
            ->assertHasErrors(['cvFile' => 'max']);
    }

    /** @test */
    public function image_file_must_be_an_image()
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)->test(Info::class)
            ->set('info.title', 'This is a title')
            ->set('info.description', 'This is a description')
            ->set('imageFile', UploadedFile::fake()->create('image.pdf'))
            ->call('edit')
            ->assertHasErrors(['imageFile' => 'image']);
    }

    /** @test */
    public function image_file_must_be_max_one_megabyte()
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)->test(Info::class)
            ->set('info.title', 'This is a title')
            ->set('info.description', 'This is a description')
            ->set('imageFile', UploadedFile::fake()->image('image.jpg')->size(2025))
            ->call('edit')
            ->assertHasErrors(['imageFile' => 'max']);
    }
}