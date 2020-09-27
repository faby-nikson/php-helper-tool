<?php


namespace Faby\PhpHelper\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class PhpHelperCommand extends Command
{
    protected function configure(): void
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Git Checker: check your local git projects statuses')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command help you to check your git projects')
            ->addArgument('root-path', InputArgument::REQUIRED, 'Root path where your git projects are.')
            ->addOption('ignore-master-check', null, InputOption::VALUE_OPTIONAL, 'Ignore the check of projects on non-master branch.');

        parent::configure();

        $this->commandStartTimestamp = time();
    }
}
