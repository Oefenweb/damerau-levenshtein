# Damerau Levenshtein

[![CI](https://github.com/Oefenweb/damerau-levenshtein/workflows/CI/badge.svg)](https://github.com/Oefenweb/damerau-levenshtein/actions?query=workflow%3ACI)
[![PHP 7 ready](http://php7ready.timesplinter.ch/Oefenweb/damerau-levenshtein/badge.svg)](https://travis-ci.org/Oefenweb/damerau-levenshtein)
[![codecov](https://codecov.io/gh/Oefenweb/damerau-levenshtein/branch/master/graph/badge.svg)](https://codecov.io/gh/Oefenweb/damerau-levenshtein)
[![Packagist downloads](http://img.shields.io/packagist/dt/Oefenweb/damerau-levenshtein.svg)](https://packagist.org/packages/oefenweb/damerau-levenshtein)
[![Code Climate](https://codeclimate.com/github/Oefenweb/damerau-levenshtein/badges/gpa.svg)](https://codeclimate.com/github/Oefenweb/damerau-levenshtein)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Oefenweb/damerau-levenshtein/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Oefenweb/damerau-levenshtein/?branch=master)

Get text similarity level with Damerau-Levenshtein distance.

## Requirements

* PHP 7.1.0 or greater.

## Installation

`composer require oefenweb/damerau-levenshtein`

## Usage

```php
$pattern = 'foo bar';
$string  = 'fuu baz';

$damerauLevenshtein = new DamerauLevenshtein($pattern, $string);

$damerauLevenshtein->getSimilarity(); // absolute edit distance; == 3

$damerauLevenshtein->getRelativeDistance(); // relative edit distance; == 0.57142857142857

$damerauLevenshtein->getMatrix(); // get complete distance matrix
/* ==
 * [
 *   [0,1,2,3,4,5,6,7],
 *   [1,0,1,2,3,4,5,6],
 *   [2,1,1,2,3,4,5,6],
 *   [3,2,2,2,3,4,5,6],
 *   [4,3,3,3,2,3,4,5],
 *   [5,4,4,4,3,2,3,4],
 *   [6,5,5,5,4,3,2,3],
 *   [7,6,6,6,5,4,3,3],
 * ]
 */

$damerauLevenshtein->displayMatrix(); // get readable and formatted distance matrix
/*
 *   '  foo bar' . PHP_EOL
 * . ' 01234567' . PHP_EOL
 * . 'f10123456' . PHP_EOL
 * . 'u21123456' . PHP_EOL
 * . 'u32223456' . PHP_EOL
 * . ' 43332345' . PHP_EOL
 * . 'b54443234' . PHP_EOL
 * . 'a65554323' . PHP_EOL
 * . 'z76665433'
 */
```

Different costs are supported by the constructor and getters / setters.

Character comparison (equal check) can easily be overridden by parent class (see `DamerauLevenshtein::compare`).

For more examples look at `/tests/DamerauLevenshteinTest.php` or **RTFC**.

## License

MIT

#### Author Information

Mischa ter Smitten (based on work of [Ph4r05](http://www.phpclasses.org/package/7021-PHP-Get-text-similarity-level-with-Damerau-Levenshtein.html))
