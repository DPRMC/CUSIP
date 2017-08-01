# CUSIP
[![Latest Stable Version](https://poser.pugx.org/dprmc/cusip/v/stable)](https://packagist.org/packages/dprmc/cusip) [![Build Status](https://travis-ci.org/DPRMC/CUSIP.svg?branch=master)](https://travis-ci.org/DPRMC/CUSIP)   [![License](https://poser.pugx.org/dprmc/cusip/license)](https://packagist.org/packages/dprmc/cusip)    [![Total Downloads](https://poser.pugx.org/dprmc/cusip/downloads)](https://packagist.org/packages/dprmc/cusip) [![Coverage Status](https://coveralls.io/repos/github/DPRMC/CUSIP/badge.svg?branch=master)](https://coveralls.io/github/DPRMC/CUSIP?branch=master)

## Installation
```
composer require dprmc/cusip
```

## Usage

```php
use \DPRMC\CUSIP;
$isCusip = CUSIP::isCUSIP('notacusip'); // false
$isCusip = CUSIP::isCUSIP('222386AA2'); // true
```
## Notes
A PHP (v5.6+) library for validating CUSIP codes.

And in case you were wondering what a CUSIP was:

https://en.wikipedia.org/wiki/CUSIP
