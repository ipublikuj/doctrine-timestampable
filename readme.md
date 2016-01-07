# Doctrine Timestampable

[![Build Status](https://img.shields.io/travis/iPublikuj/doctrine-timestampable.svg?style=flat-square)](https://travis-ci.org/iPublikuj/doctrine-timestampable)
[![Scrutinizer Code Coverage](https://img.shields.io/scrutinizer/coverage/g/iPublikuj/doctrine-timestampable.svg?style=flat-square)](https://scrutinizer-ci.com/g/iPublikuj/doctrine-timestampable/?branch=master)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/iPublikuj/doctrine-timestampable.svg?style=flat-square)](https://scrutinizer-ci.com/g/iPublikuj/doctrine-timestampable/?branch=master)
[![Latest Stable Version](https://img.shields.io/packagist/v/ipub/doctrine-timestampable.svg?style=flat-square)](https://packagist.org/packages/ipub/doctrine-timestampable)
[![Composer Downloads](https://img.shields.io/packagist/dt/ipub/doctrine-timestampable.svg?style=flat-square)](https://packagist.org/packages/ipub/doctrine-timestampable)
[![License](https://img.shields.io/packagist/l/ipub/doctrine-timestampable.svg?style=flat-square)](https://packagist.org/packages/ipub/doctrine-timestampable)
[![Dependency Status](https://img.shields.io/versioneye/d/user/projects/568ecb74691e2d003d00007c.svg?style=flat-square)](https://www.versioneye.com/user/projects/568ecb74691e2d003d00007c)

Timestampable behavior will automate the update of date fields on your Entities in [Nette Framework](http://nette.org/) and [Doctrine 2](http://www.doctrine-project.org/)

## Installation

The best way to install ipub/doctrine-timestampable is using [Composer](http://getcomposer.org/):

```sh
$ composer require ipub/doctrine-timestampable
```

After that you have to register extension in config.neon.

```neon
extensions:
	doctrineTimestampable: IPub\DoctrineTimestampable\DI\DoctrineTimestampableExtension
```

## Documentation

Learn how to register and work with timestampable behavior in [documentation](https://github.com/iPublikuj/doctrine-timestampable/blob/master/docs/en/index.md).

***
Homepage [http://www.ipublikuj.eu](http://www.ipublikuj.eu) and repository [http://github.com/iPublikuj/doctrine-timestampable](http://github.com/iPublikuj/doctrine-timestampable).
