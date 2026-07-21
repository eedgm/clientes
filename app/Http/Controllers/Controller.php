<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function userHasRole($roles)
    {
        $user = Auth::user();
        $rol = false;
        foreach ($user->roles as $role) {
            if ($role->name == $roles) {
                $rol = true;
            }
        }

        return $rol;
    }
}
