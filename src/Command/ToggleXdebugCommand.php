<?php

namespace FDTool\PhpHelper\Command;

use FDTool\PhpHelper\Output\MessageOutput;
use FDTool\PhpHelper\Php\PhpManager;
use FDTool\PhpHelper\Php\PhpShell;
use FDTool\PhpHelper\Xdebug\XdebugShell;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ToggleXdebugCommand extends Command
{
    protected static $defaultName = "fdtool:php-helper:toogle-xdebug";

    private $outputDisplayer;
    private $commandStartTimestamp;

    protected function configure(): void
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Toogle Xdebug activation')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command help you to enable and disable xdebug');
        parent::configure();

        $this->commandStartTimestamp = time();
    }

    private function initCommand(OutputInterface $output): void
    {
        $this->outputDisplayer = new MessageOutput($output);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->initCommand($output);

        $this->toggleXdebug();
        $this->restartPhpFpm();

        $this->outputDisplayer->display(
            sprintf("Command ended in %ss", time() - $this->commandStartTimestamp),
            "comment"
        );

        return Command::SUCCESS;
    }

    private function toggleXdebug(): void
    {
        $xDebugConfigFile = $this->getXDebugConfigFilePath();
        if(XdebugShell::isXdebugEnabled()) {
            $this->outputDisplayer->display("- Xdebug is enabled. Disabling it...");
            XdebugShell::disableXdebug($xDebugConfigFile);

        } else {
            $this->outputDisplayer->display("- Xdebug is disabled. Enabling it...");
            XdebugShell::enableXdebug($xDebugConfigFile);
        }
    }

    private function getXDebugConfigFilePath(): string
    {
        $pathToCheck = sprintf("/etc/php/%s/mods-available/xdebug.ini", PhpShell::getPhpVersion());
        $this->outputDisplayer->display(
            sprintf("- Path to check: %s", $pathToCheck),
            "info"
        );
        if (!file_exists($pathToCheck)) {
            throw new \RuntimeException("Unable to find the Xdebug config");
        }

        return $pathToCheck;
    }

    private function restartPhpFpm(): void
    {
        if($version = PhpShell::getPhpVersion()) {
            $this->outputDisplayer->display(
                sprintf("- Restarting the php%s-fpm", $version)
            );
            PhpShell::restartPhpFpm($version);
        }
    }
}
