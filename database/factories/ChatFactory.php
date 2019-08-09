<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Models\Chat;
use App\Models\User;
use App\Models\Group;
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
    $isToGroup = $faker->boolean(30);
    $sender = User::inRandomOrder()->first();

    if ($isToGroup) {
        $receivable = Group::inRandomOrder()->first();

        // Attach user to the group.
        if (! $receivable->users()->find($sender->id)) {
            $receivable->users()->save($sender);
        }
    } else {
        $receivable = User::where('id', '!=', $sender->id)->inRandomOrder()->first();
    }

    return [
        'message' => $faker->text,
        'sender_id' => $sender->id,
        'receivable_id' => $receivable->id,
        'receivable_type' => Relation::getClassNameAliasForMorph($receivable),
    ];
});
