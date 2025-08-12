<?php

namespace Laravel\Ranger\Resolvers\Expr;

use Illuminate\Config\Repository;
use Laravel\Ranger\Resolvers\AbstractResolver;
use Laravel\Ranger\Types\ArrayType;
use Laravel\Ranger\Types\Contracts\Type;
use Laravel\Ranger\Types\Contracts\Type as ResultContract;
use Laravel\Ranger\Types\StringType;
use Laravel\Ranger\Types\Type as RangerType;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Variable;

class FuncCall extends AbstractResolver
{
    public function resolve(Node\Expr\FuncCall $node): ResultContract
    {
        if ($node->name instanceof Variable) {
            return $this->from($node->name);
        }

        if ($node->name->name === 'config') {
            $args = array_map(fn (Arg $arg) => $this->from($arg), $node->getArgs());

            if (count($args) === 0) {
                return RangerType::string(Repository::class);
            }

            if (! $args[0] instanceof StringType) {
                dd('Config not a string', $args);
            }

            $val = config($args[0]->value);

            if ($val !== null) {
                if (is_array($val)) {
                    dd('Config value is array', $val, $args);

                    return RangerType::array($val);
                }

                if (is_string($val)) {
                    return RangerType::string($val);
                }

                if (is_numeric($val)) {
                    return RangerType::int($val);
                }

                if (is_bool($val)) {
                    return RangerType::bool($val);
                }

                dd('Unhandled config value type', $val, $args);
            }

            return $args[1] ?? RangerType::mixed();
        }

        if ($node->name->toString() === 'array_merge') {
            $arrays = collect($node->args)->map($this->from(...));
            $finalArray = collect();

            foreach ($arrays as $array) {
                if ($array instanceof ArrayType) {
                    $finalArray = $finalArray->merge($array->value);
                } else {
                    // dump('Unsupported array_merge argument type', $array);
                }

                // $finalArray['[key: string]'] = 'mixed';
            }

            if ($finalArray->keys()->every(fn ($key) => is_int($key))) {
                dd('is list', $finalArray);

                return RangerType::array([]);
            }

            return RangerType::array($finalArray);
        }

        $stanType = $this->getStanType($node);

        if ($stanType !== null) {
            return $stanType;
        }

        $result = $this->reflector->functionReturnType($node->name->toString(), $node);

        if ($result instanceof Type) {
            return $result;
        }

        if ($result === null || (is_array($result) && count($result) === 0)) {
            return RangerType::mixed();
        }

        if (is_array($result) && count($result) === 1) {
            return RangerType::from($result[0]);
        }

        return RangerType::from($result);
    }
}
