<?php

namespace App\Observers;

use App\Models\Group;
use Illuminate\Support\Str;

class GroupObserver
{
    /**
     * Listen to the User created event.
     *
     * @param \App\Models\Group  $group
     * @return void
     */
    public function creating(Group $group)
    {
        $group->code = Str::uuid()->toString();
    }
}
