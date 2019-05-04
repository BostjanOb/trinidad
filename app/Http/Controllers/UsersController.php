<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('index', User::class);

        $users = User::paginate(
            (int)$request->input('per_page', 25)
        );

        return new ResourceCollection($users);
    }

    public function store(Request $request)
    {
        $this->authorize('create', User::class);

        $userData = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string',
        ]);

        return new Resource(User::create($userData));
    }

    public function show(User $user)
    {
        $this->authorize('view', $user);

        return new Resource($user);
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $userData = $request->validate([
            'name'     => 'string|max:255',
            'email'    => ['email', Rule::unique('users')->ignore($user)],
            'password' => 'string',
        ]);

        $user->update($userData);

        return new Response($user);
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        $user->delete();

        return \Response::make(null, Response::HTTP_NO_CONTENT);
    }
}
