<?php

namespace Laravel\Ranger\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Spatie\StructureDiscoverer\Discover;

class ScaffoldPhpStanTypesCommand extends Command
{
    protected $signature = 'scaffold:stan-types';

    protected $description = 'AI was going too slowly';

    public function handle()
    {
        collect(Discover::in(__DIR__.'/../../../phpstan-src/src/Type')->classes()->get())->filter(fn (string $c) => str_contains($c, '\\Type\\'))->each(function ($class) {
            $resolverClassFqn = str($class)->after('Type\\');
            $resolverClassNamespace = str($class)->after('Type\\')->beforeLast('\\');

            $resolverClass = $resolverClassFqn->afterLast('\\');

            if ($resolverClass->toString() === $resolverClass->toString()) {
                $resolverClassNamespace = 'Laravel\\Ranger\\StanTypeResolvers';
            } else {
                $resolverClassNamespace = 'Laravel\\Ranger\\StanTypeResolvers\\'.$resolverClassNamespace;
            }

            $path = __DIR__.'/../StanTypeResolvers/'.$resolverClassFqn->replace('\\', '/')->append('.php')->toString();

            if (file_exists($path)) {
                $this->warn("File already exists: {$path}");

                return;
            }

            $contents = <<<PHP
<?php

namespace $resolverClassNamespace;

use Laravel\Ranger\StanTypeResolvers\AbstractResolver;
use PHPStan\Type;

class {$resolverClass} extends AbstractResolver
{
    public function resolve(Type\\{$resolverClassFqn} \$node): string
    {
        dd(\$node, '{$resolverClass} not implemented yet');
    }
}
PHP;

            File::ensureDirectoryExists(dirname($path));
            File::put($path, $contents);

            $this->info("Created file: {$path}");
        });
    }
}
