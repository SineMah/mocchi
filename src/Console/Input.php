<?php

namespace Mocchi\Console;

class Input {

    protected array $args = [];

    public function setArgs(array $args) {

        unset($args[0]);

        foreach ($args as $arg) {
            $cmd = explode('=', $arg);

            if(count($cmd) > 1) {
                $index = str_replace(['-', '--'], '', $cmd[0]);
                $this->args[$index] = $cmd[1];
            }
        }
    }

    public function getArg(string $name): string
    {

        if(!isset($this->args[$name])) {

            throw new \Exception('No valid cli argument');
        }

        return $this->args[$name];
    }
}