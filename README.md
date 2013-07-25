# DomainSpecificQuery

Build and define queries specific to the application domain and compile them into other formats.

# Warning!

Library is not completed yet and it is in early development stage

[![Build Status](https://secure.travis-ci.org/nicmart/DomainSpecificQuery.png?branch=master)](http://travis-ci.org/nicmart/DomainSpecificQuery)

## Install

The best way to install DomainSpecificQuery is [through composer](http://getcomposer.org).

Just create a composer.json file for your project:

```JSON
{
    "require": {
        "comperio/domain-specific-query": "dev-master"
    }
}
```

Then you can run these two commands to install it:

    $ curl -s http://getcomposer.org/installer | php
    $ php composer.phar install

or simply run `composer install` if you have have already [installed the composer globally](http://getcomposer.org/doc/00-intro.md#globally).

Then you can include the autoloader, and you will have access to the library classes:

```php
<?php
require 'vendor/autoload.php';
```