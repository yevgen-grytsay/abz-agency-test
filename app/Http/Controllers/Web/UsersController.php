<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;

class UsersController extends Controller
{
    public function index(): View
    {
        return view('users', []);
    }

    public function show(string $id): View
    {
        return view('users_profile', [
            'user' => User::findOrFail($id)
        ]);
    }
}
