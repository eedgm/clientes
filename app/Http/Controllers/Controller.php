<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function userHasRole($roles) {
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
