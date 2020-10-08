<?php

namespace FDTool\PhpHelper\Command;

use FDTool\PhpHelper\Output\MessageOutput;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ChangePhpVersionCommand extends Command
{
    protected static $defaultName = "fdtool:php-helper:change-version";

    private $outputDisplayer;
    private $commandStartTimestamp;
    private $version;

    protected function configure(): void
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Change PHP active version')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command help you to change the current version of PHP on your computer')
            ->addArgument('version', InputArgument::REQUIRED, 'PHP version to active. 7.1, 7.2, etc.');
        parent::configure();

        $this->commandStartTimestamp = time();
    }

    private function initOptionsAndArguments(InputInterface $input): void
    {
        $this->version = (string)$input->getArgument("version");
    }

    private function initOutput(OutputInterface $output): void
    {
        $this->outputDisplayer = new MessageOutput($output);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->initOptionsAndArguments($input);
        $this->initOutput($output);

        $this->changePhpVersion();

        $this->outputDisplayer->display(
            sprintf("Command ended in %ss", time() - $this->commandStartTimestamp),
            "comment"
        );

        return Command::SUCCESS;
    }

    private function changePhpVersion(): void
    {
        $this->outputDisplayer->display(
            sprintf("Activate the following PHP version %s", $this->version),
            "info"
        );

        if (!file_exists(sprintf("/usr/bin/php%s", $this->version))) {
            throw new \RuntimeException("The PHP version has not been found");
        }

        $command = sprintf('sudo update-alternatives --set php "/usr/bin/php%s"', $this->version);
        $this->outputDisplayer->display(
            sprintf("Run the following command: %s", $command),
            "info"
        );

        shell_exec($command);
    }
}
