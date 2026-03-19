# MyAdmin NoVNC Plugin

[![Build Status](https://github.com/detain/myadmin-novnc-plugin/actions/workflows/tests.yml/badge.svg)](https://github.com/detain/myadmin-novnc-plugin/actions)
[![Latest Stable Version](https://poser.pugx.org/detain/myadmin-novnc-plugin/version)](https://packagist.org/packages/detain/myadmin-novnc-plugin)
[![Total Downloads](https://poser.pugx.org/detain/myadmin-novnc-plugin/downloads)](https://packagist.org/packages/detain/myadmin-novnc-plugin)
[![License](https://poser.pugx.org/detain/myadmin-novnc-plugin/license)](https://packagist.org/packages/detain/myadmin-novnc-plugin)

A plugin for the [MyAdmin](https://github.com/detain/myadmin) control panel framework that provides NoVNC HTML5 VNC connection handling. This plugin integrates browser-based VNC access into MyAdmin, enabling remote desktop connectivity through an HTML5-compatible interface without requiring standalone VNC client software.

## Features

- HTML5-based VNC connections via NoVNC
- Seamless integration with the MyAdmin plugin system
- Event-driven architecture using Symfony EventDispatcher
- Admin menu and settings integration

## Requirements

- PHP 8.2 or higher
- ext-soap
- Symfony EventDispatcher 5.x, 6.x, or 7.x

## Installation

Install via Composer:

```sh
composer require detain/myadmin-novnc-plugin
```

## Usage

The plugin registers itself automatically through the MyAdmin plugin loader system. It hooks into the event dispatcher to provide menu entries and configuration settings for NoVNC connections.

## Testing

Run the test suite with PHPUnit:

```sh
composer install
vendor/bin/phpunit
```

## License

This package is licensed under the [LGPL-2.1](LICENSE) license.
