<?php

namespace Laravel\Ranger\Components;

use Laravel\Surveyor\Types\Contracts\Type;

class Model
{
    /**
     * @var array<string, Type>
     */
    protected array $attributes = [];

    /**
     * @var array<string, Type>
     */
    protected array $relations = [];

    public function __construct(public readonly string $name)
    {
        //
    }

    /**
     * @return array<string, Type>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @return array<string, Type>
     */
    public function getRelations(): array
    {
        return $this->relations;
    }

    public function addAttribute(string $name, Type $type): void
    {
        $this->attributes[$name] = $type;
    }

    public function addRelation(string $name, Type $type): void
    {
        $this->relations[$name] = $type;
    }
}
