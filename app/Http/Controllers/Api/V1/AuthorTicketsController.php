<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\TicketFilter;
use App\Http\Requests\{Api\V1\ReplaceTicketRequest, Api\V1\StoreTicketRequest, Api\V1\UpdateTicketRequest};
use App\Http\Resources\{V1\TicketResource};
use App\Models\{Ticket};
use App\Policies\V1\TicketPolicy;
use Illuminate\{Auth\Access\AuthorizationException, Database\Eloquent\ModelNotFoundException, Support\Facades\Gate};

class AuthorTicketsController extends ApiController
{
    protected string $policy = TicketPolicy::class;

    public function index($author_id, TicketFilter $filters)
    {
        return TicketResource::collection(
            Ticket::where('user_id', $author_id)
                ->filter($filters)
                ->paginate());
    }

    public function store(StoreTicketRequest $request, $author_id)
    {
        try {

            Gate::authorize('store', Ticket::class);

            return new TicketResource(Ticket::create($request->mappedAttributes([
                'author' => 'user_id'
            ])));

        } catch (AuthorizationException $exception) {
            return $this->error($exception->getMessage(), 403);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($author_id, $ticket_id)
    {
        try {
            $ticket = Ticket::where('id', $ticket_id)
                ->where('user_id', $author_id)->firstOrFail();

            Gate::authorize('delete', $ticket);

            $ticket->delete();

            return $this->ok('Ticket deleted');
            
        } catch (ModelNotFoundException $exception) {
            return $this->error('Ticket not found.', 404);
        } catch (AuthorizationException $exception) {
            return $this->error($exception->getMessage(), 403);
        }
    }

    public function replace(ReplaceTicketRequest $request, $author_id, $ticket_id)
    {
        try {
            $ticket = Ticket::where('id', $ticket_id)
                ->where('user_id', $author_id)->firstOrFail();

            Gate::authorize('replace', $ticket);

            $ticket->update($request->mappedAttributes());
            return new TicketResource($ticket);

        } catch (ModelNotFoundException $exception) {
            return $this->error('Ticket not found.', 404);
        } catch (AuthorizationException $exception) {
            return $this->error($exception->getMessage(), 403);
        }
    }

    public function update(UpdateTicketRequest $request, $author_id, $ticket_id)
    {
        try {
            $ticket = Ticket::where('id', $ticket_id)
                ->where('user_id', $author_id)->firstOrFail();

            Gate::authorize('update', $ticket);

            $ticket->update($request->mappedAttributes());
            return new TicketResource($ticket);

        } catch (ModelNotFoundException $exception) {
            return $this->error('Ticket not found.', 404);
        } catch (AuthorizationException $exception) {
            return $this->error($exception->getMessage(), 403);
        }
    }
}
