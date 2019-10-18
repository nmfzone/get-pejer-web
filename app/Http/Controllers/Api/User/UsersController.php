<?php

namespace App\Http\Controllers\Api\User;

use App\Models\User;
use App\Events\UserOnline;
use Illuminate\Http\Request;
use App\Transformers\UserTransformer;
use App\Http\Controllers\Api\Controller;
use Illuminate\Support\Facades\Cache;

class UsersController extends Controller
{
    /**
     * Show the user detail.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return mixed
     *
     * @throws \Exception
     */
    public function show(Request $request, User $user)
    {
        $user = $this->preprocessResource($user, UserTransformer::class);

        return UserTransformer::make($user);
    }

    /**
     * Store user online status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     *
     * @throws \Exception
     */
    public function storeOnlineStatus(Request $request)
    {
        event(new UserOnline($request->user()));

        return $this->response([
            'message' => 'You\'re online now.',
        ]);
    }
}
