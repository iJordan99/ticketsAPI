<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\TicketFilter;
use App\Http\Requests\{Api\V1\ReplaceTicketRequest, Api\V1\StoreTicketRequest, Api\V1\UpdateTicketRequest};
use App\Http\Resources\{V1\TicketResource};
use App\Models\{Ticket, User};
use App\Policies\V1\AuthorTickets;
use Illuminate\{Support\Facades\Gate};

class AuthorTicketsController extends ApiController
{
    protected string $policy = AuthorTickets::class;

    public function index(User $author, TicketFilter $filters)
    {
        Gate::authorize('view', Ticket::class);
        return TicketResource::collection(
            Ticket::where('user_id', $author->id)->filter($filters)->paginate()
        );
    }

    public function store(StoreTicketRequest $request, User $author)
    {

        Gate::authorize('store', Ticket::class);

        $attributes = $request->mappedAttributes();

        $attributes['user_id'] = $author->id;

        return new TicketResource(Ticket::create($attributes));
    }
    
    public function replace(ReplaceTicketRequest $request, User $author, Ticket $ticket)
    {
        Gate::authorize('replace', $ticket);

        $ticket->update($request->mappedAttributes());
        return new TicketResource($ticket);
    }

    public function update(UpdateTicketRequest $request, User $author, Ticket $ticket)
    {
        Gate::authorize('update', $ticket);

        $ticket->update($request->mappedAttributes());
        return new TicketResource($ticket);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $author, Ticket $ticket)
    {
        Gate::authorize('delete', $ticket);
        $ticket->delete();

        return $this->ok('Ticket deleted');
    }

}
