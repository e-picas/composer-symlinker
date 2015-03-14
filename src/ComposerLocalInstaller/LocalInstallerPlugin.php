<?php

namespace ComposerLocalInstaller;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;

class LocalInstallerPlugin implements PluginInterface
{

    public function activate(Composer $composer, IOInterface $io)
    {
        $installer = new LocalInstaller($io, $composer);
        $composer->getInstallationManager()->addInstaller($installer);
    }

}
