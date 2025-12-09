<?php

namespace Laravel\Ranger\Support;

use Closure;
use Laravel\Surveyor\Analyzed\MethodResult;
use Laravel\Surveyor\Debug\Debug;

trait AnalyzesRoutes
{
    protected function analyzeRoute(array $action): ?MethodResult
    {
        if ($action['uses'] instanceof Closure) {
            // TODO: Deal with closures
            return null;
        }

        [$controller, $method] = explode('@', $action['uses']);
        $analyzed = $this->analyzer->analyzeClass($controller)->result();

        if (! $analyzed->hasMethod($method)) {
            Debug::log("Method `{$method}` not found in class `{$controller}`");

            return null;
        }

        $result = $analyzed->getMethod($method);

        if (! $result instanceof MethodResult) {
            info('Non-method reflection in route uses!');

            return null;
        }

        return $result;
    }
}
