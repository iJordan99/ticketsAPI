<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\UserFilter;
use App\Http\Requests\Api\V1\StoreEngineerRequest;
use App\Http\Resources\V1\EngineerResource;
use App\Models\Engineer;
use App\Models\User;
use App\Policies\V1\EngineerPolicy;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class EngineerController extends ApiController
{
    public string $policy = EngineerPolicy::class;

    public function index(UserFilter $filter)
    {
        Gate::authorize('view', Auth::user());
        return EngineerResource::collection(
            User::has('engineer')
                ->filter($filter)
                ->paginate()
        );
    }

    public function show(Engineer $engineer)
    {
        Gate::authorize('show', Auth::user());
        return new EngineerResource($engineer->user);
    }
    
    public function store(StoreEngineerRequest $request)
    {
        Gate::authorize('store', Auth::user());

        // Use mappedAttributes in case we expand the passed details
        $user = User::findOrFail($request->mappedAttributes()['user_id']);
        $user->engineer()->create();
        return new EngineerResource($user);
    }

}
