<?php

namespace Laravel\Ranger\Components;

class Validator
{
    /**
     * @var array<string, list<Rule>>
     */
    public function __construct(
        public readonly array $rules,
    ) {
        //
    }
}
