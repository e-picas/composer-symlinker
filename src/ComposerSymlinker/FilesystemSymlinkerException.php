<?php

namespace ComposerSymlinker;

use Composer\Downloader\FilesystemException;

/**
 * Exception thrown when issues exist when trying to create a local symbolic link
 */
class FilesystemSymlinkerException extends FilesystemException
{

    public function __construct($message = null, $code = null, \Exception $previous = null)
    {
        parent::__construct("Filesystem symlinker exception: \n".$message, $code, $previous);
    }

}
