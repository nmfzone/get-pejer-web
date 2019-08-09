<?php

use App\Models\Chat;
use Illuminate\Database\Seeder;

class ChatsTableSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        factory(Chat::class, 100)->create();
    }
}
