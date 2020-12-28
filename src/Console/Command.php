<?php

namespace Mocchi\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputDefinition;

abstract class Command {

    const ERROR = 0;
    const SUCCESS = 1;

    protected string $name;
    protected string $version;
    protected string $description;
    protected array $args;

    /**
     * @var Input|null
     */
    private $input;

    public function register(string $name): void
    {
        $this->name = $name;
    }

    public function execute(Input $input, OutputInterface $output): int
    {
        return Command::ERROR;
    }

    protected function addArg(string $name, string $short, int $mod): void
    {
        $this->args[$name] = new CommandArg($name, $short, $mod);
    }

    public function setCliInput(Input $input): void
    {
        $this->input = $input;
    }

    protected function getArg(string $name)
    {

        if(!isset($this->args[$name])) {

            throw new \Exception('CLI argument is not defined');
        }

        if(!$this->args[$name]->value) {

            $this->parseArgs();
        }

        return $this->args[$name]->value;
    }

    protected function parseArgs(): void
    {
        foreach ($this->args as $name => $arg) {
            $this->args[$name]->value = $this->input->getArg($name);
        }
    }
}