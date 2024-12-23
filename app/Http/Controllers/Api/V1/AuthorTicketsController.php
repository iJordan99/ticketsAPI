<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\TicketFilter;
use App\Http\Requests\{Api\V1\ReplaceTicketRequest, Api\V1\StoreTicketRequest, Api\V1\UpdateTicketRequest};
use App\Http\Resources\{V1\TicketResource};
use App\Models\{Ticket, User};
use App\Policies\V1\AuthorTicketsPolicy;
use Illuminate\{Support\Facades\Gate};

class AuthorTicketsController extends ApiController
{
    protected string $policy = AuthorTicketsPolicy::class;

    /**
     * Get all tickets
     *
     * Retrieves all tickets created by a specific user.
     *
     * @group Tickets by Author
     *
     * @urlParam author_id integer required The author's ID. No-example
     *
     * @response 200 {"data":[{"type":"user","id":3,"attributes":{"name":"Mr. Henri Beatty MD","email":"bmertz@example.net","isManager":false,"emailVerifiedAt":"2024-03-14T04:41:51.000000Z","createdAt":"2024-03-14T04:41:51.000000Z","udpatedAt":"2024-03-14T04:41:51.000000Z"},"links":{"self":"http:\/\/localhost:8000\/api\/v1\/authors\/3"}}],"links":{"first":"http:\/\/localhost:8000\/api\/v1\/authors?page=1","last":"http:\/\/localhost:8000\/api\/v1\/authors?page=1","prev":null,"next":null},"meta":{"current_page":1,"from":1,"last_page":1,"links":[{"url":null,"label":"&laquo; Previous","active":false},{"url":"http:\/\/localhost:8000\/api\/v1\/authors?page=1","label":"1","active":true},{"url":null,"label":"Next &raquo;","active":false}],"path":"http:\/\/localhost:8000\/api\/v1\/authors","per_page":15,"to":1,"total":10}}
     *
     * @queryParam sort string Data field(s) to sort by. Separate multiple fields with commas. Denote descending sort with a minus sign. Example: sort=name
     * @queryParam filter[name] Filter by name. Wildcards are supported.
     * @queryParam filter[email] Filter by email. Wildcards are supported.
     * @queryParam assigned Filter by assigned/unassigned : True,False.,Yes,No,1, 0 Example: True
     * @queryParam include Return resource with included relationship: author, engineer. Example: author
     */
    public function index(User $author, TicketFilter $filters)
    {
        Gate::authorize('view', Ticket::class);
        return TicketResource::collection(
            Ticket::where('user_id', $author->id)->filter($filters)->paginate()
        );
    }

    /**
     * Create a ticket for an author
     *
     * Creates a ticket for the specific user.
     *
     * @group Tickets by Author
     *
     * @urlParam author_id integer required The author's ID. No-example
     *
     */
    public function store(StoreTicketRequest $request, User $author)
    {

        Gate::authorize('store', Ticket::class);

        $attributes = $request->mappedAttributes();

        $attributes['user_id'] = $author->id;

        return new TicketResource(Ticket::create($attributes));
    }

    /**
     * Replace an author's ticket
     *
     * Replaces an author's ticket.
     *
     * @group Tickets by Author
     * @urlParam author_id integer required The author's ID. No-example
     * @urlParam ticket_id integer required The ticket ID. No-example
     * @response {"data":{"type":"ticket","id":107,"attributes":{"title":"asdfasdfasdfasdfasdfsadf","description":"test ticket","status":"A","createdAt":"2024-03-26T04:40:48.000000Z","updatedAt":"2024-03-26T04:40:48.000000Z"},"relationships":{"author":{"data":{"type":"user","id":1},"links":{"self":"http:\/\/localhost:8000\/api\/v1\/authors\/1"}}},"links":{"self":"http:\/\/localhost:8000\/api\/v1\/tickets\/107"}}}
     */
    public function replace(ReplaceTicketRequest $request, User $author, Ticket $ticket)
    {
        Gate::authorize('replace', $ticket);

        $ticket->update($request->mappedAttributes());
        return new TicketResource($ticket);
    }

    /**
     * Update an author's ticket
     *
     * Updates an author's ticket.
     *
     * @group Tickets by Author
     * @urlParam author_id integer required The author's ID. No-example
     * @urlParam ticket_id integer required The ticket ID. No-example
     */
    public function update(UpdateTicketRequest $request, User $author, Ticket $ticket)
    {
        Gate::authorize('update', $ticket);

        $ticket->update($request->mappedAttributes());
        return new TicketResource($ticket);
    }

    /**
     * Deletes an author's ticket.
     *
     * @group Tickets by Author
     * @urlParam author_id integer required The author's ID. No-example
     * @urlParam id integer required The ticket ID. No-example
     * @response {}
     */
    public function destroy(User $author, Ticket $ticket)
    {
        Gate::authorize('delete', $ticket);
        $ticket->delete();

        return $this->ok('Ticket deleted');
    }

}
