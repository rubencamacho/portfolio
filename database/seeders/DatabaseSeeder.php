<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;


use App\Models\Navitem;
use App\Models\PersonalInformation;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::factory()->create([
            'name' => 'Ruben',
            'email' => 'ruben.techproperties@gmail.com',
        ]);

        Navitem::factory()->create([
            'label' => 'Hola',
            'link'  => '#hola',
        ]);

        Navitem::factory()->create([
            'label' => 'Projectos',
            'link'  => '#projectos',
        ]);

        Navitem::factory()->create([
            'label' => 'Contacto',
            'link'  => '#contacto',
        ]);

        PersonalInformation::factory()->create([
            'title' => 'Desarrollo y Diseño Web',
            'description' => 'Rubén: Pasión por web, PHP/JS, explorador de Laravel y tecnologías nuevas.',
            'cv' => null,
            'image' => null,
            'email' => 'rubencamachorguez@gmail.com'
        ]);
    }
}
