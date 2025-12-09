<?php

namespace Laravel\Ranger\Collectors;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Collection;
use Laravel\Ranger\Components\Model as ModelComponent;
use Laravel\Surveyor\Analyzer\Analyzer;
use Spatie\StructureDiscoverer\Discover;

class Models extends Collector
{
    protected Collection $modelComponents;

    public function __construct(protected Analyzer $analyzer)
    {
        $this->modelComponents = collect();
    }

    public function collect(): Collection
    {
        foreach ($this->findModels() as $model) {
            $this->toComponent($model);
        }

        return $this->modelComponents->values();
    }

    public function get(string $model): ?ModelComponent
    {
        return $this->getCollection()->first(fn (ModelComponent $component) => $component->name === $model);
    }

    protected function toComponent(string $model): void
    {
        $result = $this->analyzer->analyzeClass($model)->result();

        if ($result === null) {
            return;
        }

        $modelComponent = new ModelComponent($model);

        foreach ($result->publicProperties() as $property) {
            if ($property->modelAttribute || $property->fromDocBlock) {
                $modelComponent->addAttribute($property->name, $property->type);
            }
        }

        foreach ($result->publicMethods() as $method) {
            if ($method->isModelRelation()) {
                $returnType = $method->returnType();

                if (! $this->modelComponents->offsetExists($returnType->value)) {
                    $this->toComponent($returnType->value);
                }

                $modelComponent->addRelation($method->name(), $returnType);
            }
        }

        $this->modelComponents->offsetSet($modelComponent->name, $modelComponent);
    }

    protected function findModels()
    {
        return Discover::in(app_path())
            ->classes()
            ->extending(Model::class, User::class, Pivot::class)
            ->get();
    }
}
