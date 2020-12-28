<?php

namespace Mocchi\Console;

use mysql_xdevapi\Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

class Cli {

    protected Input $input;
    protected OutputInterface $output;
    protected string $commandClass;
    protected Command $command;

    public function __construct(Input $input)
    {
        $this->input = $input;
        $this->output = new ConsoleOutput();

        $this->commandClass = (string) $this->input->getArg('command');

        $this->boot();
    }

    protected function boot()
    {
        $className = 'App\Command\\' . ucfirst($this->commandClass) . 'Command';

        if(class_exists($className)) {

            $this->command = new $className();
        }else {

            throw new \Exception('Command not found: ' . $className);
        }

        $this->command->setCliInput($this->input);

        $this->command->register($this->commandClass);
        $this->command->execute($this->input, $this->output);
    }
}