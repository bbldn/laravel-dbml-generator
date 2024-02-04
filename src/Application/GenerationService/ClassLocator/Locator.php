<?php

namespace BBLDN\LaravelDbmlGenerator\Application\GenerationService\ClassLocator;

use Generator;
use RuntimeException;
use Composer\Autoload\ClassLoader;
use Roave\BetterReflection\BetterReflection;
use Roave\BetterReflection\Reflection\ReflectionEnum;
use Roave\BetterReflection\Reflector\DefaultReflector;
use Roave\BetterReflection\Reflection\ReflectionObject;
use BBLDN\LaravelDbmlGenerator\Domain\Enum\LaravelTypeEnum;
use Roave\BetterReflection\SourceLocator\Type\ComposerSourceLocator;
use Roave\BetterReflection\SourceLocator\Type\AggregateSourceLocator;
use Roave\BetterReflection\SourceLocator\Type\PhpInternalSourceLocator;
use Roave\BetterReflection\SourceLocator\SourceStubber\ReflectionSourceStubber;

final readonly class Locator
{
    public function __construct(
        private BetterReflection $betterReflection
    ) {
    }

    private function createClassLoader(string $autoloadPhpPath): ClassLoader
    {
        if (true === file_exists($autoloadPhpPath)) {
            /** @var ClassLoader */
            return require $autoloadPhpPath;
        }

        throw new RuntimeException('Autoloader not found');
    }

    private function createDefaultReflector(ClassLoader $classLoader): DefaultReflector
    {
        $astLocator = $this->betterReflection->astLocator();

        return new DefaultReflector(
            sourceLocator: new AggregateSourceLocator(
                sourceLocators: [
                    new ComposerSourceLocator($classLoader, $astLocator),
                    new PhpInternalSourceLocator($astLocator, new ReflectionSourceStubber()),
                ],
            ),
        );
    }

    /**
     * @return Generator<ReflectionEnum|ReflectionObject>
     */
    public function findClasses(string $autoloadPhpPath, string $targetNamespace): Generator
    {
        $classLoader = $this->createClassLoader($autoloadPhpPath);

        $reflector = $this->createDefaultReflector($classLoader);

        foreach ($classLoader->getClassMap() as $className => $ignored) {
            if (true === str_starts_with($className, $targetNamespace)) {
                $reflectionClass = $reflector->reflectClass($className);

                if (true === $reflectionClass->isAbstract()) {
                    continue;
                }

                if (true === $reflectionClass->isInterface()) {
                    continue;
                }

                if (false === $reflectionClass->isEnum()) {
                    if (false === $reflectionClass->isSubclassOf(LaravelTypeEnum::MODEL)) {
                        continue;
                    }
                }

                yield $reflectionClass;
            }
        }
    }
}
