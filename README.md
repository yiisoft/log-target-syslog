<p align="center">
    <a href="https://github.com/yiisoft" target="_blank">
        <img src="https://avatars0.githubusercontent.com/u/993323" height="100px">
    </a>
    <h1 align="center">Yii Logging Library - Syslog Target</h1>
    <br>
</p>

[![Latest Stable Version](https://poser.pugx.org/yiisoft/log-target-syslog/v/stable.png)](https://packagist.org/packages/yiisoft/log-target-syslog)
[![Total Downloads](https://poser.pugx.org/yiisoft/log-target-syslog/downloads.png)](https://packagist.org/packages/yiisoft/log-target-syslog)
[![Build status](https://github.com/yiisoft/log-target-syslog/workflows/build/badge.svg)](https://github.com/yiisoft/log-target-syslog/actions?query=workflow%3Abuild)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/yiisoft/log-target-syslog/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/yiisoft/log-target-syslog/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/yiisoft/log-target-syslog/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/yiisoft/log-target-syslog/?branch=master)
[![Mutation testing badge](https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Fyiisoft%2Flog-target-syslog%2Fmaster)](https://dashboard.stryker-mutator.io/reports/github.com/yiisoft/log-target-syslog/master)
[![static analysis](https://github.com/yiisoft/log-target-syslog/workflows/static%20analysis/badge.svg)](https://github.com/yiisoft/log-target-syslog/actions?query=workflow%3A%22static+analysis%22)
[![type-coverage](https://shepherd.dev/github/yiisoft/log-target-syslog/coverage.svg)](https://shepherd.dev/github/yiisoft/log-target-syslog)

This package provides the Syslog target for the [yiisoft/log](https://github.com/yiisoft/log) library.

## Requirements

- PHP 7.4 or higher.

## Installation

The package could be installed with composer:

```
composer require yiisoft/log-target-syslog --prefer-dist
```

## General usage

Creating a target:

```php
use Yiisoft\Log\Target\Syslog\SyslogTarget;

$syslogTarget = new SyslogTarget($identity, $options, $facility);
```

- `$identity (string)` - The `openlog()` identity.
- `$options (int)` - The `openlog()` options. Defaults to `LOG_ODELAY | LOG_PID`.
- `$facility (int)` - The `openlog()` facility. Defaults to `LOG_USER`.

For more information, see the description of the [`openlog()`](https://www.php.net/openlog) function.

Creating a logger:

```php
$logger = new \Yiisoft\Log\Logger([$syslogTarget]);
```

For a description of using the logger, see the [yiisoft/log](https://github.com/yiisoft/log) package.

For use in the [Yii framework](http://www.yiiframework.com/), see the configuration files:

- [`config/common.php`](https://github.com/yiisoft/log-target-syslog/blob/master/config/common.php)
- [`config/params.php`](https://github.com/yiisoft/log-target-syslog/blob/master/config/params.php)

See [Yii guide to logging](https://github.com/yiisoft/docs/blob/master/guide/en/runtime/logging.md) for more info.

## Testing

### Unit testing

The package is tested with [PHPUnit](https://phpunit.de/). To run tests:

```shell
./vendor/bin/phpunit
```

### Mutation testing

The package tests are checked with [Infection](https://infection.github.io/) mutation framework. To run it:

```shell
./vendor/bin/infection
```

### Static analysis

The code is statically analyzed with [Psalm](https://psalm.dev/). To run static analysis:

```shell
./vendor/bin/psalm
```

### Support the project

[![Open Collective](https://img.shields.io/badge/Open%20Collective-sponsor-7eadf1?logo=open%20collective&logoColor=7eadf1&labelColor=555555)](https://opencollective.com/yiisoft)

### Follow updates

[![Official website](https://img.shields.io/badge/Powered_by-Yii_Framework-green.svg?style=flat)](https://www.yiiframework.com/)
[![Twitter](https://img.shields.io/badge/twitter-follow-1DA1F2?logo=twitter&logoColor=1DA1F2&labelColor=555555?style=flat)](https://twitter.com/yiiframework)
[![Telegram](https://img.shields.io/badge/telegram-join-1DA1F2?style=flat&logo=telegram)](https://t.me/yii3en)
[![Facebook](https://img.shields.io/badge/facebook-join-1DA1F2?style=flat&logo=facebook&logoColor=ffffff)](https://www.facebook.com/groups/yiitalk)
[![Slack](https://img.shields.io/badge/slack-join-1DA1F2?style=flat&logo=slack)](https://yiiframework.com/go/slack)

## License

The Yii Logging Library - Syslog Target is free software. It is released under the terms of the BSD License.
Please see [`LICENSE`](./LICENSE.md) for more information.

Maintained by [Yii Software](https://www.yiiframework.com/).
