<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponses;
use Illuminate\{Support\Facades\Gate};
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ApiController extends Controller
{
    use ApiResponses;

    protected string $policy;

    public function __construct()
    {
        Gate::guessPolicyNamesUsing(function (string $policy) {
            return $this->policy;
        });
    }

    public function include(string $relationship): bool
    {
        $param = request()->get('include');

        if (!isset($param)) {
            return false;
        }

        $includeValues = explode(',', strtolower($param));

        return in_array(strtolower($relationship), $includeValues);
    }


    protected function handleWithTryCatch(callable $callback)
    {
        try {
            return $callback();
        } catch (ModelNotFoundException $exception) {
            return $this->error($exception->getMessage(), 404);
        } catch (AuthorizationException $exception) {
            return $this->error($exception->getMessage(), 403);
        }
    }
}
