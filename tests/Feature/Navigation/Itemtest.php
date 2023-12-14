<?php

namespace Tests\Feature\Navigation;

use App\Livewire\Navigation\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class Itemtest extends TestCase
{
    /** @test */
    public function item_can_be_rendered()
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)->test(Item::class)
            ->assertStatus(200);
    }

    /** @test */
    public function admin_can_add_item()
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)->test(Item::class)
            ->set('item.label', 'My label')
            ->set('item.link', 'My link')
            ->call('save');

        $this->assertDatabaseHas('navitems', ['label' => 'My label', 'link' => 'My link']);
    }

    /** @test */
    public function label_is_required()
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)->test(Item::class)
            ->set('item.label', '')
            ->set('item.link', '#myLink')
            ->call('save')
            ->assertHasErrors(['item.label' => 'required']);
    }

    /** @test */
    public function label_is_max_20_characters()
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)->test(Item::class)
            ->set('item.label', 'My label is more than 20 characters')
            ->set('item.link', '#myLink')
            ->call('save')
            ->assertHasErrors(['item.label' => 'max']);
    }   

    /** @test */
    public function link_is_required()
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)->test(Item::class)
            ->set('item.label', 'My link')
            ->set('item.link', '')
            ->call('save')
            ->assertHasErrors(['item.link' => 'required']);
    }   

    /** @test */
    public function link_is_max_40_characters()
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)->test(Item::class)
            ->set('item.label', 'My link')
            ->set('item.link', 'My link is more than 40 characters and this is a very long link')
            ->call('save')
            ->assertHasErrors(['item.link' => 'max']);
    }   
}
