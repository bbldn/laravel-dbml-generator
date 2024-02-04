<?php

namespace BBLDN\LaravelDbmlGenerator\Infrastructure\Command;

use BBLDN\LaravelDbmlGenerator\Container;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command as Base;

final class Command extends Base
{
    protected function configure(): void
    {
        $this->setName('laravel-scheme-tool');
        $this->addOption('autoload-php', mode: InputOption::VALUE_REQUIRED);
        $this->addOption('target-namespace', mode: InputOption::VALUE_REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $autoloadPhpPath = $input->getOption('autoload-php');
        $targetNamespace = $input->getOption('target-namespace');

        if (null !== $autoloadPhpPath && null !== $targetNamespace) {
            $container = new Container();
            $generationService = $container->getGenerationService();

            $output->writeln(
                messages: $generationService->handle(
                    autoloadPhpPath: $autoloadPhpPath,
                    targetNamespace: $targetNamespace,
                ),
            );
        }

        return Base::SUCCESS;
    }
}
