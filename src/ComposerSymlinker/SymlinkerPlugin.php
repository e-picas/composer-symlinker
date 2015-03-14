<?php

namespace ComposerSymlinker;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;

class SymlinkerPlugin implements PluginInterface
{

    public function activate(Composer $composer, IOInterface $io)
    {
        $composer->getInstallationManager()->addInstaller(
            new LocalInstaller($io, $composer)
        );
    }

}
