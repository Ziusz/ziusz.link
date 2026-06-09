<?php

namespace Database\Seeders;

use App\Link;
use Illuminate\Database\Seeder;

class LinkSeeder extends Seeder
{
    public function run(): void
    {
        foreach (range(1, 15) as $position) {
            Link::factory()
                ->sample($position)
                ->create();
        }
    }
}
