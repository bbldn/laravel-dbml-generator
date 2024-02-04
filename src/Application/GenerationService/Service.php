<?php

namespace BBLDN\LaravelDbmlGenerator\Application\GenerationService;

use BBLDN\LaravelDbmlGenerator\Domain\DTO\Schema;
use Roave\BetterReflection\Reflection\ReflectionEnum;
use BBLDN\LaravelDbmlGenerator\Application\GenerationService\SchemeRender\Render as SchemeRender;
use BBLDN\LaravelDbmlGenerator\Application\GenerationService\ClassLocator\Locator as ClassLocator;
use BBLDN\LaravelDbmlGenerator\Application\GenerationService\ItemGenerator\EnumItemGenerator\Generator as EnumItemGenerator;
use BBLDN\LaravelDbmlGenerator\Application\GenerationService\ItemGenerator\ClassItemGenerator\Generator as ClassItemGenerator;

final readonly class Service
{
    public function __construct(
        private ClassLocator $classLocator,
        private SchemeRender $schemeRender,
        private EnumItemGenerator $enumItemGenerator,
        private ClassItemGenerator $classItemGenerator
    ) {
    }

    public function handle(string $autoloadPhpPath, string $targetNamespace): string
    {
        $enumItemMap = [];
        $classItemMap = [];

        $generator = $this->classLocator->findClasses($autoloadPhpPath, $targetNamespace);
        foreach ($generator as $reflectionClass) {
            if (true === is_a($reflectionClass, ReflectionEnum::class)) {
                $item = $this->enumItemGenerator->generate($reflectionClass);
                if (null !== $item) {
                    $enumItemMap[$item->name] = $item;
                }
            } else {
                $item = $this->classItemGenerator->generate($reflectionClass);
                if (null !== $item) {
                    $classItemMap[$item->name] = $item;
                }
            }
        }

        return $this->schemeRender->render(
            schema: new Schema(
                enumItemMap: $enumItemMap,
                classItemMap: $classItemMap,
            ),
        );
    }
}
