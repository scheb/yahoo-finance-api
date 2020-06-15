Upgrade
=======

## From 3.x to 4.x

Minimum required PHP version is now 7.1.3.

The library now has PHP7.1 type-hints and `declare(strict_types=1)` everywhere. Please make sure the values
passed to the library match the type.

## From 2.x to 3.x

Since Yahoo Finance YQL API stopped working in November 2017, this client switched to a different endpoint.
Because of that the `Quote` entity changed according to the fields available from the new API endpoint.
Also, exchange rates are now represented as `Quote` entities.
