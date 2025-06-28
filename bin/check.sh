#!/bin/bash
php-cs-fixer fix
./vendor/bin/phpcs --standard=php_cs.xml src tests
./vendor/bin/psalm
./vendor/bin/phpunit
