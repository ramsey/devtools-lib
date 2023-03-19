<h1 align="center">ramsey/devtools-lib</h1>

<p align="center">
    <strong>The library behind <a href="https://github.com/ramsey/devtools">ramsey/devtools</a>, allowing for extension of the ramsey/devtools Composer plugin.</strong>
</p>

<p align="center">
    <a href="https://github.com/ramsey/devtools-lib"><img src="https://img.shields.io/badge/source-ramsey/devtools--lib-blue.svg?style=flat-square" alt="Source Code"></a>
    <a href="https://packagist.org/packages/ramsey/devtools-lib"><img src="https://img.shields.io/packagist/v/ramsey/devtools-lib.svg?style=flat-square&label=release" alt="Download Package"></a>
    <a href="https://php.net"><img src="https://img.shields.io/packagist/php-v/ramsey/devtools-lib.svg?style=flat-square&colorB=%238892BF" alt="PHP Programming Language"></a>
    <a href="https://github.com/ramsey/devtools-lib/blob/main/LICENSE"><img src="https://img.shields.io/packagist/l/ramsey/devtools-lib.svg?style=flat-square&colorB=darkcyan" alt="Read License"></a>
    <a href="https://github.com/ramsey/devtools-lib/actions/workflows/continuous-integration.yml"><img src="https://img.shields.io/github/actions/workflow/status/ramsey/devtools-lib/continuous-integration.yml?branch=2.x&style=flat-square&logo=github" alt="Build Status"></a>
    <a href="https://codecov.io/gh/ramsey/devtools-lib"><img src="https://img.shields.io/codecov/c/gh/ramsey/devtools-lib?label=codecov&logo=codecov&style=flat-square" alt="Codecov Code Coverage"></a>
    <a href="https://shepherd.dev/github/ramsey/devtools-lib"><img src="https://img.shields.io/endpoint?style=flat-square&url=https%3A%2F%2Fshepherd.dev%2Fgithub%2Framsey%2Fdevtools-lib%2Fcoverage" alt="Psalm Type Coverage"></a>
</p>

## About

This library is what powers the [ramsey/devtools](https://github.com/ramsey/devtools)
[Composer](https://getcomposer.org) plugin. Since you can't extend Composer
plugins, this exists to allow for extension. If you don't need to extend this
for your own plugin needs, and you want the functionality provided by the plugin,
check out [ramsey/devtools](https://github.com/ramsey/devtools) instead.

This package also provides the `devtools` CLI tool, which you may use even if
you do not want to use the plugin functionality.

This project adheres to a [code of conduct](CODE_OF_CONDUCT.md).
By participating in this project and its community, you are expected to
uphold this code.

## Installation

Install this package with [Composer](https://getcomposer.org).

``` bash
composer require --dev ramsey/devtools-lib
```

## Usage

If using this library as a standalone tool, and not as a Composer plugin, type
`./vendor/bin/devtools list` to see the list of commands this library provides.

``` bash
./vendor/bin/devtools list
```

> Install [ramsey/devtools](https://github.com/ramsey/devtools), instead, if you
> do not need to extend this library to create your own Composer plugin.

### Extending or Overriding ramsey/devtools-lib Commands

Maybe the commands ramsey/devtools-lib provides don't do everything you need, or
maybe you want to replace them entirely. The configuration allows you to do
this!

Using the `ramsey/devtools.commands` property in the `extra` section of
`composer.json`, you may specify any command (*without* your custom prefix, if
you've configured one) as having other scripts to run, in addition to the
command's default behavior, or you may override the default behavior entirely.

Specifying additional scripts works exactly like
[writing custom commands](https://getcomposer.org/doc/articles/scripts.md#writing-custom-commands)
in `composer.json`, but the location is different. Everything you can do with
a custom Composer command, you can do here because they're the same thing.

``` json
{
    "extra": {
        "ramsey/devtools": {
            "command-prefix": "my-prefix",
            "commands": {
                "lint:all": {
                    "script": "@mylint"
                },
                "test:all": {
                    "script": [
                        "@mylint",
                        "@phpbench"
                    ]
                }
            }
        }
    },
    "scripts": {
        "mylint": "parallel-lint src tests",
        "phpbench": "phpbench run"
    }
}
```

In this way, when you run `./vendor/bin/devtools lint:all` or
`./vendor/bin/devtools test:all`, it will execute the default behavior first and
then run your additional commands. To override the default behavior so that it
doesn't run at all and only your scripts run, specify the `override` property
and set it to `true`.

``` json
{
    "extra": {
        "ramsey/devtools": {
            "commands": {
                "lint:all": {
                    "override": true,
                    "script": "parallel-lint src tests"
                }
            }
        }
    }
}
```

## Contributing

Contributions are welcome! To contribute, please familiarize yourself with
[CONTRIBUTING.md](CONTRIBUTING.md).

## Coordinated Disclosure

Keeping user information safe and secure is a top priority, and we welcome the
contribution of external security researchers. If you believe you've found a
security issue in software that is maintained in this repository, please read
[SECURITY.md](SECURITY.md) for instructions on submitting a vulnerability report.

## Copyright and License

The ramsey/devtools-lib library is copyright © [Ben Ramsey](https://benramsey.com)
and licensed for use under the terms of the
MIT License (MIT). Please see [LICENSE](LICENSE) for more information.
