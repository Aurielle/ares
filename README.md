[ABANDONED] Ares
====

[![Build Status](https://travis-ci.org/Aurielle/ares.png?branch=master)](https://travis-ci.org/Aurielle/ares)


Installation to project
-----------------------
The best way to install Aurielle/ares is using Composer:
```sh
$ composer require aurielle/ares
```


Download information about customer via his IN.

Example
-------
```php
$ares = new \Aurielle\Ares\Ares();
$ares->loadData('87744473'); // return object \Aurielle\Ares\Data
```
