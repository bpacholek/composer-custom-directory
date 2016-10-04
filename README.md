composer-custom-directory-installer
===================================

A composer plugin, to install differenty types of composer packages in custom directories outside the default composer default installation path which is in the `vendor` folder.

Based & heavily influenced by repository by `mnsami`:
https://github.com/mnsami/composer-custom-directory-installer
(Moved outside of a fork due to slow or no maintenance by the original author.)  

For the support of [composer/installers](https://github.com/composer/installers/) there is a forked version that add the support of this module on [wpbp/installers](https://github.com/WPBP/installers).

Installation
------------

- Include the composer plugin into your `composer.json` `require` section - for example:

```
  "require":{
    "php": ">=5.4",
    "idct/composer-directory-installer": "1.0.*",
    "monolog/monolog": "*"
  }
```

- In the `extra` section define the custom directory you want to the package to be installed in::

```
  "extra":{
    "installer-paths":{
      "./monolog/": ["monolog/monolog"]
    }
```

 by adding the `installer-paths` part, you are telling composer to install the `monolog` package inside the `monolog` folder in your root directory.

Dynamic paths
=============

Feature which allows you to install packages in different directories depending on a set of predefined tokens.

- For example, a setting:

```
    "extra": {
        "installer-paths": {
            "./packages/{$name}": ["sourcepackage/package_A","sourcepackage/package_B","sourcepackage/package_B"]
        }
    },
```

will install packages provided in the list (`sourcepackage/package_A`, `sourcepackage/package_B`, `sourcepackage/package_C`) in folders:

* `./packages/package_A`
* `./packages/package_B`
* `./packages/package_C`

defined tokens are:

* `$name` which will return the second part of the package name, after `/`. For example for `sourcepackage/package_A` it will be `package_A`. In case of missing parts will return `undefined`.
* `$package` - returns full package name.
* `$vendor` - same as `$name` but works with the first part of the package name.

Flags
=====

Dynamic path definitions support flags for manipulation of the target name. Flags are parsed in a chain from the left to the right therefore the order of them is important

Currently supprted flags are:

* `F` - capitalizes first letter.
* `P` - changes all entries of a `_` or `-` followed by a character to only that character, capitalized.

Usage:

Flags should be entered after the flag variable followed by a pipe `|`.

For example:

```
    "extra": {
        "installer-paths": {
            "./packages/{$name|FP}": ["sourcepackage/my_package1","sourcepackage/my_package2","sourcepackage/my_package3"]
        }
    },
```

will install the packages into folders:

* `./packages/MyPackage1`
* `./packages/MyPackage2`
* `./packages/MyPackage3`

As the flag `F` will make the first letter a capital letter and the flag `P` will remove all `_` (or `-`) and capitalize the following letter.


