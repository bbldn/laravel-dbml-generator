<?php

namespace BBLDN\LaravelDbmlGenerator;

use Roave\BetterReflection\BetterReflection;
use BBLDN\LaravelDbmlGenerator\Application\GenerationService\Service as GenerationService;
use BBLDN\LaravelDbmlGenerator\Application\GenerationService\SchemeRender\Render as SchemeRender;
use BBLDN\LaravelDbmlGenerator\Application\GenerationService\ClassLocator\Locator as ClassLocator;
use BBLDN\LaravelDbmlGenerator\Application\GenerationService\ItemGenerator\EnumItemGenerator\Generator as EnumItemGenerator;
use BBLDN\LaravelDbmlGenerator\Application\GenerationService\ItemGenerator\ClassItemGenerator\Generator as ClassItemGenerator;

final class Container
{
    private ClassLocator|null $classLocator = null;

    private SchemeRender|null $schemeRender = null;

    private BetterReflection|null $betterReflection = null;

    private EnumItemGenerator|null $enumItemGenerator = null;

    private GenerationService|null $generationService = null;

    private ClassItemGenerator|null $classItemGenerator = null;

    public function getClassLocator(): ClassLocator
    {
        if (null === $this->classLocator) {
            $this->classLocator = new ClassLocator(
                betterReflection: $this->getBetterReflection(),
            );
        }

        return $this->classLocator;
    }

    public function getSchemeRender(): SchemeRender
    {
        if (null === $this->schemeRender) {
            $this->schemeRender = new SchemeRender();
        }

        return $this->schemeRender;
    }

    public function getBetterReflection(): BetterReflection
    {
        if (null === $this->betterReflection) {
            $this->betterReflection = new BetterReflection();
        }

        return $this->betterReflection;
    }

    public function getEnumItemGenerator(): EnumItemGenerator
    {
        if (null === $this->enumItemGenerator) {
            $this->enumItemGenerator = new EnumItemGenerator();
        }

        return $this->enumItemGenerator;
    }

    public function getGenerationService(): GenerationService
    {
        if (null === $this->generationService) {
            $this->generationService = new GenerationService(
                classLocator: $this->getClassLocator(),
                schemeRender: $this->getSchemeRender(),
                enumItemGenerator: $this->getEnumItemGenerator(),
                classItemGenerator: $this->getClassItemGenerator(),
            );
        }

        return $this->generationService;
    }

    public function getClassItemGenerator(): ClassItemGenerator
    {
        if (null === $this->classItemGenerator) {
            $this->classItemGenerator = new ClassItemGenerator(
                betterReflection: $this->getBetterReflection(),
            );
        }

        return $this->classItemGenerator;
    }
}
