<?php

namespace App\Repository;

use App\Http\Requests\ProfileRequest;
use App\Models\User;

class UserRepository
{
    public static function create(string $mobile)
    {
        return User::firstOrCreate(['mobile' => $mobile]);
    }

    public static function getUserByMobile(string $mobile)
    {
        return User::where(['mobile' => $mobile])->first();
    }

    public static function update(ProfileRequest  $request, User $user)
    {
        $user->first_name = $request->get('first_name');
        $user->last_name = $request->get('last_name');
        $user->save();
    }
}
