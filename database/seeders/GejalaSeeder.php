<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Gejala;

class GejalaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Generate 10 data dummy menggunakan factory
        Gejala::factory()->count(10)->create();

        // Atau contoh manual:
        // Gejala::create([
        //     'title' => 'Demam',
        //     'description' => 'Suhu tubuh meningkat di atas normal.',
        //     'level' => 2,
        // ]);
    }
}
