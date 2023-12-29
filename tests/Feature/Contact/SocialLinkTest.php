<?php

namespace Tests\Feature\Contact;

use App\Livewire\Contact\SocialLink;
use App\Models\SocialLink as ModelsSocialLink;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class SocialLinkTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function social_link_component_can_be_rendered()
    {
        $this->get('/')
            ->assertStatus(200)
            ->assertSeeLivewire('contact.social-link');
    }

    /** @test */
    public function component_can_load_social_links()
    {
        $links = ModelsSocialLink::factory(3)->create();

        Livewire::test(SocialLink::class)
            ->assertSee($links->first()->url)
            ->assertSee($links->first()->icon)
            ->assertSee($links->last()->url)
            ->assertSee($links->last()->icon);
    }

    /** @test */
    public function only_admin_can_see_social_links_actions()
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(SocialLink::class)
            ->assertStatus(200)
            ->assertSee(__('New'))
            ->assertSee(__('Edit'));

    }

    /** @test */
    public function guest_cannot_see_social_links_actions()
    {
        // $this->markTestSkipped('This test has not been implemented yet.');
        Livewire::test(SocialLink::class)
            ->assertStatus(200)
            ->assertDontSee(_('New'))
            ->assertDontSee(__('Edit'));
        
        $this->assertGuest();
    }

    /** @test */
    public function admin_can_add_a_social_link()
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)->test(SocialLink::class)
            ->set('socialLink.name', 'Youtube')
            ->set('socialLink.url', 'https://youtube.com/profile')
            ->set('socialLink.icon', 'fa-brands fa-youtube')
            ->call('save');

        $this->assertDatabaseHas('social_links', [
            'name' => 'Youtube',
            'url' => 'https://youtube.com/profile',
            'icon' => 'fa-brands fa-youtube'
        ]);
    }

    /** @test */
    public function admin_can_edit_a_social_link()
    {
        $user = User::factory()->create();

        $link = ModelsSocialLink::factory()->create();

        Livewire::actingAs($user)->test(SocialLink::class)
            ->set('socialLinkSelected', $link->id)
            ->set('socialLink.name', 'Youtube')
            ->set('socialLink.url', 'https://youtube.com/profile')
            ->set('socialLink.icon', 'fa-brands fa-youtube')
            ->call('save');

        $link->refresh();

        $this->assertDatabaseHas('social_links', [
            'id' => $link->id,
            'name' => 'Youtube',
            'url' => 'https://youtube.com/profile',
            'icon' => $link->icon
        ]);
    }

    /** @test */
    public function admin_can_delete_a_social_link()
    {
        $user = User::factory()->create();

        $link = ModelsSocialLink::factory()->create();

        Livewire::actingAs($user)->test(SocialLink::class)
            ->set('socialLinkSelected', $link->id)
            ->call('deleteSocialLink');

        $this->assertDatabaseMissing('social_links', [
            'id' => $link->id
        ]);
    }

    /** @test */
    public function social_link_name_is_required()
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)->test(SocialLink::class)
            ->set('socialLink.name', '')
            ->call('save')
            ->assertHasErrors(['socialLink.name' => 'required']);
    }

    /** @test */
    public function name_must_have_a_maximum_of_twenty_characters()
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)->test(SocialLink::class)
            ->set('socialLink.name', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, voluptate.')
            ->call('save')
            ->assertHasErrors(['socialLink.name' => 'max']);
    }

    /** @test */
    public function social_link_url_is_required()
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)->test(SocialLink::class)
            ->set('socialLink.url', '')
            ->call('save')
            ->assertHasErrors(['socialLink.url' => 'required']);
    }

    /** @test */
    public function social_link_url_must_be_a_valid_url()
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)->test(SocialLink::class)
            ->set('socialLink.url', 'invalid-url')
            ->call('save')
            ->assertHasErrors(['socialLink.url' => 'url']);
    }

    /** @test */
    public function social_link_icon_must_be_a_valid_font_awesome_icon()
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)->test(SocialLink::class)
            ->set('socialLink.icon', 'invalid-icon')
            ->call('save')
            ->assertHasErrors(['socialLink.icon' => 'regex']);
    }

    /** @test */
    public function icon_must_match_with_regex()
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)->test(SocialLink::class)
            ->set('socialLink.icon', 'fa-fa fa-face-smile-wink')
            ->call('save')
            ->assertHasErrors(['socialLink.icon' => 'regex']);
    }
}
