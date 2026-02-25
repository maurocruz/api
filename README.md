# Plinct Api

Interface for manipulating data in an SQL database modeled according to schema.org standards.

**\### UNDER DEVELOPMENT \###**

## Depedencies
- composer
- php 7.4 | 8.0
- slim/slim 4
- slim/psr7

## Getting Start

For install in your website

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
- Product
- PropertyValue
- Service
- Taxon
- VideoObject
- WebSite
- WebPage
- WebPageElement

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

> format=
> >   ItemList: 
> >   - return a ItemList type with **numberOfItems** and **ItemListElement** properties
> >
> > classHierarchy
> >   - return data with type, class (subClass of type) and subClass (subClasses of subClass)
> 


