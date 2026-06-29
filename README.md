# CUSIP

[![Latest Stable Version](https://poser.pugx.org/dprmc/cusip/v/stable)](https://packagist.org/packages/dprmc/cusip)
[![Build Status](https://travis-ci.org/DPRMC/CUSIP.svg?branch=master)](https://travis-ci.org/DPRMC/CUSIP)
[![License](https://poser.pugx.org/dprmc/cusip/license)](https://packagist.org/packages/dprmc/cusip)
[![Total Downloads](https://poser.pugx.org/dprmc/cusip/downloads)](https://packagist.org/packages/dprmc/cusip)
[![Coverage Status](https://coveralls.io/repos/github/DPRMC/CUSIP/badge.svg?branch=master)](https://coveralls.io/github/DPRMC/CUSIP?branch=master)

A PHP library for validating and working with financial security identifiers.

This package validates:

- CUSIP identifiers
- ISIN identifiers
- SEDOL identifiers

It also includes helpers for extracting valid CUSIPs from pasted text, removing duplicates, identifying symbol types, calculating CUSIP check digits, and fixing a narrow class of common CUSIP transcription errors.

## Requirements

- PHP 7.4 or newer
- Composer

The current source uses typed properties, so PHP 7.4+ is required.

## Installation

```bash
composer require dprmc/cusip
```

## Quick Start

```php
<?php

use DPRMC\CUSIP;

require __DIR__ . '/vendor/autoload.php';

CUSIP::isCUSIP('222386AA2');      // true
CUSIP::isCUSIP('notacusip');      // false
CUSIP::isISIN('US9311421039');    // true
CUSIP::isSEDOL('7TT6737');        // true
```

## CUSIP Validation

Use `CUSIP::isCUSIP()` to validate a single CUSIP.

```php
use DPRMC\CUSIP;

$isValid = CUSIP::isCUSIP('222386AA2');
```

`isCUSIP()` returns `true` only when the input:

- is not empty
- is exactly 9 characters after trimming surrounding whitespace
- contains only valid CUSIP characters
- has a valid check digit
- is not in the package's explicit ignore list

Surrounding whitespace is ignored:

```php
CUSIP::isCUSIP(' 222386AA2 '); // true
```

The special CUSIP characters `*`, `@`, and `#` are supported:

```php
CUSIP::isCUSIP('00800*AA0'); // true
CUSIP::isCUSIP('00800@AA8'); // true
CUSIP::isCUSIP('00800#AA6'); // true
```

Invalid check digits return `false`:

```php
CUSIP::isCUSIP('31397JYY5'); // false
```

## Extracting CUSIPs From Text

Use `getValidCusipsFromString()` when users paste a list of CUSIPs into a textarea or form field.

```php
use DPRMC\CUSIP;

$input = "
3137A96Y7,
3136A45X3

31397NCJ2,
31397JYY4
";

$cusips = CUSIP::getValidCusipsFromString($input);

// [
//     '3137A96Y7',
//     '3136A45X3',
//     '31397NCJ2',
//     '31397JYY4',
// ]
```

The parser splits on whitespace and commas, ignores empty values, and removes invalid CUSIPs.

Use `getUniqueValidCusipsFromString()` when duplicate valid CUSIPs should be collapsed:

```php
$input = "
3137A96Y7,
31397NCJ2
31397NCJ2
3137A96Y7
";

$cusips = CUSIP::getUniqueValidCusipsFromString($input);

// [
//     '3137A96Y7',
//     '31397NCJ2',
// ]
```

## Removing Invalid CUSIPs From an Array

Use `removeInvalidCusips()` when you already have an array of candidate values.

```php
$cusips = CUSIP::removeInvalidCusips([
    '222386AA2',
    'notValidCusip',
    '31397JYY5',
]);

// [
//     '222386AA2',
// ]
```

## CUSIP Check Digits

Use `getChecksumDigit()` to calculate the check digit for an 8- or 9-character CUSIP value.

```php
$checkDigit = CUSIP::getChecksumDigit('222386AA');

// 2
```

If the input length is shorter than 8 characters or longer than 9 characters, the method returns `false`.

```php
CUSIP::getChecksumDigit('12345678987654321'); // false
```

## Fixing Common CUSIP Transcription Errors

CUSIP identifiers do not use the letters `I` and `O` because they are easy to confuse with `1` and `0`.

Use `fixCusip()` to normalize a CUSIP and attempt that replacement:

```php
use DPRMC\CUSIP;
use DPRMC\UnfixableCusipException;

try {
    $cusip = CUSIP::fixCusip('61765XAYO');

    // Returns a valid CUSIP if replacing I/O with 1/0 fixes the value.
} catch (UnfixableCusipException $exception) {
    $originalValue = $exception->unfixableCusip;
}
```

`fixCusip()`:

- trims surrounding whitespace
- converts the value to uppercase
- returns the value unchanged if it is already valid
- replaces `I` with `1` and `O` with `0`
- throws `DPRMC\UnfixableCusipException` if the result is still invalid

## ISIN Validation

Use `isISIN()` to validate an International Securities Identification Number.

```php
CUSIP::isISIN('US9311421039'); // true
CUSIP::isISIN('US9311421038'); // false
```

The method validates the ISIN format and checksum.

## SEDOL Validation

Use `isSEDOL()` to validate a Stock Exchange Daily Official List identifier.

```php
CUSIP::isSEDOL('7TT6737'); // true
CUSIP::isSEDOL('CCCBDD4'); // false
```

The method validates the SEDOL length, character rules, and checksum.

## Detecting Symbol Type

Use `getSymbolType()` when an input may be a CUSIP, ISIN, or SEDOL.

```php
use DPRMC\CUSIP;
use DPRMC\UnknownSymbolException;

try {
    $type = CUSIP::getSymbolType('US9311421039');

    // 'CUSIP', 'ISIN', or 'SEDOL'
} catch (UnknownSymbolException $exception) {
    $unknownValue = $exception->unknownSymbol;
}
```

`getSymbolType()` checks the value in this order:

1. CUSIP
2. ISIN
3. SEDOL

If the symbol cannot be identified, it throws `DPRMC\UnknownSymbolException`.

## Ignored CUSIPs

The package maintains a small list of CUSIPs that are structurally valid but should be treated as invalid by this library:

```php
DPRMC\CUSIP::$cusipsToIgnore;
```

Current ignored values:

- `240000000`
- `111322608`

These values pass the checksum algorithm but are known to create false positives in downstream parsers.

## API Reference

### `CUSIP::isCUSIP($cusip = null): bool`

Returns whether a value is a valid CUSIP.

### `CUSIP::getValidCusipsFromString(string $string): array`

Splits a string on whitespace and commas, then returns only valid CUSIPs.

### `CUSIP::getUniqueValidCusipsFromString($string): array`

Returns valid CUSIPs from a string with duplicates removed and indexes reset.

### `CUSIP::removeInvalidCusips($cusips): array`

Filters an array of candidate values down to valid CUSIPs.

### `CUSIP::containsInvalidCharacters($cusip): bool`

Returns `true` when a value contains characters that are not valid in a CUSIP.

### `CUSIP::getChecksumDigit($cusip): int|false`

Calculates a CUSIP check digit. Returns `false` when the input length is invalid.

### `CUSIP::isISIN($isin): bool`

Returns whether a value is a valid ISIN.

### `CUSIP::isSEDOL($sedol): bool`

Returns whether a value is a valid SEDOL.

### `CUSIP::fixCusip(string $originalCUSIP): string`

Returns a valid normalized CUSIP or throws `DPRMC\UnfixableCusipException`.

### `CUSIP::getSymbolType(string $symbol): string`

Returns `CUSIP`, `ISIN`, or `SEDOL`. Throws `DPRMC\UnknownSymbolException` when the value is not recognized.

## Exceptions

### `DPRMC\UnfixableCusipException`

Thrown by `fixCusip()` when a value cannot be converted into a valid CUSIP.

The original value is available on:

```php
$exception->unfixableCusip;
```

### `DPRMC\UnknownSymbolException`

Thrown by `getSymbolType()` when a value is not a valid CUSIP, ISIN, or SEDOL.

The original value is available on:

```php
$exception->unknownSymbol;
```

## Development

Install dependencies:

```bash
composer install
```

Run the test suite:

```bash
vendor/bin/phpunit
```

Regenerate Composer's autoloader after adding or renaming classes:

```bash
composer dump-autoload
```

## Background

CUSIP stands for Committee on Uniform Securities Identification Procedures. CUSIPs are commonly used to identify North American financial securities.

More background is available on Wikipedia:

https://en.wikipedia.org/wiki/CUSIP

## License

This package is released under the MIT License. See [LICENSE](LICENSE).
