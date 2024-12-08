<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\AuthorFilter;
use App\Http\Requests\Api\V1\ReplaceUserRequest;
use App\Http\Requests\Api\V1\StoreUserRequest;
use App\Http\Requests\Api\V1\UpdateUserRequest;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use App\Policies\V1\UserPolicy;
use Illuminate\Support\Facades\Gate;

class UserController extends ApiController
{
    protected string $policy = UserPolicy::class;

    public function index(AuthorFilter $filter)
    {
        Gate::authorize('view', User::class);
        return UserResource::collection(
            User::filter($filter)->paginate()
        );
    }

    public function show(User $user)
    {
        Gate::authorize('show', User::class);
        return new UserResource($user);
    }

    public function store(StoreUserRequest $request)
    {
        Gate::authorize('store', User::class);
        return new UserResource(User::create($request->mappedAttributes()));
    }

    public function replace(ReplaceUserRequest $request, User $user)
    {
        Gate::authorize('replace', $user);

        $user->update($request->mappedAttributes());

        return new UserResource($user);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        Gate::authorize('update', $user);

        $user->update($request->mappedAttributes());

        return new UserResource($user);
    }

    public function destroy(User $user)
    {
        Gate::authorize('delete', $user);
        if ($user->tickets()->exists()) {
            return $this->error('User has associated tickets and cannot be deleted.', 400);
        }

        $user->delete();

        return $this->ok('User deleted');
    }
}
