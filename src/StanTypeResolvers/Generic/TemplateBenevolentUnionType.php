<?php

namespace Laravel\Ranger\StanTypeResolvers\Generic;

use Laravel\Ranger\StanTypeResolvers\AbstractResolver;
use Laravel\Ranger\Types\Contracts\Type as ResultContract;
use Laravel\Ranger\Types\Type as RangerType;
use PHPStan\Type;

class TemplateBenevolentUnionType extends AbstractResolver
{
    public function resolve(Type\Generic\TemplateBenevolentUnionType $node): ResultContract
    {
        return RangerType::union(...array_map(
            fn ($type) => $this->from($type),
            $node->getTypes()
        ));
    }
}
