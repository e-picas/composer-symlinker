Composer symlinker
==================

A [Composer](http://getcomposer.org/) plugin to use dependencies as symbolic links to local
copies instead of hard downloaded ones.

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
