<p align="center">
    <a href="https://github.com/yii-extension" target="_blank">
        <img src="https://lh3.googleusercontent.com/ehSTPnXqrkk0M3U-UPCjC0fty9K6lgykK2WOUA2nUHp8gIkRjeTN8z8SABlkvcvR-9PIrboxIvPGujPgWebLQeHHgX7yLUoxFSduiZrTog6WoZLiAvqcTR1QTPVRmns2tYjACpp7EQ=w2400" height="100px">
    </a>
    <h1 align="center">Flexible user registration and authentication module for Yii3.</h1>
    <br>
</p>

[![Total Downloads](https://img.shields.io/packagist/dt/yii-extension/user)](https://packagist.org/packages/yii-extension/user)
[![codecov](https://codecov.io/gh/yii-extension/user/branch/main/graph/badge.svg?token=AZAF464ILD)](https://codecov.io/gh/yii-extension/user)
[![static analysis](https://github.com/yii-extension/user/workflows/static%20analysis/badge.svg)](https://github.com/yii-extension/user/actions?query=workflow%3A%22static+analysis%22)
[![type-coverage](https://shepherd.dev/github/yii-extension/user/coverage.svg)](https://shepherd.dev/github/yii-extension/user)


| Css Framework |  PHP  | Build |
|:-------------:|:-----:|:-----:|
|[[user-view-bulma]](https://github.com/yii-extension/user-view-bulma)|**7.4 - 8.0**|[![bulma](https://github.com/yii-extension/user/workflows/bulma/badge.svg)](https://github.com/yii-extension/user/actions)
|[[user-view-bootstrap5]](https://github.com/yii-extension/user-view-bootstrap5)|**7.4 - 8.0**|[![boostrap5](https://github.com/yii-extension/user/workflows/bootstrap5/badge.svg)](https://github.com/yii-extension/user/actions)

<br/>
<br/>

<p align="center">
    <a href="https://github.com/yii-extension/app-bulma" target="_blank">
        <img src="https://lh3.googleusercontent.com/0NUwRte-ZTFEICMVHaJy5goeSubb06ocqSHeU0e3OyaC6OQLM04pgTCirb7OZH8HDvAhZjEU6psRiiB-LBHvKE9GAVwQNL0Cw6OiJBodr4vud31ZzAPWR2fUszMTsCRQlu-Ppctsqw=w2400">
    </a>
</p>

Yii demo application for active record with db-sqlite is best for rapidly creating projects.

## Directory structure

      config/             contains application configurations
      resources/layout    contains layout files for the web application
      resources/mail      contains layout and view files for mailer
      resources/view      contains view files for the web application
      src/                application directory
          Action          contains action controller classes
          ActiveRecord    contains active record classes
          Form            contains form classes
          Migration       contains migration classes
          Repository      contains repository classes
          Service         contains services classes

## Requirements

The minimum requirement by this project template that your Web server supports PHP 7.4.0.

## Installation

With application template `yii-extension/app-bulma`:

Bulma css framework:

```php
composer create-project --prefer-dist --stability dev yiisoft/app app
composer require yii-extension/user:@dev yii-extension/user-view-bulma:@dev
```

Bootstrap5 css framework:

```php
composer create-project --prefer-dist --stability dev yiisoft/app app
composer require yii-extension/user:@dev yii-extension/user-view-bootstrap5:@dev
```

## Run command console

```shell
/vendor/bin/yii
```

## Run migration

Application template:

```shel
/vendor/bin/yii migrate/up
```

In developer mode without application template:

```shel
/vendor/bin/yii --config=tests migrate/up
```

## Using PHP built-in server

```shell
php -S 127.0.0.1:8080 -t public
```

## Wait till it is up, then open the following URL in your browser

```shell
http://localhost:8080
```

## Includes the following features:

- [x] User module:
    - [x] /auth/login - Display login form.
    - [x] /auth/logout - Logs the user out.
    - [x] /registration/register - Displays registration form.
    - [x] /registration/resend - Displays resend form.

Note: check the directory `/runtime/mail`, the emails are stored in it.

## Codeception testing

The package is tested with [Codeception](https://github.com/Codeception/Codeception). To run tests:

```shell
php -S 127.0.0.1:8080 -t public > yii.log 2>&1 &
vendor/bin/codecept run
```

## Static analysis

The code is statically analyzed with [Psalm](https://psalm.dev/docs). To run static analysis:

```shell
/vendor/bin/psalm
```
