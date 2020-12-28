<?php

namespace App\Command;

use Mocchi\Console\Command;
use Mocchi\Console\CommandArg;
use Mocchi\Console\Input;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DefaultCommand  extends Command {

    public function register(string $name): void
    {
        $this->name = $name;
        $this->version = '0.0.1';
        $this->description = 'test command';

        $this->addArg('foo', 'f', CommandArg::REQUIRED);
    }

    public function execute(Input $input, OutputInterface $output): int {

        $output->writeln($this->getArg('foo'));

        $output->writeln($this->description);

        return Command::SUCCESS;
    }
}