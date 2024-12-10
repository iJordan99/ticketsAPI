<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\AuthorFilter;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use App\Policies\V1\UserPolicy;
use Illuminate\{Support\Facades\Gate};

class AuthorsController extends ApiController
{
    protected string $policy = UserPolicy::class;

    /**
     * Get authors.
     *
     * Retrieves all users that created a ticket.
     *
     * @group Showing Authors
     */
    public function index(AuthorFilter $filters)
    {
        Gate::authorize('view', User::class);
        return UserResource::collection(
            User::has('tickets')->filter($filters)->paginate()
        );
    }

    /**
     * Get an author.
     *
     * Retrieves all users that created a ticket.
     *
     * @group Showing Authors
     * */
    public function show(User $author)
    {
        Gate::authorize('view', User::class);
        return new UserResource($author);
    }

}
