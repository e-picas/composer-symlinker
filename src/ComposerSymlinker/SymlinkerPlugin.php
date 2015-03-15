<?php
/**
 * This file is part of <https://github.com/piwi/composer-symlinker>
 */

namespace ComposerSymlinker;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;

/**
 * @author piwi <me@e-piwi.fr>
 */
class SymlinkerPlugin implements PluginInterface
{

    /**
     * Just add the \ComposerSymlinker\LocalInstaller new installer
     *
     * @param \Composer\Composer $composer
     * @param \Composer\IO\IOInterface $io
     */
    public function activate(Composer $composer, IOInterface $io)
    {
        $composer->getInstallationManager()->addInstaller(
            new LocalInstaller($io, $composer)
        );
    }

}
