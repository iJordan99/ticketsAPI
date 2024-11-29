<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\AuthorFilter;
use App\Http\Requests\Api\V1\ReplaceUserRequest;
use App\Http\Requests\Api\V1\StoreUserRequest;
use App\Http\Requests\Api\V1\UpdateUserRequest;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use App\Policies\V1\UserPolicy;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Gate;

class UserController extends ApiController
{
    protected string $policy = UserPolicy::class;

    public function index(AuthorFilter $filter)
    {
        return UserResource::collection(
            User::filter($filter)->paginate()
        );
    }

    public function store(StoreUserRequest $request)
    {
        try {
            Gate::authorize('store', User::class);
            return new UserResource(User::create($request->mappedAttributes()));
        } catch (AuthorizationException $exception) {
            return $this->error($exception->getMessage(), 404);

        }
    }

    public function show($user_id)
    {
        try {

            $user = User::findOrFail($user_id);


            return new UserResource($user);

        } catch (ModelNotFoundException $exception) {
            return $this->error($exception->getMessage(), 404);
        }
    }

    public function replace(ReplaceUserRequest $request, $user_id)
    {
        try {
            $user = User::findOrFail($user_id);

            Gate::authorize('replace', $user);

            $user->update($request->mappedAttributes());

            return new UserResource($user);

        } catch (ModelNotFoundException $exception) {
            return $this->error($exception->getMessage(), 404);
        } catch (AuthorizationException $exception) {
            return $this->error($exception->getMessage(), 403);
        }
    }

    public function update(UpdateUserRequest $request, $user_id)
    {
        try {
            $user = User::findOrFail($user_id);

            Gate::authorize('update', $user);

            $user->update($request->mappedAttributes());

            return new UserResource($user);

        } catch (ModelNotFoundException $exception) {
            return $this->error($exception->getMessage(), 404);
        } catch (AuthorizationException $exception) {
            return $this->error($exception->getMessage(), 403);
        }
    }

    public function destroy($user_id)
    {
        try {
            $user = User::findOrFail($user_id);

            Gate::authorize('delete', $user);

            if ($user->tickets()->exists()) {
                return $this->error('User has associated tickets and cannot be deleted.', 400);
            }

            $user->delete();

            return $this->ok('User deleted');

        } catch (ModelNotFoundException $exception) {
            return $this->error($exception->getMessage(), 404);
        } catch (AuthorizationException $exception) {
            return $this->error($exception->getMessage(), 403);
        }
    }
}
