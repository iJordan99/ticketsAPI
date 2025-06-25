<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\TicketFilter;
use App\Http\Requests\Api\V1\AssignEngineerRequest;
use App\Http\Requests\Api\V1\CommentRequest;
use App\Http\Requests\Api\V1\ReplaceTicketRequest;
use App\Http\Requests\Api\V1\StoreTicketRequest;
use App\Http\Requests\Api\V1\UpdateTicketRequest;
use App\Http\Resources\V1\CommentResource;
use App\Http\Resources\V1\TicketResource;
use App\Models\Ticket;
use App\Permissions\V1\Abilities;
use App\Policies\V1\TicketPolicy;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class TicketController extends ApiController
{
    protected string $policy = TicketPolicy::class;

    /**
     * Get all tickets
     *
     * @group Tickets
     * @queryParam sort string Data field(s) to sort by. Separate multiple fields with commas. Denote descending sort with a minus sign:
     * priority,status,CreatedAt,UpdatedAt. Example: sort=title,-createdAt
     * @queryParam filter[status] Filter by status code: A, C, H, X. No-example
     * @queryParam filter[title] Filter by title. Wildcards are supported. Example: *fix*
     * @queryParam assigned Filter by assigned/unassigned : True,False.,Yes,No,1, 0 Example: True
     * @queryParam include Return resource with included relationship: author, engineer. Example: author
     */
    public function index(TicketFilter $filters)
    {
        $user = Auth::user();

        if (request()->has('assigned')) {
            Gate::authorize('view-assigned', Ticket::class);
        }

        $query = Ticket::filter($filters);

        if ($user->tokenCan(Abilities::ViewAuthorTicket)) {
            return TicketResource::collection($query->paginate());
        }

        $query = $query->where('user_id', $user->id);

        return TicketResource::collection($query->paginate());
    }

    /**
     * Show a specific ticket.
     *
     * Display an individual ticket.
     *
     * @group Tickets
     * @queryParam include Return resource with included relationship: author, engineer. Example: author
     */
    public function show(Ticket $ticket)
    {
        Gate::authorize('show', $ticket);

        $includes = $this->getIncluded();
        $ticket->load($includes);

        return new TicketResource($ticket);
    }

    /**
     * Create a ticket
     *
     * Creates a new ticket record. Users can only create tickets for themselves
     *
     * @group Tickets
     *
     * @response {"data":{"type":"ticket","id":107,"attributes":{"title":"asdfasdfasdfasdfasdfsadf","description":"test ticket","status":"A","createdAt":"2024-03-26T04:40:48.000000Z","updatedAt":"2024-03-26T04:40:48.000000Z"},"relationships":{"author":{"data":{"type":"user","id":1},"links":{"self":"http:\/\/localhost:8000\/api\/v1\/authors\/1"}}},"links":{"self":"http:\/\/localhost:8000\/api\/v1\/tickets\/107"}}}
     */
    public function store(StoreTicketRequest $request)
    {
        Gate::authorize('store', Ticket::class);
        return new TicketResource(Ticket::create($request->mappedAttributes()));
    }

    /**
     * Replace a Ticket
     *
     * Replace the specified ticket in storage.
     *
     * @group Tickets
     *
     */
    public function replace(ReplaceTicketRequest $request, Ticket $ticket)
    {
        Gate::authorize('replace', $ticket);

        $ticket->update($request->mappedAttributes());

        return new TicketResource($ticket);
    }

    /**
     * Update a Ticket
     *
     * Update the specified ticket in storage.
     *
     * @group Tickets
     *
     */
    public function update(UpdateTicketRequest $request, Ticket $ticket)
    {
        Gate::authorize('update', $ticket);

        $ticket->update($request->mappedAttributes());

        return new TicketResource($ticket);
    }

    /**
     * Delete a ticket.
     *
     * Remove the specified resource from storage.
     *
     * @group Tickets
     *
     */
    public function destroy(Ticket $ticket)
    {
        Gate::authorize('delete', $ticket);

        $ticket->delete();

        return $this->ok('Ticket deleted');
    }

    /**
     * Assign an engineer
     *
     * Assigns an engineer to the provided ticket
     * @group Tickets
     */
    public function assign(AssignEngineerRequest $request, Ticket $ticket)
    {
        $engineer = $request['data.attributes.engineer'];
        Gate::authorize('assign', $ticket);

        $ticket->engineer()->syncWithoutDetaching($engineer);

        return new TicketResource($ticket->load('engineer'));
    }

    public function comment(CommentRequest $request, Ticket $ticket)
    {
        Gate::authorize('comment', Auth::user());

        $comment = [
            'ticket_id' => $ticket->id,
            'user_id' => Auth::user()->id,
            'comment' => $request['data.attributes.comment']
        ];

        return new CommentResource($ticket->comment()->create($comment));
    }
}
