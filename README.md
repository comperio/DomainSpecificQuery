# DomainSpecificQuery

Build and define queries specific to the application domain and compile them into other formats.

# Warning!

The library is not completed yet and it is in early development stage

[![Build Status](https://travis-ci.org/comperio/DomainSpecificQuery.png?branch=master)](https://travis-ci.org/comperio/DomainSpecificQuery)
[![Coverage Status](https://coveralls.io/repos/comperio/DomainSpecificQuery/badge.png?branch=master)](https://coveralls.io/r/comperio/DomainSpecificQuery?branch=master)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/comperio/DomainSpecificQuery/badges/quality-score.png?s=af8900c6d4649fb5c44d3c7dffd431bf546550ad)](https://scrutinizer-ci.com/g/comperio/DomainSpecificQuery/)
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