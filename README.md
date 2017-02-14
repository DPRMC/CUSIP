# CUSIP
[![Latest Stable Version](https://poser.pugx.org/dprmc/cusip/v/stable)](https://packagist.org/packages/dprmc/cusip) [![Build Status](https://travis-ci.org/DPRMC/CUSIP.svg?branch=master)](https://travis-ci.org/DPRMC/CUSIP)   [![License](https://poser.pugx.org/dprmc/cusip/license)](https://packagist.org/packages/dprmc/cusip)    [![Total Downloads](https://poser.pugx.org/dprmc/cusip/downloads)](https://packagist.org/packages/dprmc/cusip)

```
composer require dprmc/cusip
```

#### Usage

```
use \DPRMC\CUSIP;
$isCusip = CUSIP::isCUSIP('notacusip'); // false
$isCusip = CUSIP::isCUSIP('222386AA2'); // true
```

A php library for validating CUSIP codes.
