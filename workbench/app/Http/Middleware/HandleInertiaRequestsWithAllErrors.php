<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequestsWithAllErrors extends Middleware
{
    protected $withAllErrors = true;

    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => fn () => [
                'user' => $request->user(),
            ],
        ];
    }
}
