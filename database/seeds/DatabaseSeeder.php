<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call(
            TagsSeeder::class,
        );
        $this->call(
            UserSeeder::class,
        );
        $this->call(
            PostSeeder::class,
        );
        $this->call(
            WSSeeder::class,
        );

        echo "---- DONE SEEDING ----\n";
    }
}
