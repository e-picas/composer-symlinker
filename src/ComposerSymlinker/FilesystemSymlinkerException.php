<?php
/**
 * This file is part of <https://github.com/piwi/composer-symlinker>
 */

namespace ComposerSymlinker;

/**
 * Exception thrown on issues when trying to create a local symbolic link
 *
 * @author piwi <me@e-piwi.fr>
 */
class FilesystemSymlinkerException extends \Exception
{

    public function __construct($message = null, $code = null, \Exception $previous = null)
    {
        parent::__construct("Filesystem symlinker exception: \n".$message, $code, $previous);
    }

}
