<?php

declare(strict_types=1);

namespace App\Command;

use Nelmio\ApiDocBundle\ApiDocGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ColllectOpenapiDumpCommand extends Command
{
    protected static $defaultName = 'colllect:openapi:dump';
    protected static $defaultDescription = 'Dump OpenAPI definition as JSON';

    public function __construct(
        private ApiDocGenerator $generator,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription(self::$defaultDescription)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln(json_encode($this->generator->generate()->toArray()));

        return 0;
    }
}
