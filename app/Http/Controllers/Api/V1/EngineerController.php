<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\TicketFilter;
use App\Http\Resources\V1\TicketResource;
use App\Models\User;
use App\Policies\V1\AssignedTicketPolicy;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class EngineerController extends ApiController
{
    protected string $policy = AssignedTicketPolicy::class;

    /**
     * Get authenticated engineer's assigned tickets
     *
     *
     * @group Assigned Tickets
     * @queryParam sort string Data field(s) to sort by. Separate multiple fields with commas. Denote descending sort with a minus sign. Example: sort=title,-createdAt
     * @queryParam filter[status] Filter by status code: A, C, H, X. No-example
     * @queryParam filter[title] Filter by title. Wildcards are supported. No-Example
     * @queryParam include Return resource with included relationship: Author, Engineer. Example: Author
     */
    public function index(TicketFilter $filters)
    {

        Gate::authorize('view', Auth::user());
        $user = Auth::user();

        $includes = $this->getIncluded();
        return TicketResource::collection($user->assignedTickets()
            ->filter($filters)
            ->with($includes)->paginate());
    }

    /**
     * Get an engineer's assigned tickets
     *
     * @group Assigned Tickets
     * @urlParam user_id integer required The id of the engineer. Example: 11
     * @queryParam sort string Data field(s) to sort by. Separate multiple fields with commas. Denote descending sort with a minus sign. Example: sort=title,-createdAt
     * @queryParam filter[status] Filter by status code: A, C, H, X. No-example
     * @queryParam filter[title] Filter by title. Wildcards are supported. No-example
     * @queryParam include Return resource with included relationship: Author, Engineer. Example: Author
     */
    public function show(User $user, TicketFilter $filters)
    {
        Gate::authorize('show', Auth::user());

        $includes = $this->getIncluded();

        return TicketResource::collection($user->assignedTickets()
            ->filter($filters)
            ->with($includes)->paginate());

    }
}
