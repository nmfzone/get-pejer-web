<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Models\Chat;
use App\Models\User;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Relations\Relation;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(Chat::class, function (Faker $faker) {
    $sender = User::inRandomOrder()->first();
    $receiver = User::where('id', '!=', $sender->id)->inRandomOrder()->first();

    return [
        'message' => $faker->text,
        'sender_id' => $sender->id,
        'receiver_id' => $receiver->id,
    ];
});
