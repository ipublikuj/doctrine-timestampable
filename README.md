# Doctrine Timestampable

[![Build Status](https://badgen.net/github/checks/ipublikuj/doctrine-timestampable/master?cache=300&style=flast-square)](https://github.com/ipublikuj/doctrine-timestampable)
[![Code coverage](https://badgen.net/coveralls/c/github/ipublikuj/doctrine-timestampable?cache=300&style=flast-square)](https://coveralls.io/github/ipublikuj/doctrine-timestampable)
![PHP](https://badgen.net/packagist/php/ipub/doctrine-timestampable?cache=300&style=flast-square)
[![Licence](https://badgen.net/packagist/license/ipub/doctrine-timestampable?cache=300&style=flast-square)](https://github.com/ipublikuj/doctrine-timestampable/blob/master/LICENSE.md)
[![Downloads total](https://badgen.net/packagist/dt/ipub/doctrine-timestampable?cache=300&style=flast-square)](https://packagist.org/packages/ipub/doctrine-timestampable)
[![Latest stable](https://badgen.net/packagist/v/ipub/doctrine-timestampable/latest?cache=300&style=flast-square)](https://packagist.org/packages/ipub/doctrine-timestampable)
[![PHPStan](https://img.shields.io/badge/PHPStan-enabled-brightgreen.svg?style=flat-square)](https://github.com/phpstan/phpstan)

Timestampable behavior will automate the update of date fields on your Entities in [Nette Framework](http://nette.org/) and [Doctrine 2](http://www.doctrine-project.org/)

## Installation

The best way to install **ipub/doctrine-timestampable** is using [Composer](http://getcomposer.org/):

```sh
composer require ipub/doctrine-timestampable
```

After that you have to register extension in config.neon.

```neon
extensions:
    doctrineTimestampable: IPub\DoctrineTimestampable\DI\DoctrineTimestampableExtension
```

## Documentation

Learn how to register and work with timestampable behavior in [documentation](https://github.com/iPublikuj/doctrine-timestampable/blob/master/docs/en/index.md).

***
Homepage [https://www.ipublikuj.eu](https://www.ipublikuj.eu) and repository [http://github.com/iPublikuj/doctrine-timestampable](http://github.com/iPublikuj/doctrine-timestampable).
