<?php

namespace Laravel\Ranger\Collectors;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Collection;
use Laravel\Ranger\Components\BroadcastEvent;
use Laravel\Ranger\Types\Type;
use ReflectionClass;
use ReflectionProperty;
use Spatie\StructureDiscoverer\Discover;

class BroadcastEvents extends Collector
{
    public function collect(): Collection
    {
        return collect(
            Discover::in(app_path())
                ->classes()
                ->implementing(ShouldBroadcast::class)
                ->get(),
        )
            ->filter()
            ->map($this->toBroadcastEvent(...));
    }

    protected function toBroadcastEvent(string $class): BroadcastEvent
    {
        // TODO: More robust + broadcastWith support
        $reflected = new ReflectionClass($class);

        $publicProperties = collect($reflected->getProperties(ReflectionProperty::IS_PUBLIC))
            ->mapWithKeys(fn ($property) => [
                $property->getName() => Type::from($property->hasType() ? $property->getType()->getName() : 'mixed'),
            ]);

        return new BroadcastEvent($class, $publicProperties->toArray());
    }
}
