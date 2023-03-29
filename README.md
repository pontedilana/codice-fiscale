CodiceFiscale
==============

A library to calculate and check the validity of the italian fiscal code (codice fiscale).

[![Latest Stable Version](https://poser.pugx.org/usu/codice-fiscale/v/stable.svg)](https://packagist.org/packages/fattureincloud/codice-fiscale) [![Total Downloads](https://poser.pugx.org/usu/codice-fiscale/downloads.svg)](https://packagist.org/packages/fattureincloud/codice-fiscale) [![License](https://poser.pugx.org/usu/codice-fiscale/license.svg)](https://packagist.org/packages/fattureincloud/codice-fiscale)

Requirements
------------

- php >= 5.4

Installation
------------

Add the library with the following command

``` bash
composer require fattureincloud/codice-fiscale
```

How to use
----------

``` php
<?php
require_once __DIR__ . '/vendor/autoload.php';

use CodiceFiscale\Calculator;
use CodiceFiscale\Checker;

$calc = new Calculator();
$calc->calcola('Nome', 'Cognome', 'M', new \DateTime('1992-03-06'), 'F205');

$chk = new Checker();
if ($chk->isFormallyCorrect('RSSMRA79S18F205J')) {
    print('Codice Fiscale formally correct');
    printf('Birth Day: %s',     $chk->getDayBirth());
    printf('Birth Month: %s',   $chk->getMonthBirth());
    printf('Birth Year: %s',    $chk->getYearBirth());
    printf('Birth Country: %s', $chk->getCountryBirth());
    printf('Sex: %s',           $chk->getSex());
} else {
    print('Codice Fiscale wrong');
}
```

Testing
-------

The library is fully tested with PHPUnit.

Go to the root folder, install the dev dependencies with composer, and then run the phpunit test suite

``` bash
$ composer --dev install
$ ./vendor/bin/phpunit
```
