<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\UserFilter;
use App\Http\Requests\Api\V1\ReplaceUserRequest;
use App\Http\Requests\Api\V1\StoreUserRequest;
use App\Http\Requests\Api\V1\UpdateUserRequest;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use App\Policies\V1\UserPolicy;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class UserController extends ApiController
{
    protected string $policy = UserPolicy::class;

    public function me()
    {
        return new UserResource(Auth::user());
    }

    /**
     * Get all users
     *
     * @group Users
     *
     * @queryParam sort string Data field(s) to sort by. Separate multiple fields with commas. Denote descending sort with a minus sign. Example: sort=name
     * @queryParam filter[name] Filter by status name. Wildcards are supported. No-example
     * @queryParam filter[email] Filter by email. Wildcards are supported. No-example
     *
     * */
    public function index(UserFilter $filter)
    {
        Gate::authorize('view', User::class);
        return UserResource::collection(
            User::filter($filter)->paginate()
        );
    }

    /**
     * Display a user
     *
     * @group Users
     *
     *
     */
    public function show(User $user)
    {
        Gate::authorize('show', User::class);
        return new UserResource($user);
    }

    /**
     *
     * Create a user
     *
     * @group Users
     *
     * @response 200 {"data":{"type":"user","id":16,"attributes":{"name":"My User","email":"user@user.com","isManager":false},"links":{"self":"http:\/\/localhost:8000\/api\/v1\/authors\/16"}}}
     */
    public function store(StoreUserRequest $request)
    {
        Gate::authorize('store', User::class);
        return new UserResource(User::create($request->mappedAttributes()));
    }

    /**
     * Replace a user
     *
     * @group Users
     *
     * @response 200 {"data":{"type":"user","id":16,"attributes":{"name":"My User","email":"user@user.com","isManager":false},"links":{"self":"http:\/\/localhost:8000\/api\/v1\/authors\/16"}}}
     */
    public function replace(ReplaceUserRequest $request, User $user)
    {
        Gate::authorize('replace', $user);

        $user->update($request->mappedAttributes());

        return new UserResource($user);
    }

    /**
     * Update a user
     *
     * @group Users
     *
     * @response 200 {"data":{"type":"user","id":16,"attributes":{"name":"My User","email":"user@user.com","isManager":false},"links":{"self":"http:\/\/localhost:8000\/api\/v1\/authors\/16"}}}
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        Gate::authorize('update', $user);

        $user->update($request->mappedAttributes());

        return new UserResource($user);
    }

    /**
     * Delete a user
     *
     * @group Users
     *
     * @response 200 {}
     */
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
