<?php

namespace FDTool\PhpHelper\Xdebug;

abstract class XdebugShell
{
    public static function isXdebugEnabled(): bool
    {
        return (bool) shell_exec("php -m | grep 'xdebug'");
    }

    public static function enableXdebug(string $xDebugConfigFile): void
    {
        shell_exec(
            sprintf("sudo sed -i 's/^#//' %s && export XDEBUG_CONFIG=\"remote_enable=1\"", $xDebugConfigFile)
        );
    }

    public static function disableXdebug(string $xDebugConfigFile): void
    {
        shell_exec(
            sprintf("sudo sed -i 's/^/#/' %s && export XDEBUG_CONFIG=\"remote_enable=0\"", $xDebugConfigFile)
        );
    }
}
