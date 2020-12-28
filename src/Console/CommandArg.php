<?php

namespace Mocchi\Console;

use Symfony\Component\Console\Input\InputOption;

class CommandArg {

    const REQUIRED = InputOption::VALUE_REQUIRED;
    const OPTIONAL = InputOption::VALUE_OPTIONAL;

    public string $name;
    public string $short;
    public int $mod;
    public $value;

    public function __construct(string $name, string $short, int $mod)
    {
        $this->name = $name;
        $this->short = $short;
        $this->mod = $mod;
    }
}