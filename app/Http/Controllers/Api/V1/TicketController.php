<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\TicketFilter;
use App\Http\Requests\Api\V1\ReplaceTicketRequest;
use App\Http\Requests\Api\V1\StoreTicketRequest;
use App\Http\Requests\Api\V1\UpdateTicketRequest;
use App\Http\Resources\V1\TicketResource;
use App\Models\Ticket;
use App\Policies\V1\TicketPolicy;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Gate;

class TicketController extends ApiController
{
    protected string $policy = TicketPolicy::class;

    /**
     * Display a listing of the resource.
     */
    public function index(TicketFilter $filters)
    {
        return TicketResource::collection(Ticket::filter($filters)->paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTicketRequest $request)
    {
        try {
            Gate::authorize('store', Ticket::class);

            return new TicketResource(Ticket::create($request->mappedAttributes()));
        } catch (AuthorizationException $exception) {
            return $this->error($exception->getMessage(), 403);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($ticket_id)
    {
        try {

            $ticket = Ticket::findOrFail($ticket_id);

            if ($this->include('author')) {
                return new TicketResource($ticket->load('author'));
            }

            return new TicketResource($ticket);

        } catch (ModelNotFoundException $exception) {
            return $this->error($exception->getMessage(), 404);
        }
    }

    public function replace(ReplaceTicketRequest $request, $ticket_id)
    {
        try {
            $ticket = Ticket::findOrFail($ticket_id);

            Gate::authorize('replace', $ticket);

            $ticket->update($request->mappedAttributes());

            return new TicketResource($ticket);

        } catch (ModelNotFoundException $exception) {
            return $this->error($exception->getMessage(), 404);
        } catch (AuthorizationException $exception) {
            return $this->error($exception->getMessage(), 403);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTicketRequest $request, $ticket_id)
    {
        try {
            $ticket = Ticket::findOrFail($ticket_id);

            Gate::authorize('update', $ticket);

            $ticket->update($request->mappedAttributes());

            return new TicketResource($ticket);

        } catch (ModelNotFoundException $exception) {
            return $this->error($exception->getMessage(), 404);
        } catch (AuthorizationException $exception) {
            return $this->error($exception->getMessage(), 403);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($ticket_id)
    {
        try {
            $ticket = Ticket::findOrFail($ticket_id);

            Gate::authorize('delete', $ticket);

            $ticket->delete();

            return $this->ok('Ticket deleted');

        } catch (ModelNotFoundException $exception) {
            return $this->error('Ticket not found', 404);
        } catch (AuthorizationException $exception) {
            return $this->error($exception->getMessage(), 403);
        }
    }
}
