# ramsey/devtools-lib Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## 2.2.1 - 2024-04-27

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Allow projects using ramsey/devtools-lib to install version 7 of symfony/console, symfony/filesystem, or symfony/process.

## 2.2.0 - 2023-10-27

### Added

- Allow tools based on this one to override the default prefix

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 2.1.1 - 2023-04-25

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Render help with proper styling when using in the context of a Composer plugin

## 2.1.0 - 2023-04-25

### Added

- Allow setting memory limit for commands

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 2.0.1 - 2023-04-24

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Accommodate line endings on Windows systems

## 2.0.0 - 2023-04-24

### Added

- You may use `devtools` as a Composer `extra` property name, instead of `ramsey/devtools`

- Allow changing the Composer `extra` property name if developing your own devtools on top of this package

### Changed

- Bump minimum supported version of PHP to 8.1

- Migrate all commands to Symfony console commands

- Wrap commands with `ComposerCommand` as a proxy, when running as a Composer plugin

- Enable `bin/devtools` for standalone CLI use

### Deprecated

- Nothing.

### Removed

- Remove deprecated `captainhook:install` command

- Remove deprecated `pre-commit` command

### Fixed

- Nothing.

## 1.4.0 - 2022-01-27

### Added

- If provided, use Composer's `process-timeout` configuration option as the timeout for commands provided by this tool. See the [Composer documentation](https://getcomposer.org/doc/06-config.md#process-timeout), for more details.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Enable TTY mode for commands when in non-Windows environments.

## 1.3.0 - 2021-11-13

### Added

- Update PHPStan to the version 1.0 series

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 1.2.2 - 2021-08-08

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Print out text coverage report with test:coverage:ci command

## 1.2.1 - 2021-07-14

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Remove `lint:pds` from `lint:all` command.

## 1.2.0 - 2021-07-11

### Added

- Add `lint:style` command to check for coding standards issues.
- Add `lint:syntax` command to check for syntax errors.
- Add `license` command that uses [madewithlove/license-checker](https://github.com/madewithlove/license-checker-php).
- Add `clean:coverage` command to remove code coverage logging files.
- Improve documentation for all commands.

### Changed

- The `lint` command is now aliased to `lint:all` and executes `lint:pds`, `lint:syntax`, and `lint:style`.

### Deprecated

- Deprecate `pre-commit` command and make it a no-op. This also deprecates `Ramsey\Dev\Tools\Composer\Command\PreCommitCommand`.
- Deprecate `Ramsey\Dev\Tools\Composer\Command\CaptainHookInstallCommand`. This was only used internally and has been replaced with [captainhook/plugin-composer](https://github.com/captainhookphp/plugin-composer).

### Removed

- Nothing.

### Fixed

- Nothing.

## 1.1.0 - 2021-03-21

### Added

- Add lint:pds command to validate projects against [pds/skeleton](https://github.com/php-pds/skeleton).

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 1.0.4 - 2021-03-21

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Update roave/security-advisories to use dev-latest.

## 1.0.3 - 2021-02-05

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Discontinue use of deprecated ReflectionParameter::isArray() method.

## 1.0.2 - 2020-10-26

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Register Hamcrest functions from base testcase.

## 1.0.1 - 2020-10-09

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Move Hamcrest functions to autoload-dev.

## 1.0.0 - 2020-10-08

### Added

- Move [ramsey/devtools](https://github.com/ramsey/devtools) library to a separate package to allow for extension.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.
