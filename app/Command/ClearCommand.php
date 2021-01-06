<?php

namespace App\Command;

use App\Model\Invoice;
use App\Model\Position;
use Mocchi\Console\Command;
use Mocchi\Console\CommandArg;
use Mocchi\Console\Input;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Ramsey\Uuid\Uuid;

class ClearCommand  extends Command {

    public function register(string $name): void
    {
        $this->name = $name;
        $this->version = '0.0.7';
        $this->description = 'clear cache';
    }

    public function execute(Input $input, OutputInterface $output): int {
        $forbidden = ['.', '..', '/'];
        $path = implode(DIRECTORY_SEPARATOR, [APP_PATH, 'var', 'cache',  '*']);
        $list = glob($path);

        foreach ($list as $path) {

            if(!in_array($path, $forbidden)) {

                system('rm -rf ' . escapeshellarg($path));

                $output->writeln('delete ' . $path);
            }
        }

        return Command::SUCCESS;
    }
}