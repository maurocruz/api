# Plinct Api

Php api for schema.org modelling

**UNDER DEVELOPMENT**

## Depedencies
- composer
- php7+
- slim/slim 4.0
- slim/psr7 0.4+

## Install dependencies

  ```composer require slim/slim:"4.*"```

  ```composer require slim/psr7```

  ```composer require plinct/api```

## Getting Start

> Create mysql schema;

> Create two user on mysql schema: one public user and one admin user;

> Grant insert privilegies for the public user in only the user table and grant all privilegies for the admin user;

> GRANT INSERT ON schema.user TO 'publicUser'@'hostname' IDENTIFIED BY 'password';


Add in index page on the root
 
``` 
<?php
declare(strict_types=1);

use \Slim\Factory\AppFactory;
use Plinct\Api\PlinctApiFactory;

// autoload
include '../vendor/autoload.php';

// slim app
$slimApp = AppFactory::create();

// for enable routes PUT and DELETE
$slimApp->addBodyParsingMiddleware();

/******** PLINCT API ***********/
// api factory
$api = PlinctApiFactory::create($slimApp);
// database connect
$api->connect("driver", "host", "dbname", "username", "password");

$api->run();

// run
$slimApp->run();
```

> Start api using https://domain/api/start with request HTTP POST request, sending the database admin username and password by form url encoded using <username> and <password> with name of values;

> Register a user with <name>, <email> and <password> on https://<domain>/api/user sending with request HTTP POST from a form url encoded;

> Update in table user the user for administrator with status = 1


## Types enabled

- Action
- Article
- Book
- ContactPoint
- Event
- ImageObject
- LocalBusiness
- Organization
- Person
- Place
- PostalAddress
- PropertyValue
- Taxon
- VideoObject
- WebPage

