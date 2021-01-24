<p align="center">
    <a href="https://github.com/yii-extension" target="_blank">
        <img src="https://lh3.googleusercontent.com/ehSTPnXqrkk0M3U-UPCjC0fty9K6lgykK2WOUA2nUHp8gIkRjeTN8z8SABlkvcvR-9PIrboxIvPGujPgWebLQeHHgX7yLUoxFSduiZrTog6WoZLiAvqcTR1QTPVRmns2tYjACpp7EQ=w2400" height="100px">
    </a>
    <h1 align="center">Flexible user registration and authentication module for Yii3.</h1>
    <br>
</p>

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/58ee145c728f48a4867b6096537df634)](https://app.codacy.com/gh/yii-extension/user?utm_source=github.com&utm_medium=referral&utm_content=yii-extension/user&utm_campaign=Badge_Grade)
[![bulma](https://github.com/yii-extension/user/workflows/bulma/badge.svg)](https://github.com/yii-extension/user-view-bulma)
[![boostrap5](https://github.com/yii-extension/user/workflows/bootstrap5/badge.svg)](https://github.com/yii-extension/user-view-bootstrap5)
[![codecov](https://codecov.io/gh/yii-extension/user/branch/main/graph/badge.svg?token=AZAF464ILD)](https://codecov.io/gh/yii-extension/user)
[![static analysis](https://github.com/yii-extension/user/workflows/static%20analysis/badge.svg)](https://github.com/yii-extension/user/actions?query=workflow%3A%22static+analysis%22)
[![type-coverage](https://shepherd.dev/github/yii-extension/user/coverage.svg)](https://shepherd.dev/github/yii-extension/user)

<br/>

Flexible user registration and authentication module for Yii3.

## Directory structure

      config/             contains application configurations
      resources/layout    contains layout files for the web application
      resources/mail      contains layout and view files for mailer
      resources/view      contains view files for the web application
      src/                application directory
          Action          contains action controller classes
          ActiveRecord    contains active record classes
          Form            contains form classes
          Helper          contains helper classes
          Middleware      contains class middleware
          Migration       contains migration classes
          Repository      contains repository classes
          Service         contains service classes

## Project

In this link you will find the lists of tasks to implement: [task-list](https://github.com/yii-extension/user/projects/1)

## Requirements

The minimum requirement by this project template that your Web server supports PHP 7.4.0.

## Installation

With application template [yiisoft/app](https://github.com/yiisoft/app):

Bulma css framework:

```php
composer create-project --prefer-dist --stability dev yiisoft/app app
composer require yii-extension/user:@dev yii-extension/user-view-bulma:@dev
```

With application template [yii-extension/app-bootstrap5](https://github.com/yii-extension/app-bootstrap5):

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
  - [x] [/login] - Display login form.
  - [x] [/logout] - Log the user out.
  - [x] [/confirm[/{id}/{token}]] - Confirms a user (requires id and token query params).
  - [x] [/profile] - Displays profile form.
  - [x] [/register] - Displays registration form.
  - [x] [/request] - Displays recovery request form.
  - [x] [/resend] - Displays resend form.
  - [x] [/reset[/{id}/{token}]] - Displays password reset form (requires id and token query params).
  - [x] [/email/change] - Displays email change form.
  - [x] [/email/attempt[/{id}/{token}]] - Confirm email change (requires id and token query params).

Note: check the directory `/runtime/mail`, the emails are stored in it.

## Codeception testing

The package is tested with [Codeception](https://github.com/Codeception/Codeception). To run tests:

```shell
php -S 127.0.0.1:8080 -t tests/_data/public > /dev/null 2>&1&
vendor/bin/codecept run
```

## Static analysis

The code is statically analyzed with [Psalm](https://psalm.dev/docs). To run static analysis:

```shell
/vendor/bin/psalm
```

## License

The Flexible user registration and authentication module for Yii3. It is released under the terms of the BSD License.

Please see [`LICENSE`](./LICENSE.md) for more information.

Maintained by [Yii Extension](https://github.com/yii-extension).

[![License](https://poser.pugx.org/yii-extension/user/license)](//packagist.org/packages/yii-extension/user)
[![Total Downloads](https://img.shields.io/packagist/dt/yii-extension/user)](https://packagist.org/packages/yii-extension/user)
