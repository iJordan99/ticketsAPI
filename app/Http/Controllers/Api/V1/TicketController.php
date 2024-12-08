<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\TicketFilter;
use App\Http\Requests\Api\V1\ReplaceTicketRequest;
use App\Http\Requests\Api\V1\StoreTicketRequest;
use App\Http\Requests\Api\V1\UpdateTicketRequest;
use App\Http\Resources\V1\TicketResource;
use App\Models\Ticket;
use App\Policies\V1\TicketPolicy;
use Illuminate\Support\Facades\Gate;

class TicketController extends ApiController
{
    protected string $policy = TicketPolicy::class;

    /**
     * Display a listing of the resource.
     */
    public function index(TicketFilter $filters)
    {
        if (Gate::allows('view')) {
            return TicketResource::collection(Ticket::filter($filters)->paginate());
        }

        $userId = auth()->id();
        return TicketResource::collection(Ticket::filter($filters)->where('user_id', $userId)->paginate());
    }

    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket)
    {
        Gate::authorize('show', $ticket);

        if ($this->include('author')) {
            return new TicketResource($ticket->load('author'));
        }

        return new TicketResource($ticket);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTicketRequest $request)
    {
        Gate::authorize('store', Ticket::class);
        return new TicketResource(Ticket::create($request->mappedAttributes()));
    }

    public function replace(ReplaceTicketRequest $request, Ticket $ticket)
    {
        Gate::authorize('replace', $ticket);

        $ticket->update($request->mappedAttributes());

        return new TicketResource($ticket);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTicketRequest $request, Ticket $ticket)
    {
        Gate::authorize('update', $ticket);

        $ticket->update($request->mappedAttributes());

        return new TicketResource($ticket);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket)
    {
        Gate::authorize('delete', $ticket);

        $ticket->delete();

        return $this->ok('Ticket deleted');
    }
}
