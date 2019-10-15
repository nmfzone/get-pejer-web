<?php

use App\Models\Group;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chats.groups.{groupId}', function ($user, $groupId) {
    $group = Group::findOrFail($groupId);

    return ! is_null($group->participants()->find($user->id));
});

Broadcast::channel('chats.users.{senderId}.to.{receiverId}', function ($user, $senderId, $receiverId) {
    return (int) $user->id === (int) $senderId || (int) $user->id === (int) $receiverId;
});

Broadcast::channel('chats.all.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
