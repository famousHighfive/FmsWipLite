<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Inertia\Inertia;

class RoleController extends Controller
{
    public function index()
    {
        return Inertia::render('Roles/Index', [
            'roles' => Role::withCount('users')->get()
        ]);
    }
}
