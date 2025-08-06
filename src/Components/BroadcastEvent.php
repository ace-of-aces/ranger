<?php

namespace Laravel\Ranger\Components;

class BroadcastEvent
{
    public function __construct(
        public readonly string $name,
        public readonly array $data,
    ) {
        //
    }
}
