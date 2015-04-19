Composer symlinker
==================

A [Composer](http://getcomposer.org/) plugin to install packages as local symbolic links.

This plugin is a *temporary* implementation of using symbolic links to local packages as dependencies
to allow a parallel work process. For a descriptive (and commented) problematic, see
<https://github.com/composer/composer/issues/1299>.


Usage
-----

To use it, just add it as a dependency in your `composer.json`:

```json
"piwi/composer-symlinker": "dev-master"
```

You must define concerned local paths or packages as *extra* config entries:

-   `local-dirs`: a list of local paths to scan while searching a local version
    of a package ; the final package path will be completed with `vendor/package` ;
-   `local-packages`: an array of `vendor/package => local_path` items ;
-   `local-vendors`: a list of vendors to restrict local scanning.


```json
"extra": {
    "local-dirs": [
        "/my/absolute/local/path1",
        "/my/absolute/local/path2"
    ],
    "local-vendors": [
        "vendor1",
        "vendor2"
    ],
    "local-packages": {
        "vendor/package1": "/my/absolute/path/to/vendor/package1",
        "vendor/package2": "/my/absolute/path/to/vendor/package2"
    }
}
```


Windows users warning
---------------------

The plugin uses the internal [`symlink()`](http://php.net/symlink) PHP function.
See *Windows* restrictions on the manual.


Quick tutorial
--------------

Let's say we want to work on a project named `MyProject` base on three dependencies:
`MyPackage1` and `MyPackage2` which are some of our packages, and a third-party
`ExternalPackage` which is not. Let's say our localhost architecture is the following:

    [DOCUMENT_ROOT]
    |
    |projects/
    |-------- MyVendor/
    |----------------- MyPackage1/      // this is a clone of MyVendor/MyPackage1
    |
    |MyPackage2/                        // this is a clone of MyVendor/MyPackage2
    |
    |MyProject/                         // this is the project we currently work on
                                        // which depends on other three packages

Note: `MyVendor/MyPackage1` and `MyVendor\MyPackage2` must exist in some composer repository
already before they can be considered by composer to be installed or symlinked.  Typically,
packages will already be accessible via Packagist.  But if they are local only (no already
configured repository) then a local one will need to be added to your composer.json.
It might look like this:

      "repositories": [
        {
          "type":"vcs",
          "url":"/path/to/DOCUMENT_ROOT/projects/MyVendor/MyPackage1"
        }
      ]

As we want to work on both *MyProject* and its dependencies *MyPackageX*, we would usually 
first install our dependencies with Composer (as hard copies), to let it create a valid 
`autoload.php`, then we would manually replace these hard copies by local symbolic links to 
our clones of `MyPackage1` and `MyPackage2` ...

Well, the plugin can do this for us, as long as we well-configure it and forces Composer to
use it when installing our dependencies.

The common way to force Composer to use the plugin when installing a dependency should
be to include it in its `require` statement. In our case, this is not relevant as we only
want to use it to build our local environment (it must not be a requirement for other users).
A good way to do so is to create a "development-only" composer's configuration file for our
project to let us install local dependencies with the plugin in our environment but let 
final users have a "real-life" behavior (the default one).

Our "development-only" `composer.json` could be:

```json
"require": {
    "piwi/composer-symlinker": "1.*"
},
"require-dev": {
    "MyVendor/MyPackage1": "dev-master",
    "MyVendor/MyPackage2": "dev-master",
    "OtherVendor/ExternalPackage": "dev-master"
},
"extra": {
    "local-dirs": "/path/to/DOCUMENT_ROOT/projects/",
    "local-packages": {
        "MyVendor/MyPackage2": "/path/to/DOCUMENT_ROOT/MyPackage2"
    }
}
```

This way, we may first run:

    $ composer install --no-dev

to install the plugin, then:

    $ composer update

will use it to install all packages.

Our final `vendor` directory should be something like:

    [vendor]
    |
    |MyVendor/
    |--------- MyPackage1   => /path/to/DOCUMENT_ROOT/projects/MyVendor/MyPackage1 (symlink)
    |--------- MyPackage2   => /path/to/DOCUMENT_ROOT/MyPackage2 (symlink)
    |
    |OtherVendor/
    |----------- ExternalPackage/ (hard copy)

and our autoloader will be still valid.
