<?php

namespace Laravel\Ranger\StanTypeResolvers;

use Laravel\Ranger\Types\Contracts\Type as ResultContract;
use Laravel\Ranger\Types\Type as RangerType;
use PHPStan\Type;

class NeverType extends AbstractResolver
{
    public function resolve(Type\NeverType $node): ResultContract
    {
        return RangerType::never();
    }
}
