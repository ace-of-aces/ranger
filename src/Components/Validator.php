<?php

namespace Laravel\Ranger\Components;

class Validator
{
    public function __construct(
        public readonly array $rules,
    ) {
        //
    }
}
