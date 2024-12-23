<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\TicketFilter;
use App\Http\Resources\V1\TicketResource;
use App\Models\Engineer;
use App\Policies\V1\EngineerTicketsPolicy;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class EngineerTicketsController extends ApiController
{

    protected string $policy = EngineerTicketsPolicy::class;

    /**
     * Get authenticated engineer's assigned tickets
     *
     *
     * @group Engineer
     * @queryParam sort string Data field(s) to sort by. Separate multiple fields with commas. Denote descending sort with a minus sign. Example: sort=title,-createdAt
     * @queryParam filter[status] Filter by status code: A, C, H, X. No-example
     * @queryParam filter[title] Filter by title. Wildcards are supported. No-Example
     * @queryParam include Return resource with included relationship: Author, Engineer. Example: Author
     */
    public function index(TicketFilter $filters)
    {
        $user = Auth::user();
        Gate::authorize('view', $user);

        $includes = $this->getIncluded();
        return TicketResource::collection($user->assignedTickets()
            ->filter($filters)
            ->with($includes)->paginate());
    }

    /**
     * Get an engineer's assigned tickets
     *
     * @group Engineer
     * @urlParam user_id integer required The id of the engineer. Example: 11
     * @queryParam sort string Data field(s) to sort by. Separate multiple fields with commas. Denote descending sort with a minus sign. Example: sort=title,-createdAt
     * @queryParam filter[status] Filter by status code: A, C, H, X. No-example
     * @queryParam filter[title] Filter by title. Wildcards are supported. No-example
     * @queryParam include Return resource with included relationship: Author, Engineer. Example: Author
     */
    public function show(Engineer $engineer, TicketFilter $filters)
    {
        Gate::authorize('show', Auth::user());

        $includes = $this->getIncluded();

        return TicketResource::collection($engineer->user->assignedTickets()
            ->filter($filters)
            ->with($includes)->paginate());

    }
}
