# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [4.1.0] - 2022-01-06

### Added
- Support for Protobuf
- Add MapperCollectionInterface

## [4.0.0] - 2021-11-09

### Added
- Support for snesio/framework-extra-bundle 6.x

### Removed
- Support for PHP 7.1, 7.2 and 7.3
- Support for Symfony 4.4, 5.0, 5.1, 5.2


## [3.2.0] - 2021-06-28

### Added

- PHP 8.0 support

## [3.1.0] - 2020-12-16

### Changed

- throw new `UnmappableException` is not mappable

## [3.0.0] - 2020-06-25

### Fixed

- exception serialization handling in fos rest bundle 3.0

### Removed

- support for PHP < 7.2 (7.1 is end of life)
- support for fos rest bundle 2.x
- support for symfony 4.3 (end of life)

## [2.0.2] - 2020-03-26

### Added

- support for fos rest bundle 3.0

### Fixed

- fos rest bundle >= 2.8 deprecation

### Removed

- dependency on fos-rest templating integration

## [2.0.1] - 2020-03-23

### Fixed

- exception when mapping arrays of data

## [2.0.0] - 2020-03-20

### Added

- Support for Symfony 5.0
- PHP quality checker to this project

### Changed

### Removed

- unused doctrine/orm dependency
- support for symfony < 4.3

### Fixed

## [1.0.5] - 2020-08-07

Last release without a changelog ;-)

[unreleased]: https://github.com/byWulf/apitk-dtomapper-bundle/compare/4.1.0...HEAD
[4.1.0]: https://github.com/byWulf/apitk-dtomapper-bundle/compare/4.0.0...4.1.0
[4.0.0]: https://github.com/byWulf/apitk-dtomapper-bundle/compare/3.2.0...4.0.0
[3.2.0]: https://github.com/byWulf/apitk-dtomapper-bundle/compare/3.1.0...3.2.0
[3.1.0]: https://github.com/byWulf/apitk-dtomapper-bundle/compare/3.0.0...3.1.0
[3.0.0]: https://github.com/byWulf/apitk-dtomapper-bundle/compare/2.0.2...3.0.0
[2.0.2]: https://github.com/byWulf/apitk-dtomapper-bundle/compare/2.0.1...2.0.2
[2.0.1]: https://github.com/byWulf/apitk-dtomapper-bundle/compare/2.0.0...2.0.1
[2.0.0]: https://github.com/byWulf/apitk-dtomapper-bundle/compare/1.0.5...2.0.0
[1.0.5]: https://github.com/byWulf/apitk-dtomapper-bundle/compare/1.0.4...1.0.5
