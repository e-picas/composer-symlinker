<?php

namespace ComposerSymlinker;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Composer\Installer\LibraryInstaller;
use Composer\Util\Filesystem;

/**
 * @author piwi <me@e-piwi.fr>
 */
class LocalInstaller extends LibraryInstaller
{

    protected $localDirs        = array();
    protected $localVendors     = array();
    protected $localPackages    = array();

    public function __construct(IOInterface $io, Composer $composer, $type = 'library', Filesystem $filesystem = null)
    {
        parent::__construct($io, $composer, $type, $filesystem);
        $extra = $composer->getPackage()->getExtra();
        $this->setLocalDirs(
            isset($extra['local-dirs']) ? rtrim($extra['local-dirs'], '/') : dirname(getcwd())
        );
        if (isset($extra['local-vendors'])) {
            $this->setLocalVendors($extra['local-vendors']);
        }
        if (isset($extra['local-packages'])) {
            $this->setLocalPackages($extra['local-packages']);
        }
    }

    public function setLocalDirs($dirs)
    {
        $dirs = is_array($dirs) ? $dirs : array($dirs);
        foreach ($dirs as $i=>$dir) {
            if (!file_exists($dir)) {
                throw new \InvalidArgumentException(
                    sprintf('Local path not found: %s', $dir)
                );
            }
            $dirs[$i] = rtrim($dir, '/');
        }
        $this->localDirs = $dirs;
        return $this;
    }

    public function setLocalVendors($vendors)
    {
        $this->localVendors = is_array($vendors) ? $vendors : array($vendors);
        return $this;
    }

    public function setLocalPackages(array $paths)
    {
        $this->localPackages = $paths;
        return $this;
    }

    public function supports($packageType)
    {
        return parent::supports($packageType);
    }

    public function isLocalSymlink($path)
    {
        $link = readlink($path);

        // declared paths
        if (in_array($link, $this->localPackages)) {
            return true;
        }

        // local directories
        foreach ($this->localDirs as $dir) {
            if (substr_count($dir, $link)>0) {
                return true;
            }
        }

        return false;
    }

    protected function getLocalPackagePath(PackageInterface $package)
    {
        // vendors restriction ?
        $vendor = $this->getPackageVendorName($package);
        if (!empty($this->localVendors) && !in_array($vendor, $this->localVendors)) {
            return null;
        }

        // declared paths
        if (array_key_exists($package->getPrettyName(), $this->localPackages)) {
            $local = $this->localPackages[$package->getPrettyName()];
            if (!file_exists($local) || !is_dir($local) || !file_exists($local . '/composer.json')) {
                throw new \InvalidArgumentException(
                    sprintf('Local path "%s" defined for package "%s" is not valid', $local, $package->getPrettyName())
                );
            }
            return $local;
        }

        // local directories
        foreach ($this->localDirs as $dir) {
            $local = $dir . '/' . $package->getPrettyName();
            if (file_exists($local) && is_dir($local) && file_exists($local . '/composer.json')) {
                return $local;
            }
        }

        return null;
    }

    protected function installCode(PackageInterface $package)
    {
        $local_path = $this->getLocalPackagePath($package);
        if (!is_null($local_path)) {
            $this->io->writeError("  - Installing <info>" . $package->getName() . "</info> (<comment>as a symbolic link of " . $local_path . "</comment>)");
            $this->initializeVendorSubdir($package);
            if (false === @symlink($local_path, $this->getInstallPath($package))) {
                throw new FilesystemSymlinkerException(
                    sprintf('Symlink fails: "%s" => "%s"', $local_path, $this->getInstallPath($package))
                );
            }
            return true;
        }
        return parent::installCode($package);
    }

    protected function updateCode(PackageInterface $initial, PackageInterface $target)
    {
        if ($this->isLocalSymlink($initial)) {
            $this->initializeVendorSubdir($target);
            $this->filesystem->rename($this->getInstallPath($initial), $this->getInstallPath($target));
            return true;
        }
        return parent::updateCode($initial, $target);
    }

    protected function removeCode(PackageInterface $package)
    {
        if ($this->isLocalSymlink($package)) {
            $this->filesystem->unlink($this->getInstallPath($package));
            return true;
        }
        return parent::removeCode($package);
    }

    protected function getPackageVendorName(PackageInterface $package)
    {
        list($vendor, $name) = explode('/', $package->getName());
        return $vendor;
    }

    protected function initializeVendorSubdir(PackageInterface $package)
    {
        $this->initializeVendorDir();
        $this->filesystem->ensureDirectoryExists($this->vendorDir.'/'.$this->getPackageVendorName($package));
    }

}
