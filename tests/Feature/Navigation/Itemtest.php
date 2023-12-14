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
}
