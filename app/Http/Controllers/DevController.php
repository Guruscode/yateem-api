<?php

namespace App\Http\Controllers;

use App\Models\Guardians;
use App\Models\User;
use Illuminate\Http\Request;

class DevController extends Controller
{
    public function index()
    {
        $users = User::all();
        return response()->json(['users' => $users], 200);
        $guard = Guardians::all();
        return response()->json(['guard' => $guard], 200);
    }
}
