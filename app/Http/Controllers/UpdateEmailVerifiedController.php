<?php

namespace App\Http\Controllers;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;

class UpdateEmailVerifiedController extends BaseController
{
    public function __invoke($email)
    {
        User::where('email', base64_decode($email))->update(['email_verified_at' => now()]);

        return redirect('/login?result=Email verification successful');
    }
}
