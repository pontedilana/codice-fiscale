CodiceFiscale
==============

A library to calculate and check the validity of the Italian fiscal code (codice fiscale).
Based on the original work of andreausu, with the contribution of fdisotto.

[![Latest Stable Version](https://poser.pugx.org/pontedilana/codice-fiscale/v/stable.svg)](https://packagist.org/packages/pontedilana/codice-fiscale) [![Total Downloads](https://poser.pugx.org/pontedilana/codice-fiscale/downloads.svg)](https://packagist.org/packages/pontedilana/codice-fiscale) [![License](https://poser.pugx.org/pontedilana/codice-fiscale/license.svg)](https://packagist.org/packages/pontedilana/codice-fiscale)

Requirements
------------

- php >= 8.1

Installation
------------

Add the library with the following command

``` bash
composer require pontedilana/codice-fiscale
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
