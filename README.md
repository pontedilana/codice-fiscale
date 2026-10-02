CodiceFiscale
==============

A library to calculate and check the validity of the Italian fiscal code (codice fiscale).
Based on the original work of andreausu, with the contribution of fdisotto.

[![Latest Stable Version](https://poser.pugx.org/pontedilana/codice-fiscale/v/stable.svg)](https://packagist.org/packages/pontedilana/codice-fiscale) [![Total Downloads](https://poser.pugx.org/pontedilana/codice-fiscale/downloads.svg)](https://packagist.org/packages/pontedilana/codice-fiscale) [![License](https://poser.pugx.org/pontedilana/codice-fiscale/license.svg)](https://packagist.org/packages/pontedilana/codice-fiscale)

Requirements
------------

- php >= 8.1
- ext-intl

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
    printf('Codice Fiscale wrong: %s', $chk->getError());
}
```

`Calculator::calcola()` throws `\InvalidArgumentException` when the sex is not `M` or `F`
(case-insensitive) or the codice comune is not a letter followed by three digits.
The codice comune is trimmed and uppercased before use.

`Checker::getError()` returns one of these messages after a failed check:

| Message                | Cause                                                         |
|------------------------|---------------------------------------------------------------|
| `Empty code`           | the code is empty                                             |
| `Length error`         | the code is not 16 characters long                            |
| `Code with wrong char` | the code does not match the expected pattern                  |
| `Wrong code`           | the control character is wrong                                |
| `Wrong birth date`     | the encoded birth date does not exist (e.g. day 00, 31 February) |

The century is not encoded in the fiscal code, so 29 February is accepted for every year divisible by 4.

Upgrading from 2.x
------------------

- `Calculator::calcola()` now throws `\InvalidArgumentException` for an invalid sex or codice comune.
  In 2.x it returned a wrong code (any sex other than `F` was treated as male, a lowercase codice comune produced a wrong control character).
- `Checker::isFormallyCorrect()` now returns `false`, with the error `Wrong birth date`, for codes that encode an impossible birth date.
  In 2.x these codes were accepted.

Testing
-------

The library is fully tested with PHPUnit.

Go to the root folder, install the dev dependencies with composer, and then run the phpunit test suite

``` bash
$ composer install
$ ./vendor/bin/phpunit
```
