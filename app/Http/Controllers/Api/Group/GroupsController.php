<?php

namespace App\Http\Controllers\Api\Group;

use App\Models\Group;
use Illuminate\Http\Request;
use App\Transformers\GroupTransformer;
use App\Http\Controllers\Api\Controller;

class GroupsController extends Controller
{
    /**
     * Show the group detail.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Group  $group
     * @return mixed
     *
     * @throws \Exception
     */
    public function show(Request $request, Group $group)
    {
        $group = $this->preprocessResource($group, GroupTransformer::class);

        return GroupTransformer::make($group);
    }
}
