# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project

`pontedilana/codice-fiscale`: a dependency-free PHP library (PHP >= 8.1, `ext-intl` required) that calculates and validates the Italian fiscal code (codice fiscale). Published on Packagist; the public API is `CodiceFiscale\Calculator` and `CodiceFiscale\Checker`, so treat method signatures and getter return formats as a BC contract.

## Commands

```bash
composer install                                   # dev dependencies
./vendor/bin/phpunit                               # full suite (stopOnFailure=true)
./vendor/bin/phpunit --filter testCalcoloCodiceFiscale test/CalculatorTest.php   # single test
./vendor/bin/phpunit --filter 'testCalcoloCodiceFiscale@andrea usuelli'   # single data-provider case by name
./vendor/bin/phpstan analyse                       # level max + strict rules + bleedingEdge, on src/ and test/
./vendor/bin/php-cs-fixer fix                      # PER-CS2.0 (+ risky)
./vendor/bin/rector process --dry-run              # PHP 8.1 set, PHPUnit 10 set, full type coverage
```

Verification gate before committing: phpunit, phpstan, php-cs-fixer, rector dry-run. CI (`.github/workflows/test.yml`) runs phpunit on PHP 8.1 to 8.5 (plus lowest dependencies on 8.1) and the static checks on PHP 8.1, on pushes to `main`, `develop`, `release/**`, `hotfix/**` and on PRs to `main` and `develop`.

A gitignored local `phpstan.neon` (includes `phpstan.dist.neon`) takes precedence when present. `composer.lock` is gitignored too.

phpunit writes coverage and testdox reports to `test/output/` (needs Xdebug or PCOV for coverage).

## Architecture

Two independent classes in `src/CodiceFiscale/` (PSR-0 autoload), sharing no code:

- **`Calculator::calcola()`**: builds the 16-char code from name, surname, sex, `DateTime` and the cadastral municipality code (e.g. `F205`). Inputs go through `sanitizeString()` (ICU `transliterator_transliterate` to ASCII, strip non-alphanumerics, uppercase), so accented and non-Latin names are supported. Surname takes the first 3 consonants; name takes consonants 1, 3, 4 when there are more than 3. Both pad with vowels then `X`. Women get day + 40. The check character uses `matriceCodiceControllo`, keyed by `"<index in 0-9A-Z><1 if odd position else 0>"`. Invalid sex (not M/F) or municipality code (not letter + 3 digits) throws `\InvalidArgumentException`.
- **`Checker::isFormallyCorrect()`**: stateful. It resets its properties, then runs sequential checks (empty, length, regex, checksum, birth date). The regex already limits the omocodia positions 6, 7, 9, 10, 12, 13, 14 to digits and `LMNPQRSTUV`, so error 3 is kept in the list only for compatibility. The birth date check decodes omocodia first and uses `checkdate()` on year 20yy because the century is not encoded. Each failure throws an internal `\Exception` that is caught in the same method and stored as the `getError()` message; the method never throws to the caller. On success it decodes omocodia letters back to digits and fills the getters (`getSex`, `getDayBirth` with leading zero and the female +40 removed, `getMonthBirth` as `01`-`12`, `getYearBirth` as 2 digits, `getCountryBirth`). Getters return `null` after a failed check.

The two classes encode the checksum differently (`Calculator` uses one combined matrix, `Checker` uses separate odd/even weight tables): a fix to the algorithm must be applied to both.

## Tests

`test/` files have no namespace and load `vendor/autoload.php` themselves (there is no `autoload-dev`). Tests use PHPUnit 10 attributes (`#[CoversClass]`, `#[DataProvider]`) with named data-provider cases; add new cases to the existing providers rather than new test methods. Comments and identifiers in the code mix Italian and English; keep the surrounding style.
