<?php

namespace App\Observers;

use App\Models\Group;
use App\Garage\Utility\Helper;

class GroupObserver
{
    /**
     * Listen to the Group created event.
     *
     * @param \App\Models\Group  $group
     * @return void
     *
     * @throws \Exception
     */
    public function creating(Group $group)
    {
        $group->code = Helper::generateUniqueUuid(Group::class, 'code');
    }
}
