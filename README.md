# Plinct Api

Interface for manipulating data in an SQL database modeled according to schema.org standards.

**\### UNDER DEVELOPMENT \###**

## Depedencies
- composer
- php 8.2
- slim/slim 4
- slim/psr7
- php-di/slim-bridge
- vlucas/phpdotenv

## Getting Start

For install in your website

> Create mysql schema;

> Create two user on mysql schema: one public user and one admin user;

> Grant insert privilegies for the public user in only the user table and grant all privilegies for the admin user;

> GRANT INSERT ON schema.user TO 'publicUser'@'hostname' IDENTIFIED BY 'password';


Create index.php and save in /www/api 
 
``` 
<?php
/**
 *   PLINCT API
 */
include '../../vendor/autoload.php';

use Dotenv\Dotenv;
use Plinct\Api\ApiFactory;

// Carrega as variáveis do arquivo .env
$dotenv = Dotenv::createImmutable(__DIR__.'/../../');
$dotenv->load();

$settings = [
  'development' => [true|false],
  'host' => 'https://[your domain]/',
];


/******* PLINCT *********/
// API
try {
  $api = ApiFactory::create($settings);
  $api->run();
} catch (Exception $e) {
  print '<pre>';
    var_dump($e);
  print '</pre>';
}

```

> Start api using https://domain/api/start with request HTTP POST request, sending the database admin username and password by form url encoded using <username> and <password> with name of values;

> Register a user with <name>, <email> and <password> on https://<domain>/api/user sending with request HTTP POST from a form url encoded;

> Update in table user the user for administrator with status = 1


## Types enabled

- Action
- Article
- Book
- Certification
- Collection
- ContactPoint
- Event
- ImageObject
- LocalBusiness
- MediaObject
- Order
- Organization
- Person
- Place
- PostalAddress
- Product
- PropertyValue
- Review
- Service
- Taxon
- Trip
- VideoObject
- WebSite

## Api access

> Use https://yourdomain.dpn/api with base url

> Get all items for a type
>``` 
> Https://yourdomain.dpn/api/[typename]
>```

> Get type and subClass of type with additionalType properties
> ```
> https://yourdomain.dpn/api/[type]?format=classHierarchy&subClass=[additionalType]
>```

### properties from use in query strings

> properties
> > Use properties from type. E.g: https://yourdomain.dpn/api/[type]?properties=[property1,property]
 


