<?php

namespace App\Command;

use App\QuestionParser\QuestionParser;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:test')]
final class TestCommand extends Command
{
    public function __construct(
        private QuestionParser $parser,
        ?string                $name = null
    )
    {
        parent::__construct($name);

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $this->parser->standardize('покажи  рестораны на сегодня в атердаме');

        return Command::SUCCESS;
    }
}
