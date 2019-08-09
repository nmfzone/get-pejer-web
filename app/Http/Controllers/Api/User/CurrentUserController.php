<?php

namespace App\Http\Controllers\Api\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;

class CurrentUserController extends Controller
{
    /**
     * Display the logged in user data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function show(Request $request)
    {
        $user = $request->user();

        return $user;
    }
}
