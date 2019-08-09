<?php

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        factory(User::class)->create([
            'email' => 'demo@getpejer.com',
        ]);

        factory(User::class)->create([
            'email' => 'client@getpejer.com',
        ]);

        factory(User::class, 20)->create();
    }
}
