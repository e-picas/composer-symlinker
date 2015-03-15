Composer symlinker
==================

A [Composer](http://getcomposer.org/) plugin to install packages as local symbolic links.

This project is a *temporary* implementation of using symbolic links to local packages as dependencies
to allow a parallel work process. For a descriptive (and commented) problematic, see
<https://github.com/composer/composer/issues/1299>.


Usage
-----

To use this plugin, just add it as a dependency in your `composer.json`:

```json
"piwi/composer-symlinker": "dev-master"
```

You must define concerned local paths or packages as *extra* config entries:

-   `local-dirs`: a list of local paths to scan while searching a local version
    or a package ; the final package path will be completed with `vendor/package` ;
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

Let's say you want to work on a project named `YourProject` base on three dependencies:
`YourPackage1` and `YourPackage2` which are some of your packages, and a third-party
`ExternalPackage` which is not. As you want to work on both *YourProject* and its dependencies
*YourPackageX*, you usually first install your dependencies with Composer (as hard copies), to
let it create a valid `autoload.php`, then you manually replace these hard copies by local
symbolic links to your clones of `YourPackage1` and `YourPackage2` ...

The plugin can do this for you, as long as you well-configure it and forces Composer to
use it when installing your packages.

The common way to force Composer to use the plugin when installing your dependencies would
be to include it in there *dependencies*. In our case, this is not relevant as you only
want to use it to build your local environment (it must not be a requirement for other users).

The "best" way to do is:

```json
"require": {
    "piwi/composer-symlinker": "dev-master"
},
"require-dev": {
    "MyVendor/MyPackage1": "dev-master",
    "MyVendor/MyPackage1": "dev-master",
    "OtherVendor/ExternalPackage": "dev-master"
},
```

This way, you may first run:

    $ composer install --no-dev

to install the plugin, then:

    $ composer update

will use it to install all packages.
