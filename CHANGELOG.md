# Yii Logging Library - Syslog Target Change Log

## 2.1.0 December 13, 2025

- New #54: Add optional `$levels` parameter to `SyslogTarget` constructor allowing log level filtering at instantiation (@samdark)
- Enh #45: Remove dead code that check case when `syslog()` returns false (@vjik)

## 2.0.0 February 17, 2023

- Chg #32: Adapt configuration group names to Yii conventions (@vjik)

## 1.1.0 May 23, 2022

- Chg #24: Raise the minimum `yiisoft/log` version to `^2.0` and the minimum PHP version to 8.0 (@rustamwin)

## 1.0.2 August 26, 2021

- Bug #20: Remove `Psr\Log\LoggerInterface` definition from configuration for using multiple targets to application (@devanych)

## 1.0.1 March 23, 2021

- Chg: Adjust config for new config plugin (@samdark)

## 1.0.0 February 11, 2021

Initial release.
