<?php


namespace FDTool\PhpHelper\Php;


abstract class PhpShell
{
    public static function restartPhpFpm(string $versionToRestart): void
    {
        $command = sprintf("sudo service php%s-fpm restart", $versionToRestart);

        shell_exec($command);
    }
    /**
     * Complex command to return the current php version
     */
    public static function getPhpVersion(): string
    {
        if (!$currentVersion = trim(shell_exec("php -v | grep -Po '(?!PHP )(7.[0-9]+)' -m 1"))) {
            throw new \RuntimeException("No PHP version found");
        }

        return $currentVersion;
    }
}
