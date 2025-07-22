<?php

namespace Laravel\Ranger\Resolvers\Expr;

use Laravel\Ranger\Known\Known;
use Laravel\Ranger\Resolvers\AbstractResolver;
use Laravel\Ranger\Types\ArrayShapeType;
use Laravel\Ranger\Types\ClassType;
use Laravel\Ranger\Types\Contracts\Type as ResultContract;
use Laravel\Ranger\Types\Type as RangerType;
use PhpParser\Node;

class StaticCall extends AbstractResolver
{
    public function resolve(Node\Expr\StaticCall $node): ResultContract
    {
        if (($known = Known::resolve($node->class->toString(), $node->name->name, ...$node->getArgs())) !== false) {
            return RangerType::from($known);
        }

        $stanType = $this->getStanType($node);

        if ($stanType instanceof ClassType) {
            $return = match ($stanType->value) {
                'Inertia\\LazyProp' => RangerType::from($this->from($node->getArgs()[0]))->optional(),
                'Inertia\\AlwaysProp' => RangerType::from($this->from($node->getArgs()[0])),
                default => null,
            };

            if ($return) {
                return $return;
            }
        }

        if ($stanType !== null) {
            return $stanType;
        }

        $varType = $this->from($node->class);

        if ($varType instanceof ClassType || (is_string($varType) && class_exists($varType))) {
            $classVarType = $varType instanceof ClassType ? $varType->resolved() : $varType;
            $varType = $varType instanceof ClassType ? $varType->value : $varType;

            if ($classVarType !== $varType) {
                $reflection = $this->reflector->reflectClass($classVarType);

                $parsed = $this->parser->parse($reflection);

                $methodNode = $this->parser->nodeFinder()->findFirst(
                    $parsed,
                    static fn (Node $n) => $n instanceof Node\Stmt\ClassMethod && $n->name->name === $node->name->name,
                );

                return $this->from($methodNode);
            }

            $returnType = $this->reflector->methodReturnType($classVarType, $node->name->name, $node);

            // TODO: Ew
            // Try to get something more specific if we can
            if ($returnType instanceof ArrayShapeType) {
                $reflection = $this->reflector->reflectMethod($classVarType, $node->name->name);
                $foundNode = null;

                $returns = collect($this->parser->nodeFinder()->find(
                    $this->parser->parse($reflection),
                    function ($n) use ($reflection, &$foundNode) {
                        if (
                            $n->getStartLine() < $reflection->getStartLine() ||
                            $n->getEndLine() > $reflection->getEndLine()
                        ) {
                            return false;
                        }

                        $foundNode ??= $n;

                        if (! ($n instanceof Node\Stmt\Return_)) {
                            return false;
                        }

                        $parent = $n->getAttribute('parent');

                        while ($parent && $parent !== $foundNode) {
                            if ($parent instanceof Node\Expr\Closure) {
                                return false;
                            }

                            $parent = $parent->getAttribute('parent');
                        }

                        return $parent === $foundNode;
                    }
                ));

                $result = collect($returns)->map(fn ($n) => $this->from($n->expr))->filter();

                if ($result->isNotEmpty()) {
                    return RangerType::union(...$result->all());
                }
            }

            if ($returnType) {
                return $returnType;
            }
        }

        return RangerType::mixed();
    }
}
