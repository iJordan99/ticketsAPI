<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\TicketFilter;
use App\Http\Requests\Api\V1\ReplaceTicketRequest;
use App\Http\Requests\Api\V1\StoreTicketRequest;
use App\Http\Requests\Api\V1\UpdateTicketRequest;
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
     * @group Managing Tickets
     * @queryParam sort string Data field(s) to sort by. Separate multiple fields with commas. Denote descending sort with a minus sign. Example: sort=title,-createdAt
     * @queryParam filter[status] Filter by status code: A, C, H, X. No-example
     * @queryParam filter[title] Filter by title. Wildcards are supported. Example: *fix*
     */
    public function index(TicketFilter $filters)
    {
        $user = Auth::user();
        if ($user->tokenCan(Abilities::ViewAuthorTicket)) {
            return TicketResource::collection(Ticket::filter($filters)->paginate());
        }
        return TicketResource::collection(Ticket::filter($filters)->where('user_id', $user->id)->paginate());
    }

    /**
     * Show a specific ticket.
     *
     * Display an individual ticket.
     *
     * @group Managing Tickets
     *
     */
    public function show(Ticket $ticket)
    {
        Gate::authorize('show', $ticket);
        
        $includeParam = request()->get('include');
        if ($includeParam) {
            $relationships = explode(',', strtolower($includeParam));
            $ticket->load($relationships);
        }

        return new TicketResource($ticket);
    }

    /**
     * Create a ticket
     *
     * Creates a new ticket record. Users can only create tickets for themselves
     *
     * @group Managing Tickets
     *
     * @response {"data":{"type":"ticket","id":107,"attributes":{"title":"asdfasdfasdfasdfasdfsadf","description":"test ticket","status":"A","createdAt":"2024-03-26T04:40:48.000000Z","updatedAt":"2024-03-26T04:40:48.000000Z"},"relationships":{"author":{"data":{"type":"user","id":1},"links":{"self":"http:\/\/localhost:8000\/api\/v1\/authors\/1"}}},"links":{"self":"http:\/\/localhost:8000\/api\/v1\/tickets\/107"}}}
     */
    public function store(StoreTicketRequest $request)
    {
        Gate::authorize('store', Ticket::class);
        return new TicketResource(Ticket::create($request->mappedAttributes()));
    }

    /**
     * Replace Ticket
     *
     * Replace the specified ticket in storage.
     *
     * @group Managing Tickets
     *
     */
    public function replace(ReplaceTicketRequest $request, Ticket $ticket)
    {
        Gate::authorize('replace', $ticket);

        $ticket->update($request->mappedAttributes());

        return new TicketResource($ticket);
    }

    /**
     * Update Ticket
     *
     * Update the specified ticket in storage.
     *
     * @group Managing Tickets
     *
     */
    public function update(UpdateTicketRequest $request, Ticket $ticket)
    {
        Gate::authorize('update', $ticket);

        $ticket->update($request->mappedAttributes());

        return new TicketResource($ticket);
    }

    /**
     * Delete ticket.
     *
     * Remove the specified resource from storage.
     *
     * @group Managing Tickets
     *
     */
    public function destroy(Ticket $ticket)
    {
        Gate::authorize('delete', $ticket);

        $ticket->delete();

        return $this->ok('Ticket deleted');
    }
}
